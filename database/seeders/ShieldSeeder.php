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

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_branches::branch","view_any_branches::branch","create_branches::branch","update_branches::branch","delete_branches::branch","delete_any_branches::branch","view_coupons::coupon","view_any_coupons::coupon","create_coupons::coupon","update_coupons::coupon","restore_coupons::coupon","restore_any_coupons::coupon","replicate_coupons::coupon","reorder_coupons::coupon","delete_coupons::coupon","delete_any_coupons::coupon","force_delete_coupons::coupon","force_delete_any_coupons::coupon","view_exhibitions::exhibition","view_any_exhibitions::exhibition","create_exhibitions::exhibition","update_exhibitions::exhibition","delete_exhibitions::exhibition","delete_any_exhibitions::exhibition","view_users::user","view_any_users::user","create_users::user","update_users::user","restore_users::user","restore_any_users::user","replicate_users::user","reorder_users::user","delete_users::user","delete_any_users::user","force_delete_users::user","force_delete_any_users::user"]},{"name":"marketer","guard_name":"web","permissions":["view_coupons::coupon","update_coupons::coupon","restore_coupons::coupon","restore_any_coupons::coupon","delete_coupons::coupon","delete_any_coupons::coupon","force_delete_coupons::coupon","force_delete_any_coupons::coupon","view_users::user","create_users::user","update_users::user","restore_users::user","delete_users::user","delete_any_users::user","force_delete_users::user","force_delete_any_users::user"]},{"name":"agent","guard_name":"web","permissions":["view_coupons::coupon","create_coupons::coupon","update_coupons::coupon","delete_coupons::coupon"]},{"name":"employee","guard_name":"web","permissions":["view_coupons::coupon","update_coupons::coupon","restore_coupons::coupon","restore_any_coupons::coupon","delete_coupons::coupon","delete_any_coupons::coupon","force_delete_coupons::coupon","force_delete_any_coupons::coupon"]}]';
        $directPermissions = '{"10":{"name":"restore_branches::branch","guard_name":"web"},"11":{"name":"restore_any_branches::branch","guard_name":"web"},"12":{"name":"replicate_branches::branch","guard_name":"web"},"13":{"name":"reorder_branches::branch","guard_name":"web"},"16":{"name":"force_delete_branches::branch","guard_name":"web"},"17":{"name":"force_delete_any_branches::branch","guard_name":"web"},"34":{"name":"restore_exhibitions::exhibition","guard_name":"web"},"35":{"name":"restore_any_exhibitions::exhibition","guard_name":"web"},"36":{"name":"replicate_exhibitions::exhibition","guard_name":"web"},"37":{"name":"reorder_exhibitions::exhibition","guard_name":"web"},"40":{"name":"force_delete_exhibitions::exhibition","guard_name":"web"},"41":{"name":"force_delete_any_exhibitions::exhibition","guard_name":"web"},"54":{"name":"marketer","guard_name":"web"},"55":{"name":"employee","guard_name":"web"}}';

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
