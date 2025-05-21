<?php

namespace App;

use App\Helpers\Humans;
use App\Traits\TimeStampsGetterTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    use TimeStampsGetterTrait;

    protected $primaryKey = 'id_user';

    protected $fillable = ['type', 'name', 'email', 'password', 'colla_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_access_at' => 'datetime',
    ];

    /***/
    public function setPassword(string $password)
    {
        $this->attributes['password'] = Hash::make($password);
        $this->save();
    }

    /** get path profile image from user */
    public function getProfileImage(string $size = 'xs'): string
    {
        return $this->getPhotoUrl($size);
    }

    public function getPhotoUrl(string $size = 'xs'): string
    {
        if ($this->getPhoto()) {
            $size = ($size !== 'xs')
                ? strtolower($size)
                : $size;

            $path = '/media/users/';
            $photo = $this->getPhoto().'-'.$size.'.png';

            return asset($path.$photo);
        }

        return asset('media/avatars/avatar.jpg');
    }

    /** get array of permissions */
    public function getAllExistingPermissions(): array
    {
        // All permissions but the basic ones
        $existingPermissions = Permission::whereNotIn('name', ['dashboard', 'profile'])->get();

        $permissions = [];

        foreach ($existingPermissions as $existingPermission) {
            // we group permissions by section
            $section = str_replace(' ', '_', (str_replace('view ', '', str_replace('edit ', '', $existingPermission->name))));
            $permissions[$section][] = $existingPermission;
        }

        return $permissions;
    }

    //Relations

    /** get user Colla  */
    public function colla(): BelongsTo
    {
        return $this->belongsTo(Colla::class, 'colla_id', 'id_colla');
    }

    //getters
    public function getId(): int
    {
        return $this->getAttribute('id_user');
    }

    public function getCollaId(): int
    {
        return $this->getAttribute('colla_id');
    }

    public function getColla(): Colla
    {
        return $this->getAttribute('colla');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getEmail(): string
    {
        return $this->getAttribute('email');
    }

    public function getLanguage(): string
    {
        return $this->getAttribute('language');
    }

    public function getPhoto(): ?string
    {
        return $this->getAttribute('photo');
    }

    public function getPassword(): string
    {
        return $this->getAttribute('password');
    }

    public function getRememberToken(): ?string
    {
        return $this->getAttribute('remember_token');
    }

    public function getLastAccessAt(): ?Carbon
    {
        return $this->getAttribute('last_access_at');
    }

    public function getLastLogin(bool $shortDate = false): ?string
    {
        return Humans::parseDate($this->getLastAccessAt(), $shortDate);
    }
}
