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
    "guard_name": "web",
    "slug": "admin",
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
      "widget_MyCalendarWidget"
    ]
  },
  {
    "name": "supervisor",
    "guard_name": "web",
    "slug": "admin",
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
      "creat' .
            'e_exhibitions::exhibition",
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
    "name": "marketer",
    "guard_name": "web",
    "slug": "marketer",
    "permissions": [
      "view_coupons::coupon",
      "view_any_coupons::coupon",
      "restore_coupons::coupon",
      "restore_any_coupons::coupon",
      "delete_coupons::coupon",
      "delete_any_coupons::coupon",
      "force_delete_coupons::coupon",
      "force_delete_any_coupons::coupon",
      "view_users::user",
      "view_any_users::user",
      "create_users::user",
      "update_users::user",
      "restore_users::user",
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
      "update_coupons::coupon",
      "delete_coupons::coupon"
    ]
  },
  {
    "name": "customer service",
    "guard_name": "web",
    "slug": "employee",
    "permissions": [
      "view_coupons::coupon",
      "update_coupons::coupon",
      "restore_coupons::coupon",
      "restore_any_coupons::coupon",
      "delete_coupons::coupon"
    ]
  },
  {
    "name": "observer",
    "guard_name": "web",
    "slug": "admin",
    "permissions": [
      "view_coupons::coupon",
      "view_any_coupons::coupon"
    ]
  },
  {
    "name": "reporter",
    "slug": "reporter",
    "guard_name": "web",
    "permissions": []
  },
  {
    "name": "branch manager",
    "slug": "branch manager",
    "guard_name": "web",
    "permissions": []
  }
]';
        $directPermissions = '{"6":{"name":"view_any_permission","guard_name":"web"},"7":{"name":"view_permission","guard_name":"web"},"8":{"name":"create_permission","guard_name":"web"},"9":{"name":"update_permission","guard_name":"web"},"10":{"name":"delete_permission","guard_name":"web"},"11":{"name":"delete_any_permission","guard_name":"web"},"12":{"name":"view_any_shield::seeder","guard_name":"web"},"13":{"name":"view_shield::seeder","guard_name":"web"},"14":{"name":"create_shield::seeder","guard_name":"web"},"15":{"name":"update_shield::seeder","guard_name":"web"},"16":{"name":"delete_shield::seeder","guard_name":"web"},"17":{"name":"delete_any_shield::seeder","guard_name":"web"},"18":{"name":"view_any_shield::generate","guard_name":"web"},"19":{"name":"view_shield::generate","guard_name":"web"},"20":{"name":"create_shield::generate","guard_name":"web"},"21":{"name":"update_shield::generate","guard_name":"web"},"22":{"name":"delete_shield::generate","guard_name":"web"},"23":{"name":"delete_any_shield::generate","guard_name":"web"},"24":{"name":"view_any_shield::scan","guard_name":"web"},"25":{"name":"view_shield::scan","guard_name":"web"},"26":{"name":"create_shield::scan","guard_name":"web"},"27":{"name":"update_shield::scan","guard_name":"web"},"28":{"name":"delete_shield::scan","guard_name":"web"},"29":{"name":"delete_any_shield::scan","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (!blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
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

                if (!blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn($permission) => $permissionModel::firstOrCreate([
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
        if (!blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)
                    ->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
