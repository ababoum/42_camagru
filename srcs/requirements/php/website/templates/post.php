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
    <em>Taken on
        <?= $post->creation_date ?>
    </em>
    <br>
    <img src="<?= $post->image_path ?>" alt="Picture taken on <?= $post->creation_date ?>" />
</div>

<h2 class="title is-3">Comments</h2>

<?php if ($current_user_id) { ?>
    <form action="index.php?action=add_comment&post_id=<?= $post->id ?>" method="post">
        <div>
            <label class="label" for="comment">New comment</label><br />
            <textarea class="textarea" id="comment" name="comment"></textarea>
        </div>
        <div>
            <input class="button is-link is-light" value="Send" type="submit" />
        </div>
    </form>
<?php } ?>

<?php
foreach ($comments as $comment) {
    ?>
    <p><strong>
            <?= htmlspecialchars($comment->author) ?>
        </strong> on
        <?= $comment->creation_date ?>
    </p>
    <?php
    if ($comment->author_id === $current_user_id) {
        ?>
        <a href="index.php?action=delete_comment&id=<?= $comment->id ?>&post_id=<?= $post->id ?>" class="button is-danger is-small">Delete</a>
        <?php
    }
    ?>
    <div class="content">
        <blockquote>
            <?= nl2br(htmlspecialchars($comment->comment)) ?>
        </blockquote>
    </div>
    <?php
}
?>
<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>