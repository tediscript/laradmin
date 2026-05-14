<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create post permissions
        $permissions = ['post.view', 'post.create', 'post.update', 'post.delete'];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create admin role and assign all permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo($permissions);

        // Create editor role with limited permissions
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->givePermissionTo(['post.view', 'post.create', 'post.update']);

        // Assign admin role to default admin users
        $adminEmails = ['admin@example.com'];

        foreach ($adminEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && ! $user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        }
    }
}
