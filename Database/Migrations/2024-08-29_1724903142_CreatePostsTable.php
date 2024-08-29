<?php

namespace Database\Migrations;

use Database\SchemaMigration;

class CreatePostsTable implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーション処理を書く
        return [
            'CREATE TABLE posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                parent_id INT DEFAULT NULL,
                title VARCHAR(255) DEFAULT NULL,
                content TEXT,
                path VARCHAR(255),
                image_path VARCHAR(255),
                thumbnail_path VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )'
        ];
    }

    public function down(): array
    {
        // ロールバック処理を書く
        return [
            'DROP TABLE posts'
        ];
    }
}