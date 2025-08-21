<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles (consistent with your store method)
        $roles = [
            'admin' => [
                'description' => 'Administrator with full access',
                'permissions' => [
                    'Dashboard' => [
                        'dashboard-view',
                    ],
                    'Access' => [
                        'access-view',
                        'permission-view',
                        'users-view',
                        'roles-view',
                    ],
                    'Settings' => [
                        'settings-view',
                        'menu-view',
                        'app-settings-view',
                        'backup-view',
                    ],
                    'Utilities' => [
                        'utilities-view',
                        'log-view',
                        'filemanager-view',
                    ],
                    'Jobs' => [
                        'job-view',
                        'job-create',
                        'job-edit',
                        'job-delete',
                        'job-apply',
                    ],
                ],
            ],
            'user' => [
                'description' => 'Basic user with limited access',
                'permissions' => [
                    'Dashboard' => [
                        'dashboard-view',
                    ],
                ],
            ],
            'jobseeker' => [
                'description' => 'Jobseeker who can view and apply for jobs',
                'permissions' => [
                    'Dashboard' => [
                        'dashboard-view',
                    ],
                    'Jobs' => [
                        'job-view',
                        'job-apply',
                    ],
                ],
            ],
            'employer' => [
                'description' => 'Employer who can create and manage jobs',
                'permissions' => [
                    'Dashboard' => [
                        'dashboard-view',
                    ],
                    'Jobs' => [
                        'job-view',
                        'job-create',
                        'job-edit',
                        'job-delete',
                    ],
                ],
            ],
        ];

        foreach ($roles as $roleName => $roleData) {
            // Create or update role
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Collect all permissions for this role
            $rolePermissions = [];
            foreach ($roleData['permissions'] as $group => $permissions) {
                foreach ($permissions as $permissionName) {
                    // Create permission if it doesn't exist
                    $permission = Permission::firstOrCreate([
                        'name' => $permissionName,
                        'group' => $group,
                    ]);
                    $rolePermissions[] = $permission->name;
                }
            }

            // Sync permissions to the role (removes any unlisted permissions)
            $role->syncPermissions($rolePermissions);
        }
    }
}