<?php

return [
    'module' => [
        [
            'title' => 'Post Management',
            'icon' => 'fa fa-file',
            'name' => ['post'],
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
            'title' => 'User Group Management',
            'icon' => 'fa fa-user',
            'name' => ['user'],
            'subModule' => [
                [
                    'title' => 'User Group Management',
                    'route' => 'user/catalogue/index'
                ],
                [
                    'title' => 'User Management',
                    'route' => 'user/index'
                ]
            ]
        ],

        [
            'title' => 'General Settings',
            'icon' => 'fa fa-file',
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => 'Language Management',
                    'route' => 'language/index'
                ]
            ]
        ]
    ],
];
