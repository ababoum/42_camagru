<?php $title = "Camagru"; ?>

<?php ob_start(); ?>

<section class="hero is-primary">
    <div class="hero-body">
        <p class="title has-text-centered">
            Take a stylized picture with Camagru!
        </p>
    </div>
</section>

<div class="box">
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

<form action="index.php?action=addComment&id=<?= $post->id ?>" method="post">
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

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>