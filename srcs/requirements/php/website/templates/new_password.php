<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-1">Reset your password</h1>

<!-- ERROR NOTIFICATIONS -->
<?php if (isset($_SESSION['error'])) : ?>
    <div class="notification is-danger">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form action="index.php?action=new_password" method="post">
    <div>
        <label for="password">Password</label><br />
        <input class="input" type="password" id="password" name="password" placeholder="Password" />
    </div>
    <div>
        <label for="re_password">Confirm password</label><br />
        <input class="input" type="password" id="re_password" name="re_password" placeholder="Confirm password" />
    </div>
    <div hidden>
        <input class="input" type="email" id="email" name="email" value=<?= $_SESSION['email'] ?? '' ?> />
    </div>
    <div hidden>
        <input class="input" type="text" id="token" name="token" value=<?= $_SESSION['token'] ?? '' ?> />
    </div>
    <br>
    <div>
        <input class="button is-dark" value="Change password" type="submit" />
    </div>
</form>
<?php $content = ob_get_clean(); ?>

<?php require('layout.php') ?>