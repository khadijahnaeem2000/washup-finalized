<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
        	'name' => 'admin', 
        	'email' => 'admin@gmail.com',
        	'password' => Hash::make('rootroot')
        ]);
  
        $role = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();
  
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}
