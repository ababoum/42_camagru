<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<section class="hero is-primary">
    <div class="hero-body">
        <p class="title has-text-centered">
            Take a stylized picture with Camagru!
        </p>
    </div>
</section>

<!-- NOTIFICATIONS -->
<?php if (isset($_SESSION['info'])) { ?>
    <div class="notification is-info is-light has-text-centered">
        <?= $_SESSION['info'] ?>
    </div>
    <?php unset($_SESSION['info']);
} ?>

<div class="columns is-vcentered">
    <div class="column is-half">
        <div class="content">
            <p><b>Camagru</b> is a webapp where you can:</p>
            <ul>
                <li>Take a picture with your webcam and apply a sticker on it!</li>
                <li>See your posts and other people's posts</li>
                <li>Like and comment the posts</li>
            </ul>
        </div>
    </div>
    <div class="column is-half is-flex is-justify-content-center is-align-items-center">
        <img src="assets/enjoy.png" alt="Enjoy!" class="center" width="200">
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>