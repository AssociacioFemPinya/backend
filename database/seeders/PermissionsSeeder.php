<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        foreach ([
            'dashboard',
            'profile',
            'view colla',
            'edit colla',
            'view BBDD',
            'edit BBDD',
            'view casteller config',
            'edit casteller config',
            'view events',
            'edit events',
            'view boards',
            'edit boards',
            'view notifications',
            'edit notifications',
            'view casteller personals',
            'edit casteller personals',
        ] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }


        // create roles

        // has access to everything automatically
        $role1 = Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);

        // basic user
        $role2 = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $role2->givePermissionTo('dashboard');
        $role2->givePermissionTo('profile');

        // colla admin -> has acccess to everything on that colla
        $role3 = Role::firstOrCreate(['name' => 'Colla-Admin', 'guard_name' => 'web']);
        $role3->givePermissionTo('dashboard');
        $role3->givePermissionTo('profile');
        $role3->givePermissionTo('view colla');
        $role3->givePermissionTo('edit colla');
        $role3->givePermissionTo('view BBDD');
        $role3->givePermissionTo('edit BBDD');
        $role3->givePermissionTo('view casteller config');
        $role3->givePermissionTo('edit casteller config');
        $role3->givePermissionTo('view events');
        $role3->givePermissionTo('edit events');
        $role3->givePermissionTo('view boards');
        $role3->givePermissionTo('edit boards');
        $role3->givePermissionTo('view notifications');
        $role3->givePermissionTo('edit notifications');
        $role3->givePermissionTo('view casteller personals');
        $role3->givePermissionTo('edit casteller personals');

    }
}
