<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Name',
    'column.guard_name' => 'Guard Name',
    'column.team' => 'Team',
    'column.roles' => 'Roles',
    'column.permissions' => 'Permissions',
    'column.updated_at' => 'Updated At',
    'column.slug' =>'Slug',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Name',
    'field.guard_name' => 'Guard Name',
    'field.permissions' => 'Permissions',
    'field.team' => 'Team',
    'field.team.placeholder' => 'Select a team ...',
    'field.select_all.name' => 'Select All',
    'field.select_all.message' => 'Enables/Disables all Permissions for this role',
    'field.slug' =>'Slug',
    'field.slug_options.admin' => 'Admin',
    'field.slug_options.agent' => 'Agent',
    'field.slug_options.branch_manager' => 'Branch Manager',
    'field.slug_options.employee' => 'Employee',
    'field.slug_options.marketer' => 'Marketer',
    'field.slug_options.reporter' => 'Reporter',
    'field.slug_options.customer_service_manager' => 'Customer Service Manager',
    'column.team_name.global' => 'Global',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Roles',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Role',
    'resource.label.roles' => 'Roles',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entities',
    'resources' => 'Resources',
    'widgets' => 'Widgets',
    'pages' => 'Pages',
    'custom' => 'Custom Permissions',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'You do not have permission to access',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'View',
        'view_any' => 'View Any',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'delete_any' => 'Delete Any',
        'force_delete' => 'Force Delete',
        'force_delete_any' => 'Force Delete Any',
        'restore' => 'Restore',
        'reorder' => 'Reorder',
        'restore_any' => 'Restore Any',
        'replicate' => 'Replicate',
        'submit_ticket' => 'Report a Problem / Support Ticket',
        'change_status' => 'Change Coupon Status',
        'reserve_coupon' => 'Reserve Coupon (For Customer Service Only)'

    ],
];
