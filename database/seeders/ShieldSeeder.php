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
      "view_branches::branch",
      "view_any_branches::branch",
      "create_branches::branch",
      "update_branches::branch",
      "restore_branches::branch",
      "restore_any_branches::branch",
      "delete_branches::branch",
      "delete_any_branches::branch",
      "force_delete_branches::branch",
      "force_delete_any_branches::branch",
      "view_coupons::coupon",
      "view_any_coupons::coupon",
      "create_coupons::coupon",
      "update_coupons::coupon",
      "restore_coupons::coupon",
      "restore_any_coupons::coupon",
      "delete_coupons::coupon",
      "delete_any_coupons::coupon",
      "force_delete_coupons::coupon",
      "force_delete_any_coupons::coupon",
      "submit_ticket_coupons::coupon",
      "change_status_coupons::coupon",
      "reserve_coupon_coupons::coupon",
      "view_exhibitions::exhibition",
      "view_any_exhibitions::exhibition",
      "create_exhibitions::exhibition",
      "update_exhibitions::exhibition",
      "restore_exhibitions::exhibition",
      "restore_any_exhibitions::exhibition",
      "delete_exhibitions::exhibition",
      "delete_any_exhibitions::exhibition",
      "force_delete_exhibitions::exhibition",
      "force_delete_any_exhibitions::exhibition",
      "view_role",
      "view_any_role",
      "create_role",
      "update_role",
      "delete_role",
      "delete_any_role",
      "view_sungard::branches::sungard::branches",
      "view_any_sungard::branches::sungard::branches",
      "create_sungard::branches::sungard::branches",
      "update_sungard::branches::sungard::branches",
      "restore_sungard::branches::sungard::branches",
      "restore_any_sungard::branches::sungard::branches",
      "delete_sungard::branches::sungard::branches",
      "delete_any_sungard::branches::sungard::branches",
      "force_delete_sungard::branches::sungard::branches",
      "force_delete_any_sungard::branches::sungard::branches",
      "view_tickets::ticket",
      "view_any_tickets::ticket",
      "create_tickets::ticket",
      "update_tickets::ticket",
      "restore_tickets::ticket",
      "restore_any_tickets::ticket",
      "delete_tickets::ticket",
      "delete_any_tickets::ticket",
      "force_delete_tickets::ticket",
      "force_delete_any_tickets::ticket",
      "view_users::user",
      "view_any_users::user",
      "create_users::user",
      "update_users::user",
      "restore_users::user",
      "restore_any_users::user",
      "delete_users::user",
      "delete_any_users::user",
      "force_delete_users::user",
      "force_delete_any_users::user",
      "page_CouponsCalendarPage",
      "widget_BranchCoupons",
      "widget_GrowthWidget",
      "widget_MyCalendarWidget",
      "widget_StateOverview",
      "widget_TopAgentWidget"
    ]
  },
  {
    "name": "marketer",
    "slug": "marketer",
    "guard_name": "web",
    "permissions": []
  },
  {
    "name": "agent",
    "slug": "agent",
    "guard_name": "web",
    "permissions": []
  },
  {
    "name": "customer service",
    "slug": "customer service",
    "guard_name": "web",
    "permissions": []
  },
  {
    "name": "report manager",
    "slug": "report manager",
    "guard_name": "web",
    "permissions": []
  },
  {
    "name": "branch manager",
    "slug": "branch manager",
    "guard_name": "web",
    "permissions": []
  },
  {
    "name": "customer service manager",
    "slug": "customer service manager",
    "guard_name": "web",
    "permissions": []
  }
]';
        $directPermissions = '[]';

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
