<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_any_role","create_role","view_role","update_role","delete_role","delete_any_role","view_any_permission","view_permission","create_permission","update_permission","delete_permission","delete_any_permission","view_any_shield::seeder","view_shield::seeder","create_shield::seeder","update_shield::seeder","delete_shield::seeder","delete_any_shield::seeder","view_any_shield::generate","view_shield::generate","create_shield::generate","update_shield::generate","delete_shield::generate","delete_any_shield::generate","view_any_shield::scan","view_shield::scan","create_shield::scan","update_shield::scan","delete_shield::scan","delete_any_shield::scan"]},{"name":"supervisor","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_branches::branch","view_any_branches::branch","create_branches::branch","update_branches::branch","delete_branches::branch","delete_any_branches::branch","view_coupons::coupon","view_any_coupons::coupon","create_coupons::coupon","update_coupons::coupon","restore_coupons::coupon","restore_any_coupons::coupon","replicate_coupons::coupon","reorder_coupons::coupon","delete_coupons::coupon","delete_any_coupons::coupon","force_delete_coupons::coupon","force_delete_any_coupons::coupon","view_exhibitions::exhibition","view_any_exhibitions::exhibition","create_exhibitions::exhibition","update_exhibitions::exhibition","delete_exhibitions::exhibition","delete_any_exhibitions::exhibition","view_users::user","view_any_users::user","create_users::user","update_users::user","restore_users::user","restore_any_users::user","replicate_users::user","reorder_users::user","delete_users::user","delete_any_users::user","force_delete_users::user","force_delete_any_users::user"]},{"name":"marketer","guard_name":"web","permissions":["view_coupons::coupon","view_any_coupons::coupon","restore_coupons::coupon","restore_any_coupons::coupon","delete_coupons::coupon","delete_any_coupons::coupon","force_delete_coupons::coupon","force_delete_any_coupons::coupon","view_users::user","view_any_users::user","create_users::user","update_users::user","restore_users::user","delete_users::user","delete_any_users::user","force_delete_users::user","force_delete_any_users::user"]},{"name":"agent","guard_name":"web","permissions":["view_coupons::coupon","view_any_coupons::coupon","create_coupons::coupon","update_coupons::coupon","delete_coupons::coupon"]},{"name":"customer service","guard_name":"web","permissions":["view_coupons::coupon","update_coupons::coupon","restore_coupons::coupon","restore_any_coupons::coupon","delete_coupons::coupon"]},{"name":"observer","guard_name":"web","permissions":["view_coupons::coupon","view_any_coupons::coupon"]}]';

        static::makeRolesWithPermissions($rolesWithPermissions);

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
}