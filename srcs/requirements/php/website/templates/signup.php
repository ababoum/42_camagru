<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-1">Sign up</h1>

<form action="index.php?action=signup" method="post">
    <div>
        <label for="username">Username</label><br />
        <input class="input" type="text" id="username" name="username" placeholder="Username" />
    </div>
    <div>
        <label for="email">Email</label><br />
        <input class="input" type="email" id="email" name="email" placeholder="Email address" />
    </div>
    <div>
        <label for="password">Password</label><br />
        <input class="input" type="password" id="password" name="password" placeholder="Password" />
    </div>
    <div>
        <label for="re_password">Confirm password</label><br />
        <input class="input" type="password" id="re_password" name="re_password" placeholder="Confirm password" />
    </div>
    <br>
    <div>
        <input class="button is-dark" value="Sign up" type="submit" />
    </div>
</form>
<?php $content = ob_get_clean(); ?>

<?php require('layout.php') ?>