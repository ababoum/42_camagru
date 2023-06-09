<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-2 has-text-centered">Your profile</h1>


<div class="box">
    👤 Username: <strong><?= $user->username ?></strong>
</div>
<div class="box">
    📧 Email address: <strong><?= $user->email ?></strong>
</div>


<!-- EMAIL NOTIFICATIONS -->
<h2 class="title is-3 has-text-centered">Email notifications</h2>
<?php if ($user->accept_notifications == 1) { ?>
    <div class="box has-text-centered">
        ✔️ You will receive an email when someone comments on your posts.<br />
        <a href="index.php?action=update_email_notifications&value=0">Disable</a>
    </div>

<?php } else { ?>
    <div class="box has-text-centered">
        🛑 You will not receive an email when someone comments on your posts.<br />
        <a href="index.php?action=update_email_notifications&value=1">Enable</a>
    </div>
<?php } ?>


<h2 class="title is-3 has-text-centered">Update your details</h2>

<!-- NOTIFICATION -->
<?php
if (isset($_SESSION['info'])) {
?>
    <div class="notification is-primary">
        <?= $_SESSION['info'] ?>
    </div>
<?php
    unset($_SESSION['info']);
}
?>

<!-- ERROR NOTIFICATION -->
<?php
if (isset($_SESSION['error'])) {
?>
    <div class="notification is-danger">
        <?= $_SESSION['error'] ?>
    </div>
<?php
    unset($_SESSION['error']);
}
?>

<!-- UPDATE INFO FORM -->
<form action="index.php?action=update_user" method="post">
    <div>
        <label for="username">Username</label><br />
        <input class="input" type="text" id="username" name="username" placeholder="Username" value="<?= $user->username ?>" />
    </div>
    <div>
        <label for="email">Email</label><br />
        <input class="input" type="email" id="email" name="email" placeholder="Email address" value="<?= $user->email ?>" />
    </div>
    <div>
        <label for="password">Password</label><br />
        <input class="input" type="password" id="password" name="password" placeholder="Password" />
    </div>
    <div>
        <label for="password">Confirm password</label><br />
        <input class="input" type="password" id="re_password" name="re_password" placeholder="Confirm password" />
    </div>
    <br>
    <div>
        <input class="button is-dark" value="Update" type="submit" />
    </div>
</form>

<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>