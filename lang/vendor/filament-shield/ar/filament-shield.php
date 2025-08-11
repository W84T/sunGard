<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'العنوان',
    'column.guard_name' => 'اسم الحارس',
    'column.roles' => 'الأدوار',
    'column.permissions' => 'الصلاحيات',
    'column.updated_at' => 'تاريخ التحديث',
    'column.slug' =>'المُعرّف',


    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'العنوان',
    'field.guard_name' => 'اسم الحارس',
    'field.permissions' => 'الصلاحيات',
    'field.select_all.name' => 'تحديد الكل',
    'field.select_all.message' => 'تفعيل كافة الصلاحيات لهذا الدور',
    'field.slug' =>'المُعرّف',
    'field.slug_options.admin' => 'مسؤول',
    'field.slug_options.agent' => 'وكيل',
    'field.slug_options.branch_manager' => 'مدير فرع',
    'field.slug_options.employee' => 'موظف',
    'field.slug_options.marketer' => 'مسوق',
    'field.slug_options.reporter' => 'مراسل',
    'field.slug_options.customer_service_manager' => 'مدير خدمة العملاء',
    'column.team_name.global' => 'عالمي',
    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'إدارة الوصول',
    'nav.role.label' => 'الأدوار',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'دور',
    'resource.label.roles' => 'الأدوار',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'الأقسام',
    'resources' => 'المصادر',
    'widgets' => 'الأجزاء',
    'pages' => 'الصفحات',
    'custom' => 'صلاحيات مخصصة',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'أنت غير مخول، لديك صلاحية للوصول',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'عرض',
        'view_any' => 'عرض الكل',
        'create' => 'إضافة',
        'update' => 'تعديل',
        'delete' => 'حذف',
        'delete_any' => 'حذف الكل',
        'force_delete' => 'إجبار الحذف',
        'force_delete_any' => ' إجبار حذف أي',
        'reorder' => 'إعادة ترتيب',
        'restore' => 'استرجاع',
        'restore_any' => 'استرجاع الكل',
        'replicate' => 'استنساخ',
    ],
];
