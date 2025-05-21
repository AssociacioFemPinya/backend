<?php

namespace App\Http\Controllers;

use App\Colla;
use App\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    /***/
    public function getList(): View
    {

        $user = $this->user();

        if (! $user->hasRole('Super-Admin')) {
            abort(404);
        }

        $data_content['users'] = User::query()->get();
        $colla = Colla::getCurrent();
        if ($user->getCollaId() !== $colla->getId()) {
            abort(404);
        }
        if ($user->hasRole('Super-Admin')) {
            $roles = Role::all()->toArray();
        } else {
            $roles = Role::whereNotIn('name', ['Super-Admin'])->get()->toArray();
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

        return view('admin.users.list', $data_content);

    }
}
