<!DOCTYPE html>
<html>

<head>
    <title>Camagru - Sign up page</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <form action="signup.php" method="post">
        <h2>SIGN UP</h2>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error">
                <?php echo $_GET['error']; ?>
            </p>
        <?php } ?>
        <label>Username</label>
        <input type="text" name="uname" placeholder="Username"><br>
        <label>Email address</label>
        <input type="email" name="email" placeholder="Email address"><br>
        <label>Password</label>
        <input type="password" name="password" placeholder="Password"><br>
        <label>Confirm Password</label>
        <input type="password" name="re_password" placeholder="Confirm Password"><br>
        <button type="submit">Sign up</button>
    </form>
</body>

</html>