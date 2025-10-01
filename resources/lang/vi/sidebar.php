<?php

return [
    'module' => [
        [
            'title' => 'QL Anh',
            'icon' => 'fa fa-cube',
            'name' => ['gallery'],
            'subModule' => [
                [
                    'title' => 'QL nhóm hinh anh',
                    'route' => 'gallery/catalogue/index'
                ],
                [
                    'title' => 'QL hinh anh',
                    'route' => 'gallery/index'
                ],
            ]
        ],
        [
            'title' => 'QL San pham',
            'icon' => 'fa fa-cube',
            'name' => ['product'],
            'subModule' => [
                [
                    'title' => 'QL nhóm San pham',
                    'route' => 'product/catalogue/index'
                ],
                [
                    'title' => 'QL San pham',
                    'route' => 'product/index'
                ],
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
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-file',
            'name' => ['language', 'generate'],
            'subModule' => [
                [
                    'title' => 'QL Ngôn ngữ',
                    'route' => 'language/index'
                ],
                [
                    'title' => 'QL Module',
                    'route' => 'generate/index'
                ]
            ]
        ],
    ],
];
