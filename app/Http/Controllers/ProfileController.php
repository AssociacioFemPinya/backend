<?php

namespace App\Http\Controllers;

use App\Colla;
use App\Helpers\RenderHelper;
use App\Managers\CollesManager;
use App\Managers\UsersManager;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Intervention\Image\ImageManagerStatic as Image;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProfileController extends Controller
{
    /**
     * Edit Profile User
     */
    public function getSetupUser(): View
    {

        $user = $this->user();

        if (! $user->can('view events') && ! $user->can('edit events')) {
            abort(404);
        }

        if ($user->hasRole('Super-Admin')) {
            $data_content['colles'] = Colla::query()->select('id_colla', 'name')->orderBy('name')->get();
        }

        $data_content['user'] = $user;
        $data_content['colla'] = Colla::getCurrent();

        return view('profile.user', $data_content);

    }

    /**Update user profile.*/
    public function postUpdateUserProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255|min:3',
            'language' => 'required|size:2',
            'photo' => 'sometimes|file|image|mimes:jpeg,png|max:10240',
        ]);

        $user = $this->user();

        if ($user->hasRole('Super-Admin')) {
            $user->setAttribute('colla_id', $request->input('select_colla'));
        }

        $user->setAttribute('name', $request->input('name'));
        $user->setAttribute('language', $request->input('language'));

        $user->save();

        $imageSizes = [
            'xs' => 32,
            'med' => 128,
        ];

        //exist file, upload
        if ($request->file('photo')) {

            if ($user->getPhoto()) {

                $path = public_path('media/users').'/'.$user->getPhoto();

                foreach ($imageSizes as $size => $width) {
                    $filePath = $path.'-'.$size.'.png';
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

            }

            $random_str = Str::random(10);
            $image_input = $request->file('photo');

            $photo_name = $user->getId().'_'.$random_str;

            $imagePath = public_path('media/users/').$photo_name;

            foreach ($imageSizes as $size => $width) {
                $image = Image::make($image_input);
                $image->fit($width);
                $image->encode('png');
                $image->save($imagePath.'-'.$size.'.png');
            }

            $user->setAttribute('photo', $photo_name);
            $user->save();
        }

        Session::flash('status_ok', trans('user.user_updated'));

        return redirect()->route('profile.user');
    }

    /** update user password*/
    public function postUpdateUserPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm_password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
            'new_password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
        ]);

        $user = $this->user();

        if (! Hash::check($request->input('password'), $user->getPassword())) {
            Session::flash('status_ko', trans('user.user_password_not_correct'));

            return redirect(route('profile.user'));
        }

        if ($request->input('new_password') === $request->input('confirm_password')) {
            $user->setPassword($request->input('new_password'));

            Session::flash('status_ok', trans('user.user_password_updated'));

            return redirect()->route('profile.user');
        }

        Session::flash('status_ko', trans('user.user_password_not_equals'));

        return redirect()->route('profile.user');

    }

    public function postAddUser(UsersManager $usersManager, Request $request): RedirectResponse|View
    {

        $user = $this->user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $request->validate([
            'email' => 'required|email:rfc|unique:users,email',
            'name' => 'required|max:255|min:3',
            'language' => 'required|size:2',
            'confirm_password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->symbols()
                    ->numbers(),
            ],
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->symbols()
                    ->numbers(),
            ],
        ]);

        if ($request->input('password') !== $request->input('confirm_password')) {
            if (! $user->hasPermissionTo('view colla')) {
                abort(404);
            }

            $data_content['colla'] = $colla;
            $data_content['users'] = User::query()
                ->where('colla_id', $colla->getId())
                ->where('role', '!=', 'ADMIN')
                ->get();
            $data_content['active'] = RenderHelper::profileActiveTab('users');

            Session::flash('status_ko', trans('user.user_password_not_equals'));

            return view('profile.colla', $data_content);
        }

        $bag = new ParameterBag($request->except('_token'));
        $usersManager->createUser($colla, $bag);

        $data_content['active'] = RenderHelper::profileActiveTab('users');

        Session::flash('status_ok', trans('user.user_added'));

        return redirect()->route('profile.colla', $data_content);
    }

    /** Show the form for editing the specified resource.*/
    public function getEditUserModalAjax(User $user): View
    {

        $realUser = $this->user();

        if (! $realUser->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();
        if ($realUser->getCollaId() !== $colla->getId()) {
            abort(404);
        }
        if ($realUser->hasRole('Super-Admin')) {
            $roles = Role::all()->toArray();
        } elseif ($realUser->hasRole('Colla-Admin')) {
            $roles = Role::whereNotIn('name', ['Super-Admin'])->get()->toArray();
        } else {
            $roles = Role::whereNotIn('name', ['Super-Admin', 'Colla-Admin'])->get()->toArray();
        }
        $userRoles = $user->roles->pluck('id');
        $permissionsView = Permission::where('name', 'like', '%view%')->get();
        $permissionsEdit = Permission::where('name', 'like', '%edit%')->get();

        $data_content['user'] = $user;
        $data_content['roles'] = $roles;
        $data_content['userRoles'] = $userRoles->toArray();
        $data_content['permissionsView'] = $permissionsView;
        $data_content['permissionsEdit'] = $permissionsEdit;
        $data_content['existingPermissions'] = $user->getAllExistingPermissions();

        return view('profile.modal-update-user', $data_content);
    }

    /** Show the form for editing the specified resource.*/
    public function getAddUserModalAjax(): View
    {

        $user = $this->user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        if ($user->getCollaId() !== $colla->getId()) {
            abort(404);
        }
        if ($user->hasRole('Super-Admin')) {
            $roles = Role::all()->toArray();
        } elseif ($user->hasRole('Colla-Admin')) {
            $roles = Role::whereNotIn('name', ['Super-Admin'])->get()->toArray();
        } else {
            $roles = Role::whereNotIn('name', ['Super-Admin', 'Colla-Admin'])->get()->toArray();
        }
        $permissionsView = Permission::where('name', 'like', '%view%')->get();
        $permissionsEdit = Permission::where('name', 'like', '%edit%')->get();

        $data_content['roles'] = $roles;
        $data_content['userRoles'] = [];
        $data_content['permissionsView'] = $permissionsView;
        $data_content['permissionsEdit'] = $permissionsEdit;
        $data_content['existingPermissions'] = $user->getAllExistingPermissions();

        return view('profile.modal-add-user', $data_content);
    }

    /** update user, profile and permissions*/
    public function postUpdateUser(UsersManager $usersManager, Request $request, User $user): RedirectResponse
    {

        $realUser = $this->user();

        if (! $realUser->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        if ($user->getCollaId() !== $colla->getId()) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|max:255|min:3',
            'language' => 'required|size:2',
        ]);

        $bag = new ParameterBag($request->except('_token'));
        //print_r($bag);die();
        $usersManager->updateUser($user, $bag);

        if (strpos($request->headers->get('referer'), 'admin/users')) {
            Session::flash('status_ok', trans('user.user_updated'));

            return redirect()->route('admin.users');
        }

        $data_content['active'] = RenderHelper::profileActiveTab('users');

        Session::flash('status_ok', trans('user.user_updated'));

        return redirect()->route('profile.colla', $data_content);
    }

    /* destroy user form colla profile*/
    public function postDestroyUser(UsersManager $usersManager, User $user): RedirectResponse
    {

        $realUser = $this->user();

        if (! $realUser->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        if ($user->getCollaId() !== $colla->getId()) {
            abort(404);
        }

        $usersManager->deleteUser($user);

        Session::flash('status_ok', trans('admin.user_destroyed'));

        return redirect()->route('profile.colla');
    }

    /** get colla for profile.*/
    public function getSetupColla(Request $request): View
    {

        $user = $this->user();

        if (! $user->can('view colla') && ! $user->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $data_content['colla'] = $colla;

        $data_content['users'] = User::query()
            ->where('colla_id', $colla->getId())
            ->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'Super-Admin');
            })
            ->get();

        $active = $request->get(
            'active',
            RenderHelper::profileActiveTab('profile')
        );
        $data_content['active'] = $active;

        $data_content['config'] = $colla->getConfig();
        $userRoles = $user->roles->pluck('id');
        $permissionsView = Permission::where('name', 'like', '%view%')->get();
        $permissionsEdit = Permission::where('name', 'like', '%edit%')->get();

        $data_content['user'] = $user;
        $data_content['userRoles'] = $userRoles->toArray();
        $data_content['permissionsView'] = $permissionsView;
        $data_content['permissionsEdit'] = $permissionsEdit;
        $data_content['existingPermissions'] = $user->getAllExistingPermissions();
        $data_content['periods'] = $colla->periods()->orderBy('name', 'asc')->get();

        return view('profile.colla', $data_content);

    }

    /** update colla form edit colla profile*/
    public function postUpdateColla(Request $request, CollesManager $collesManager): RedirectResponse
    {

        $user = $this->user();

        if (! $user->can('edit colla')) {
            abort(404);
        }

        $colla = Colla::getCurrent();

        $request->validate([
            'name' => 'required|max:50|min:3',
            'email' => 'required|email:rfc',
            'phone' => 'required|numeric',
            'country' => 'required|max:100|min:3',
            'city' => 'required|max:100|min:3',
            'logo' => 'sometimes|file|image|mimes:jpeg,png|max:10240',
            'banner' => 'sometimes|file|image|mimes:jpeg,png|max:10240',
        ]);

        $bag = new ParameterBag($request->except(['_token', 'max_members', 'shortname']));

        $collesManager->updateColla($colla, $bag);

        //exist file, upload
        if ($request->file('logo')) {
            //Has logo, destroy logo
            if ($colla->getLogo()) {
                $filePath = public_path('media/colles/'.$colla->getShortName()).'/'.$colla->getLogo();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $file = $request->file('logo');
            $file_name = Str::random(10).'.'.$request->logo->extension();
            $file->move(public_path('media/colles/'.$colla->getShortName()), $file_name);

            $colla->setAttribute('logo', $file_name);
            $colla->save();
        }

        //exist file, upload
        if ($request->file('banner')) {
            //Has banner, destroy banner
            if ($colla->getBanner()) {
                $filePath = public_path('media/colles/'.$colla->getShortName()).'/'.$colla->getBanner();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $file = $request->file('banner');
            $file_name = Str::random(10).'.'.$request->banner->extension();
            $file->move(public_path('media/colles/'.$colla->getShortName()), $file_name);

            $colla->setAttribute('banner', $file_name);
            $colla->save();
        }

        $data_content['active'] = RenderHelper::profileActiveTab('profile');

        Session::flash('status_ok', trans('admin.colla_updated'));

        return redirect()->route('profile.colla', $data_content);
    }
}
