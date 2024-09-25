<?php

namespace Database\DataAccess\Interfaces;

use Models\Post;

interface PostDAO {
    public function create(Post $postData): bool;
    public function getById(int $id): ?Post;
    public function update(Post $postData): bool;
    public function delete(int $id): bool;
    public function createOrUpdate(Post $postData): bool;

    /**
     * @param int $offset
     * @param int $limit
     * @return Post[] ほか投稿への返信ではないメインスレッドであるすべての投稿、つまりparentIdがnullの投稿
     */
    public function getAllThreads(int $offset, int $limit): array;
    
    /**
     * @param Post $postData - すべての返信の親となるメインスレッド（投稿）
     * @param int $offset
     * @param int $limit
     * @return Post[] $parentId が $postData の id と一致する投稿の返信
     */
    public function getReplies(Post $postData, int $offset, int $limit): array;
}
