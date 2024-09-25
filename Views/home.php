<d?php

namespace Views;

?>

<div class="title-container">
    <p class="page-description pt-6">ダベリバは会員登録なしで気軽に投稿できる匿名掲示板です。<br/> 何でも好きなことについて、ゆったりだべりましょう。</p>
</div>

<div class="container flex flex-col align-center">
    <div class="flex flex-col align-center">
        <?php foreach($posts as $post): ?>
        <?php include 'component/postCard.php'; ?>
        <?php endforeach; ?> 
    </div>
</div>

    

