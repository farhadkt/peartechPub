<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['slug' => 'admin', 'name' => 'System Admin'],
            ['slug' => 'user', 'name' => 'Buyer/Seller'],
        ];

        foreach ($roles as $role) {
            $newRole = new Role();
            $newRole->slug = $role['slug'];
            $newRole->name = $role['name'];
            $newRole->save();
        }
    }
}
