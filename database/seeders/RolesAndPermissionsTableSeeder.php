<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use App\Models\User;

class RolesAndPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'edit.books']);
        Permission::create(['name' => 'delete.books']);
        Permission::create(['name' => 'create.books']);
        Permission::create(['name' => 'view.books']);

        Permission::create(['name' => 'edit.authors']);
        Permission::create(['name' => 'delete.authors']);
        Permission::create(['name' => 'create.authors']);
        Permission::create(['name' => 'view.authors']);
        Permission::create(['name' => 'view.author']);

        Permission::create(['name' => 'edit.users']);
        Permission::create(['name' => 'edit.user']);
        Permission::create(['name' => 'delete.users']);
        Permission::create(['name' => 'create.users']);
        Permission::create(['name' => 'view.users']);

        // admin permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        // user permission
        $role = Role::create(['name' => 'user'])
            ->givePermissionTo(['view.books', 'view.author', 'edit.user']);

        // assign role to users
        $users = User::all();

        foreach($users as &$user) {
            if ($user->id == 1) {
                $user->assignRole('admin');
            } else {
                $user->assignRole('user');
            }
        }
    }
}
