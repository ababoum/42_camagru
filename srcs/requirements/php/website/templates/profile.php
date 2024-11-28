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
<form action="index.php?action=update_username" method="post">
    <h3 class="title is-4">Update Username</h3>
    <div class="field">
        <div class="columns is-vcentered">
            <div class="column is-narrow">
                <div class="control">
                    <input type="text" name="username" class="input is-small" style="width: 200px;" placeholder="New Username" required>
                </div>
            </div>
            <div class="column is-narrow">
                <div class="control">
                    <button type="submit" name="update_username" class="button is-link is-small">Update Username</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="index.php?action=update_email" method="post">
    <h3 class="title is-4">Update Email</h3>
    <div class="field">
        <div class="columns is-vcentered">
            <div class="column is-narrow">
                <div class="control">
                    <input type="email" name="email" class="input is-small" style="width: 200px;" placeholder="New Email" required>
                </div>
            </div>
            <div class="column is-narrow">
                <div class="control">
                    <button type="submit" name="update_email" class="button is-link is-small">Update Email</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="index.php?action=update_password" method="post">
    <h3 class="title is-4">Update Password</h3>
    <div class="field">
        <div class="columns is-vcentered">
            <div class="column is-narrow">
                <div class="control">
                    <input type="password" name="password" class="input is-small" style="width: 200px;" placeholder="New Password" required>
                </div>
            </div>
            <div class="column is-narrow">
                <div class="control">
                    <input type="password" name="re_password" class="input is-small" style="width: 200px;" placeholder="Confirm New Password" required>
                </div>
            </div>
            <div class="column is-narrow">
                <div class="control">
                    <button type="submit" name="update_password" class="button is-link is-small">Update Password</button>
                </div>
            </div>
        </div>
    </div>
</form>


<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>