<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\PostDAO;
use Database\DatabaseManager;
use Models\Post;
use Models\DataTimeStamp;

class PostDAOImpl implements PostDAO
{
    public function create(Post $postData): bool
    {
        if($postData->getId() !== null) throw new \Exception('Failed to create post: Cannot create post with existing ID ' . $postData->getId());
        return $this->createOrUpdate($postData);
    }

    public function getById(int $id): ?Post
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $post = $mysqli->prepareAndFetchAll("SELECT * FROM posts WHERE id = ?",'i',[$id])[0]??null;

        return $post === null ? null : $this->resultToPost($post);
    }

    public function update(Post $postData): bool
    {
        if($postData->getId() === null) throw new \Exception('Computer part specified has no ID.');

        $current = $this->getById($postData->getId());
        if($current === null) throw new \Exception(sprintf("Computer part %s does not exist.", $postData->getId()));

        return $this->createOrUpdate($postData);
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM posts WHERE id = ?", 'i', [$id]);
    }



    public function createOrUpdate(Post $postData): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query =
        <<<SQL
            INSERT INTO posts (id, parent_id, title, content, path, image_path)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE id = ?,
            parent_id = VALUES(parent_id),
            title = VALUES(title),
            content = VALUES(content),
            path = VALUES(path),
            image_path = VALUES(image_path)
        SQL;

        $result = $mysqli->prepareAndExecute(
            $query,
            'iissssi',
            [
                // idがnullの場合、createのほうを行うことになる
                // それ以外の場合、ON DUPLICATE KEY UPDATEによってすでに存在するレコードに対してupdateが行われる
                $postData->getId(), // null idに対しては、mysqlによって自動生成されインクリメントされる
                $postData->getParentId(),
                $postData->getTitle(),
                $postData->getContent(),
                $postData->getPath(),
                $postData->getImagePath(),
                // ON DUPLICATE KEY UPDATEのために再度idを指定
                $postData->getId()
            ],
        );

        if(!$result) return false;

        // createの場合、idがnullなのでmysqlによって自動生成されたidをセットする
        if($postData->getId() === null){
            $postData->setId($mysqli->insert_id);
            $timeStamp = $postData->getTimeStamp()??new DataTimeStamp(date('Y-m-h'), date('Y-m-h'));
            $postData->setTimeStamp($timeStamp);
        }

        return true;
    }

    private function resultToPost(array $data): Post{
        return new Post(
            $data['id'],
            $data['parent_id'],
            $data['title'],
            $data['content'],
            $data['path'],
            $data['image_path'],
            new DataTimeStamp($data['created_at'], $data['updated_at'])
        );
    }

    private function resultToPosts(array $results): array{
        $posts = [];

        foreach($results as $result){
            $posts[] = $this->resultToPost($result);
        }

        return $posts;
    }

    public function getAllThreads(int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts LIMIT ?, ? WHERE parent_id IS NULL";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$offset, $limit]);

        return $results === null ? [] : $this->resultToPosts($results);
    }
    
    public function getReplies(Post $postData, int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM posts WHERE parent_id = ? LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'iii', [$postData->getId(), $offset, $limit]);

        return $results === null ? [] : $this->resultToPosts($results);
    }
 
}
