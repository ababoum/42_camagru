<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1>Take a stylized picture with Camagru!</h1>
<p><a href="index.php">Go back to the main page</a></p>

<div class="news">
    <h3>
        <?= htmlspecialchars($post->title) ?>
    </h3>
    <em>on
        <?= $post->creationDate ?>
    </em>
    <br>
    <img src="<?= $post->image_path ?>" alt="Picture taken on <?= $post->creationDate ?>" />
</div>

<h2>Comments</h2>

<form action="index.php?action=addComment&id=<?= $post->identifier ?>" method="post">
    <div>
        <label for="author">Author</label><br />
        <input type="text" id="author" name="author" />
    </div>
    <div>
        <label for="comment">Comment</label><br />
        <textarea id="comment" name="comment"></textarea>
    </div>
    <div>
        <input value="Send" type="submit" />
    </div>
</form>

<?php
foreach ($comments as $comment) {
    ?>
    <p><strong>
            <?= htmlspecialchars($comment->author) ?>
        </strong> on
        <?= $comment->creationDate ?>
    </p>
    <p>
        <?= nl2br(htmlspecialchars($comment->comment)) ?>
    </p>
    <?php
}
?>
<?php $content = ob_get_clean(); ?>

<?php require('layout.php') ?>