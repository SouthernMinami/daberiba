<div class="max-w-sm my-3 p-6 border rounded-lg card" style="width: 20rem;">
    <div class="card-body">
        <h2 class="card-title"><?= htmlspecialchars($post->getTitle()) ?></h2>
        <h6 class="card-content mb-2 text-muted"><?= htmlspecialchars($post->getContent()) ?></h6>
        <img src="<?= htmlspecialchars($post->getThumbnailPath()) ?>" class="card-img-top" alt="...">
    </div>
</div>
