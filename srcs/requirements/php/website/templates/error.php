<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-1">Take a stylized picture with Camagru!</h1>
<p>An error occurred: <?= $errorMessage ?></p>
<?php $content = ob_get_clean(); ?>

<?php require('layout.php') ?>