<?php

return [
    'module' => [
        [
            'title' => 'Dashboard',
            'icon'  => 'fa fa-database',
            'name'  => ['dashboard'],
            'subModule' => [
                [
                    'title' => 'Dashboard',
                    'route' => 'dashboard/index',
                ],
            ]
        ],

        [
            'title' => 'Product Management',
            'icon'  => 'fa fa-cube',
            'name'  => ['product', 'attribute'],
            'subModule' => [
                [
                    'title' => 'Product Category Management',
                    'route' => 'product/catalogue/index'
                ],
                [
                    'title' => 'Product Management',
                    'route' => 'product/index'
                ],
                [
                    'title' => 'Attribute Category Management',
                    'route' => 'attribute/catalogue/index'
                ],
                [
                    'title' => 'Attribute Management',
                    'route' => 'attribute/index'
                ],
            ]
        ],

        [
            'title' => 'Banner & Slide Management',
            'icon'  => 'fa fa-picture-o',
            'name'  => ['slide'],
            'subModule' => [
                [
                    'title' => 'Slide Settings',
                    'route' => 'slide/index'
                ],
            ]
        ],

        [
            'title' => 'Post Management',
            'icon'  => 'fa fa-file',
            'name'  => ['post'],
            'subModule' => [
                [
                    'title' => 'Post Category Management',
                    'route' => 'post/catalogue/index'
                ],
                [
                    'title' => 'Post Management',
                    'route' => 'post/index'
                ]
            ]
        ],

        [
            'title' => 'User & Permission Management',
            'icon'  => 'fa fa-user',
            'name'  => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => 'User Group Management',
                    'route' => 'user/catalogue/index'
                ],
                [
                    'title' => 'User Management',
                    'route' => 'user/index'
                ],
                [
                    'title' => 'Permission Management',
                    'route' => 'permission/index'
                ]
            ]
        ],

        [
            'title' => 'Marketing Management',
            'icon'  => 'fa fa-money',
            'name'  => ['promotion', 'source'],
            'subModule' => [
                [
                    'title' => 'Promotion Management',
                    'route' => 'promotion/index'
                ],
                [
                    'title' => 'Customer Source Management',
                    'route' => 'source/index'
                ],
            ]
        ],

        [
            'title' => 'Order Management',
            'icon'  => 'fa fa-shopping-bag',
            'name'  => ['order'],
            'subModule' => [
                [
                    'title' => 'Order Management',
                    'route' => 'order/index'
                ],
            ]
        ],

        [
            'title' => 'Customer Group Management',
            'icon'  => 'fa fa-user',
            'name'  => ['customer'],
            'subModule' => [
                [
                    'title' => 'Customer Group Management',
                    'route' => 'customer/catalogue/index'
                ],
                [
                    'title' => 'Customer Management',
                    'route' => 'customer/index'
                ],
            ]
        ],

        [
            'title' => 'Menu Management',
            'icon'  => 'fa fa-bars',
            'name'  => ['menu'],
            'subModule' => [
                [
                    'title' => 'Menu Settings',
                    'route' => 'menu/index'
                ],
            ]
        ],

        [
            'title' => 'System Configuration',
            'icon'  => 'fa fa-file',
            'name'  => ['language', 'generate', 'system'],
            'subModule' => [
                [
                    'title' => 'Language Management',
                    'route' => 'language/index'
                ],
                [
                    'title' => 'Module Management',
                    'route' => 'generate/index'
                ],
                [
                    'title' => 'System Settings',
                    'route' => 'system/index'
                ],
                [
                    'title' => 'Widget Management',
                    'route' => 'widget/index'
                ],
            ]
        ],
    ],
];
