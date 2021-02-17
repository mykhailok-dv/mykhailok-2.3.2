<?php
return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'crypt' => [
        'key' => '8661214c28d00831faf413c0f8a78070'
    ],
    'db' => [
        'table_prefix' => 'm2_',
        'connection' => [
            'default' => [
                'host' => 'mysql',
                'dbname' => 'mykhailok_local',
                'username' => 'root',
                'password' => 'root',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'system' => [
        'default' => [
            'web' => [
                'secure' => [
                    'base_url' => 'https://mykhailokhrypko.local/',
                    'base_media_url' => 'https://mykhailokhrypko.local/media/',
                    'base_static_url' => 'https://mykhailokhrypko.local/static/'
                ],
                'unsecure' => [
                    'base_url' => 'https://mykhailokhrypko.local/',
                    'base_media_url' => 'https://mykhailokhrypko.local/media/',
                    'base_static_url' => 'https://mykhailokhrypko.local/static/',
                    'base_link_url' => 'https://mykhailokhrypko.local/'
                ],
                'cookie' => [
                    'cookie_domain' => 'mykhailokhrypko.local'
                ]
            ],
            'catalog' => [
                'search' => [
                    'engine' => 'elasticsearch7',
                    'elasticsearch7_server_hostname' => 'elasticsearch'
                ]
            ]
        ],
        'websites' => [
            'additional_website' => [
                'web' => [
                    'unsecure' => [
                        'base_url' => 'https://mykhailokhrypko.local/',
                        'base_static_url' => 'https://mykhailokhrypko.local/static/',
                        'base_media_url' => 'https://mykhailokhrypko.local/media/'
                    ],
                    'secure' => [
                        'base_url' => 'https://mykhailokhrypko.local/',
                        'base_static_url' => 'https://mykhailokhrypko.local/static/',
                        'base_media_url' => 'https://mykhailokhrypko.local/media/'
                    ]
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => '40d_'
            ],
            'page_cache' => [
                'id_prefix' => '40d_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'lock' => [
        'provider' => 'db',
        'config' => [
            'prefix' => null
        ]
    ],
    'directories' => [
        'document_root_is_pub' => true
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 0,
        'block_html' => 0,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 0,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 1
    ],
    'install' => [
        'date' => 'Wed, 04 Dec 2019 09:04:13 +0000'
    ],
    'downloadable_domains' => [
        'mykhailokhrypko.local'
    ]
];
