<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-1">Forgotten password?</h1>
<p class="subtitle">Fill your email address to get a link to reset your password</p>

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

<?php require('layout.php') ?>