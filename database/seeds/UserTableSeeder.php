<?php

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('slug','admin')->first();

        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@example.com';
        $admin->password = bcrypt('password');
        $admin->mobile = '+15555555555';
        $admin->balance = 50000;
        $admin->save();
        $admin->assignRoleAndPermissions('admin');

        factory(App\User::class, 100)->create()->each(function ($user) {
            $user->assignRoleAndPermissions('admin');
        });

        factory(App\User::class, 100)->create()->each(function ($user) {
            $user->assignRoleAndPermissions('user');
        });
    }
}
