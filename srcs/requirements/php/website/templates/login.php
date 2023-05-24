<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-2">Log in</h1>
<p class="subtitle">You need to be logged in to get access to Camagru</p>

<!-- NOTIFICATION -->
<?php
if (isset($_SESSION['info'])) {
    ?>
    <div class="notification is-primary">
        <?= $_SESSION['info'] ?>
    </div>
    <?php unset($_SESSION['info']);
}
?>

<!-- ERRORS -->
<?php
if (isset($_SESSION['error'])) {
    ?>
    <div class="notification is-danger">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']);
}
?>

<!-- LOGIN FORM -->
<form action="index.php?action=login" method="post">
    <div>
        <label for="username">Username</label><br />
        <input class="input" type="text" id="username" name="username" />
    </div>
    <div>
        <label for="password">Password</label><br />
        <input class="input" type="password" id="password" name="password" />
    </div>
    <br>
    <div>
        <input class="button is-dark" value="Log in" type="submit" />
    </div>
</form>
<div>
    <a href="index.php?action=reset_password">Forgotten password?</a> — Don't have an account? <a
        href="index.php?action=signup">Sign up</a>
</div>
<?php $content = ob_get_clean(); ?>

<?php require('layout.php') ?>