<?php

use Helpers\ValidationHelper;
use Models\Post;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Database\DataAccess\Implementations\PostDAOImpl;
use Response\Render\JSONRenderer;
use Types\ValueType;

return [
    '' => function(): HTTPRenderer{
        $postDAO = new PostDAOImpl();
        $posts = $postDAO->getAllThreads(0, 10);

        if ($posts === null) throw new Exception('No posts found.');

        return new HTMLRenderer('home', ['posts' => $posts]);
    },
];
