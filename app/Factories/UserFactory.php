<?php

declare(strict_types=1);

namespace App\Factories;

use App\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\ParameterBag;

class UserFactory
{
    public static function make(int $collaId, ParameterBag $bag): User
    {
        $user = new User();

        $user->setAttribute('colla_id', $collaId);

        return self::update($user, $bag);
    }

    public static function update(User $user, ParameterBag $bag): User
    {

        if ($bag->has('name')) {
            $user->setAttribute('name', $bag->get('name'));
        }

        if ($bag->has('email')) {
            $user->setAttribute('email', $bag->get('email'));
        }

        if ($bag->has('language')) {
            $user->setAttribute('language', $bag->get('language'));
        } else {
            $user->setAttribute('language', 'ca');
        }

        if ($bag->has('password')) {
            $user->setAttribute('password', Hash::make($bag->get('password')));
        }

        // we need to save the user to be able to set a Role
        $user->save();

        if ($bag->has('role')) {
            $user->syncRoles($bag->get('role'));
        } else {
            $user->syncRoles('User');
        }

        // we need to save the user tagain to check the new role
        $user->save();

        if (! $user->hasRole('Super-Admin') && ! $user->hasRole('Colla-Admin')) {
            if ($bag->has('permissions')) {
                $user->syncPermissions($bag->get('permissions'));
            } else {
                $user->syncPermissions([]);
                $user->givePermissionTo('dashboard');
                $user->givePermissionTo('profile');
            }
        }

        return $user;

    }
}
