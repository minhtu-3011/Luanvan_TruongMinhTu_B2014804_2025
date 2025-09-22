<?php

return [
    'module' => [
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
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => 'QL Ngôn ngữ',
                    'route' => 'language/index'
                ]
            ]
        ]
    ],
];
