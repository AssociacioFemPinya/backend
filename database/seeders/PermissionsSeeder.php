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
        Permission::create(['name' => 'dashboard']);
        Permission::create(['name' => 'profile']);

        Permission::create(['name' => 'view colla']);
        Permission::create(['name' => 'edit colla']);

        Permission::create(['name' => 'view BBDD']);
        Permission::create(['name' => 'edit BBDD']);

        Permission::create(['name' => 'view casteller config']);
        Permission::create(['name' => 'edit casteller config']);

        Permission::create(['name' => 'view events']);
        Permission::create(['name' => 'edit events']);

        Permission::create(['name' => 'view boards']);
        Permission::create(['name' => 'edit boards']);

        Permission::create(['name' => 'view notifications']);
        Permission::create(['name' => 'edit notifications']);

        Permission::create(['name' => 'view casteller personals']);
        Permission::create(['name' => 'edit casteller personals']);


        // create roles

        // has access to everything automatically
        $role1 = Role::create(['name' => 'Super-Admin']);

        // basic user
        $role2 = Role::create(['name' => 'User']);
        $role2->givePermissionTo('dashboard');
        $role2->givePermissionTo('profile');

        // colla admin -> has acccess to everything on that colla
        $role3 = Role::create(['name' => 'Colla-Admin']);
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
