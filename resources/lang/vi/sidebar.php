<?php

return [
    'module' => [
        [
            'title' => 'Dashboard',
            'icon' => 'fa fa-database',
            'name' => ['dashboard'],
            'route' => 'dashboard/index',
            'class' => 'special'
        ],

        [
            'title' => 'QL San pham',
            'icon' => 'fa fa-cube',
            'name' => ['product', 'attribute'],
            'subModule' => [
                [
                    'title' => 'QL nhóm San pham',
                    'route' => 'product/catalogue/index'
                ],
                [
                    'title' => 'QL San pham',
                    'route' => 'product/index'
                ],
                [
                    'title' => 'QL Loại thuộc tính',
                    'route' => 'attribute/catalogue/index'
                ],
                [
                    'title' => 'QL thuộc tính',
                    'route' => 'attribute/index'
                ],
            ]
        ],
        [
            'title' => 'QL Banner & Slide',
            'icon' => 'fa fa-picture-o',
            'name' => ['slide'],
            'subModule' => [
                [
                    'title' => 'Cài đặt Slide',
                    'route' => 'slide/index'
                ],
            ]
        ],

        [
            'title' => 'QL Bình Luận',
            'icon' => 'fa fa-comment',
            'name' => ['reviews'],
            'subModule' => [
                [
                    'title' => 'QL Bình Luận',
                    'route' => 'review/index'
                ]
            ]
        ],

        [
            'title' => 'QL Bài viết',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'QL nhóm Bài viết',
                    'route' => 'post/catalogue/index'
                ],
                [
                    'title' => 'QL Bài viết',
                    'route' => 'post/index'
                ]
            ]
        ],

        [
            'title' => 'QL nhóm Thành viên',
            'icon' => 'fa fa-user',
            'name' => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => 'QL nhóm thành viên',
                    'route' => 'user/catalogue/index'
                ],
                [
                    'title' => 'QL thành viên',
                    'route' => 'user/index'
                ],
                [
                    'title' => 'QL quyền',
                    'route' => 'permission/index'
                ]
            ]
        ],
        [
            'title' => 'QL Marketing',
            'icon' => 'fa fa-money',
            'name' => ['promotion', 'source'],
            'subModule' => [
                [
                    'title' => 'QL Khuyến mại',
                    'route' => 'promotion/index'
                ],
                [
                    'title' => 'QL nguồn khách',
                    'route' => 'source/index'
                ],
            ]
        ],
        [
            'title' => 'QL đơn hàng',
            'icon' => 'fa fa-shopping-bag',
            'name' => ['order'],
            'subModule' => [
                [
                    'title' => 'QL Đơn Hàng',
                    'route' => 'order/index'
                ],
            ]
        ],
        [
            'title' => 'QL Nhóm Khách hàng',
            'icon' => 'fa fa-user',
            'name' => ['customer'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Khách hàng',
                    'route' => asset('customer/catalogue/index')
                ],
                [
                    'title' => 'QL Khách hàng',
                    'route' => 'customer/index'
                ],
            ]
        ],
        [
            'title' => 'QL Menu',
            'icon' => 'fa fa-bar',
            'name' => ['menu'],
            'subModule' => [
                [
                    'title' => 'Cài đặt Menu',
                    'route' => 'menu/index'
                ],

            ]
        ],
        [
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-file',
            'name' => ['language', 'generate', 'system'],
            'subModule' => [
                [
                    'title' => 'QL Ngôn ngữ',
                    'route' => 'language/index'
                ],
                [
                    'title' => 'QL Module',
                    'route' => 'generate/index'
                ],
                [
                    'title' => 'Cấu hình hệ thống',
                    'route' => 'system/index'
                ],
                [
                    'title' => 'Quản lý Widget',
                    'route' => 'widget/index'
                ],
            ]
        ],

    ],
];
