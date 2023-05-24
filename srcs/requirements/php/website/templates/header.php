<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="index.php">
            <img src="assets/camera.png" width="28" height="28">
        </a>

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="index.php">
                Home
            </a>

            <a class="navbar-item" href="index.php?action=gallery">
                Gallery
            </a>

            <a class="navbar-item" href="index.php?action=profile">
                Profile
            </a>

            <a class="navbar-item" href="index.php?action=webcam">
                Take a picture
            </a>

            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) : ?>
                <a class="navbar-item" href="index.php?action=logout">
                    Log out
                </a>
            <?php else : ?>
                <a class="navbar-item" href="index.php?action=login">
                    Log in
                </a>
            <?php endif; ?>

        </div>
    </div>
</nav>