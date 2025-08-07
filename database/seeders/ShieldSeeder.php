<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[
  {
    "name": "super_admin",
    "slug": "admin",
    "guard_name": "web",
    "permissions": [
      "view_any_role",
      "create_role",
      "view_role",
      "update_role",
      "delete_role",
      "delete_any_role",
      "view_branches::branch",
      "view_any_branches::branch",
      "create_branches::branch",
      "update_branches::branch",
      "delete_branches::branch",
      "delete_any_branches::branch",
      "view_coupons::coupon",
      "view_any_coupons::coupon",
      "create_coupons::coupon",
      "update_coupons::coupon",
      "restore_coupons::coupon",
      "restore_any_coupons::coupon",
      "replicate_coupons::coupon",
      "reorder_coupons::coupon",
      "delete_coupons::coupon",
      "delete_any_coupons::coupon",
      "force_delete_coupons::coupon",
      "force_delete_any_coupons::coupon",
      "view_exhibitions::exhibition",
      "view_any_exhibitions::exhibition",
      "create_exhibitions::exhibition",
      "update_exhibitions::exhibition",
      "delete_exhibitions::exhibition",
      "delete_any_exhibitions::exhibition",
      "view_users::user",
      "view_any_users::user",
      "create_users::user",
      "update_users::user",
      "restore_users::user",
      "restore_any_users::user",
      "replicate_users::user",
      "reorder_users::user",
      "delete_users::user",
      "delete_any_users::user",
      "force_delete_users::user",
      "force_delete_any_users::user",
      "widget_MyCalendarWidget",
      "view_sungard::branches::sungard::branches",
      "view_any_sungard::branches::sungard::branches",
      "create_sungard::branches::sungard::branches",
      "update_sungard::branches::sungard::branches",
      "restore_sungard::branches::sungard::branches",
      "restore_any_sungard::branches::sungard::branches",
      "replicate_sungard::branches::sungard::branches",
      "reorder_sungard::branches::sungard::branches",
      "delete_sungard::branches::sungard::branches",
      "delete_any_sungard::branches::sungard::branches",
      "force_delete_sungard::branches::sungard::branches",
      "force_delete_any_sungard::branches::sungard::branches"
    ]
  },
  {
    "name": "marketer",
    "guard_name": "web",
    "slug": "marketer",
    "permissions": [
      "view_branches::branch",
      "view_any_branches::branch",
      "create_branches::branch",
      "update_branches::branch",
      "delete_branches::branch",
      "delete_any_branches::branch",
      "view_coupons::coupon",
      "view_any_coupons::coupon",
      "view_exhibitions::exhibition",
      "view_any_exhibitions::exhibition",
      "create_exhibitions::exhibition",
      "update_exhibitions::exhibition",
      "delete_exhibitions::exhibition",
      "delete_any_exhibitions::exhibition",
      "view_users::user",
      "view_any_users::user",
      "create_users::user",
      "update_users::user",
      "restore_users::user",
      "restore_any_users::user",
      "replicate_users::user",
      "reorder_users::user",
      "delete_users::user",
      "delete_any_users::user",
      "force_delete_users::user",
      "force_delete_any_users::user"
    ]
  },
  {
    "name": "agent",
    "guard_name": "web",
    "slug": "agent",
    "permissions": [
      "view_coupons::coupon",
      "view_any_coupons::coupon",
      "create_coupons::coupon",
      "delete_coupons::coupon"
    ]
  },
  {
    "name": "customer service",
    "guard_name": "web",
    "slug": "customer service",
    "permissions": [
      "view_coupons::coupon",
      "view_any_coupons::coupon",
      "update_coupons::coupon",
      "restore_coupons::coupon",
      "restore_any_coupons::coupon",
      "delete_coupons::coupon"
    ]
  },
  {
    "name": "report manager",
    "slug": "customer service",
    "guard_name": "web",
    "permissions": []
  },
  {
    "name": "branch manager",
    "guard_name": "web",
    "slug": "branch manager",
    "permissions": []
  },
  {
    "name": "customer service manager",
    "guard_name": "web",
    "slug": "customer service manager",
    "permissions": [
      "view_coupons::coupon",
      "view_any_coupons::coupon",
      "create_coupons::coupon",
      "update_coupons::coupon",
      "restore_coupons::coupon",
      "restore_any_coupons::coupon",
      "replicate_coupons::coupon",
      "reorder_coupons::coupon",
      "delete_coupons::coupon",
      "delete_any_coupons::coupon",
      "force_delete_coupons::coupon",
      "force_delete_any_coupons::coupon",
      "view_users::user",
      "view_any_users::user",
      "create_users::user",
      "update_users::user",
      "restore_users::user",
      "restore_any_users::user",
      "replicate_users::user",
      "reorder_users::user",
      "delete_users::user",
      "delete_any_users::user",
      "force_delete_users::user",
      "force_delete_any_users::user"
    ]
  }
]';
        $directPermissions = '{"55":{"name":"view_any_permission","guard_name":"web"},"56":{"name":"view_permission","guard_name":"web"},"57":{"name":"create_permission","guard_name":"web"},"58":{"name":"update_permission","guard_name":"web"},"59":{"name":"delete_permission","guard_name":"web"},"60":{"name":"delete_any_permission","guard_name":"web"},"61":{"name":"view_any_shield::seeder","guard_name":"web"},"62":{"name":"view_shield::seeder","guard_name":"web"},"63":{"name":"create_shield::seeder","guard_name":"web"},"64":{"name":"update_shield::seeder","guard_name":"web"},"65":{"name":"delete_shield::seeder","guard_name":"web"},"66":{"name":"delete_any_shield::seeder","guard_name":"web"},"67":{"name":"view_any_shield::generate","guard_name":"web"},"68":{"name":"view_shield::generate","guard_name":"web"},"69":{"name":"create_shield::generate","guard_name":"web"},"70":{"name":"update_shield::generate","guard_name":"web"},"71":{"name":"delete_shield::generate","guard_name":"web"},"72":{"name":"delete_any_shield::generate","guard_name":"web"},"73":{"name":"view_any_shield::scan","guard_name":"web"},"74":{"name":"view_shield::scan","guard_name":"web"},"75":{"name":"create_shield::scan","guard_name":"web"},"76":{"name":"update_shield::scan","guard_name":"web"},"77":{"name":"delete_shield::scan","guard_name":"web"},"78":{"name":"delete_any_shield::scan","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                    'slug' => $rolePlusPermission['slug'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
