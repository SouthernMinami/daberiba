<?php

namespace Database\Seeds;

require_once 'vendor/autoload.php';

use Database\AbstractSeeder;

class PostsSeeder extends AbstractSeeder
{
    protected ?string $tableName = 'posts';
    protected array $tableColumns = [
        [
            'data_type' => 'int',
            'column_name' => 'parent_id'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'title'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'content'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'path'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'image_path'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'thumbnail_path'
        ]
    ];

    public function createRowData(): array
    {
        return [
            ...array_map(function () {
                return [
                    0,
                    \Faker\Factory::create()->title,
                    \Faker\Factory::create()->text,
                    hash('sha256', \Faker\Factory::create()->text),
                    \Faker\Factory::create()->imageUrl,
                    \Faker\Factory::create()->imageUrl('200', '200'),
                ];

            }, range(0, 9))
        ];
    }
}
