<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-1">Log in</h1>
<p class="subtitle">You need to be logged in to get access to Camagru</p>

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
<?php $content = ob_get_clean(); ?>

<?php require('layout.php') ?>