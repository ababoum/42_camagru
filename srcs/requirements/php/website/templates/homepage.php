<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-1 has-text-centered">Take a stylized picture with Camagru!</h1>
<p>Last pictures taken:</p>

<?php
foreach ($posts as $post) {
    ?>
    <div class="box">
        <h3 class="title is-3">
            <?= htmlspecialchars($post->title); ?>
        </h3>
        <em>Taken on
            <?= $post->creationDate; ?>
        </em>
        <p>
            <img src="<?= $post->image_path; ?>" alt="Picture taken on <?= $post->creationDate; ?>" />
            <br />
            <em><a href="index.php?action=post&id=<?= urlencode($post->identifier) ?>">Comments</a></em>
        </p>
    </div>
    <?php
}
?>
<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>