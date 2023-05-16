<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-3 has-text-centered">Error ❌</h1>
<div class="notification is-danger has-text-centered">
    <?= $errorMessage ?></p>
</div>
<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>