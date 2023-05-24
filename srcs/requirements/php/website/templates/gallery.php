<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<section class="hero is-primary">
    <div class="hero-body">
        <p class="title has-text-centered">
            Take a stylized picture with Camagru!
        </p>
    </div>
</section>

<p style="margin: 15px;	">Last pictures taken:</p>

<?php
// Display message box if no posts are available
if (empty($posts)) {
?>
    <div class="box has-text-centered">
        <p>No posts available.</p>
    </div>
<?php
}
foreach ($posts as $post) {
?>
    <div class="box has-text-centered">
        <h3 class="title is-3">
            <?= htmlspecialchars($post->title); ?>
        </h3>
        <em>Taken on
            <?= $post->creationDate; ?>
        </em>
        <p>
            <img src="<?= $post->image_path; ?>" alt="Picture taken on <?= $post->creationDate; ?>" />
            <br />
            <?php if ($post->does_current_user_like_post) { ?>
                <a href="index.php?action=unlike_post&id=<?= urlencode($post->id) ?>&page=<?= $page ?>">Unlike 👎 </a>
            <?php } else { ?>
                <a href="index.php?action=like_post&id=<?= urlencode($post->id) ?>&page=<?= $page ?>">Like 👍 </a>
            <?php } ?>
            —
            <?php if ($post->nb_likes == 1) { ?>
                <?= $post->nb_likes; ?> like
            <?php } else { ?>
                <?= $post->nb_likes; ?> likes
            <?php } ?>
            —
            <a href="index.php?action=post&id=<?= urlencode($post->id) ?>">Comments</a>
            <?php if ($post->author_id == $_SESSION['id']) { ?>
                <br />
                <button class="button is-danger" onclick="window.location.href='index.php?action=delete_post&id=<?= urlencode($post->id) ?>&page=<?= $page ?>'">Delete</button>
            <?php } ?>
        </p>
    </div>
<?php
}
?>
<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>