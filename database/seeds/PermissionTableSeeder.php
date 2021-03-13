<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('slug','admin')->first();
        foreach ($this->adminPermissions() as $permission) {
            $newPermission = $this->savePermission($permission);
            $newPermission->roles()->attach($adminRole);
        }

        $adminRole = Role::where('slug','user')->first();
        foreach ($this->userPermissions() as $permission) {
            $newPermission = $this->savePermission($permission);
            $newPermission->roles()->attach($adminRole);
        }
    }

    public function savePermission($permission)
    {
        return Permission::firstOrCreate(
            ['slug' => $permission['slug']],
            ['name' => $permission['name']]
        );
    }

    public function adminPermissions()
    {
        return [
            ['slug' => 'users-index', 'name' => 'See list of all users'],
            ['slug' => 'users-create', 'name' => 'Add new users'],
            ['slug' => 'users-update', 'name' => 'Edit users'],
            ['slug' => 'users-delete', 'name' => 'Remove users'],
            ['slug' => 'self-update', 'name' => 'Update self information'],

            ['slug' => 'products-import', 'name' => 'Import new products'],

            ['slug' => 'orders-create', 'name' => 'Create new order'],
            ['slug' => 'orders-update', 'name' => 'Edit orders'],
        ];
    }

    public function userPermissions()
    {
        return [
            ['slug' => 'self-update', 'name' => 'Update self information'],

            ['slug' => 'orders-create', 'name' => 'Create new order'],
            ['slug' => 'orders-update', 'name' => 'Edit orders'],
        ];
    }
}
