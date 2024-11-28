<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-2">Forgotten password?</h1>
<p class="subtitle">Fill your email address to get a link to reset your password</p>

<!-- ERROR NOTIFICATIONS -->
<?php if (isset($_SESSION['error'])) : ?>
    <div class="notification is-danger">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form action="index.php?action=reset_password" method="post">
    <div>
        <label for="email">Email</label><br />
        <input class="input" type="email" id="email" name="email" />
    </div>
    <br>
    <div>
        <input class="button is-dark" value="Send link" type="submit" />
    </div>
</form>
<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>