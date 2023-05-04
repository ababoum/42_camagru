<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1>Log in</h1>
<p>You need to be logged in to get access to Camagru</p>

<form action="index.php?action=login" method="post">
    <div>
        <label for="username">Username</label><br />
        <input type="text" id="username" name="username" />
    </div>
    <div>
        <label for="password">Password</label><br />
        <input type="password" id="password" name="password" />
    </div>
    <div>
        <input value="Log in" type="submit" />
    </div>
</form>
<?php $content = ob_get_clean(); ?>

<?php require('layout.php') ?>