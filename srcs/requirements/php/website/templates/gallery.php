<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<section class="hero is-primary">
    <div class="hero-body">
        <p class="title has-text-centered">
            Take a stylized picture with Camagru!
        </p>
    </div>
</section>

<p style="margin: 15px;	">Last pictures taken:</p>

<?php
// Display message box if no posts are available
if (empty($posts)) {
    ?>
    <div class="box has-text-centered">
        <p>No posts available.</p>
    </div>
<?php } else { ?>
    <?php foreach ($posts as $post) {
        ?>
        <div class="box has-text-centered">
            <h3 class="title is-4">
                <?= htmlspecialchars($post->title); ?>
            </h3>
            <em>Taken on
                <?= $post->creation_date; ?>
            </em>
            <p>
                <img src="<?= $post->image_path; ?>" alt="Picture taken on <?= $post->creation_date; ?>" width="400"
                    height="auto" />
                <br />
                <?php if ($current_user_id) {
                    if ($post->does_current_user_like_post) { ?>
                        <a href="index.php?action=unlike_post&id=<?= urlencode($post->id) ?>&page=<?= $page ?>">Unlike 👎 —</a>
                    <?php } else { ?>
                        <a href="index.php?action=like_post&id=<?= urlencode($post->id) ?>&page=<?= $page ?>">Like 👍 —</a>
                    <?php }
                } ?>
                <?php if ($post->nb_likes == 1) { ?>
                    <?= $post->nb_likes; ?> like
                <?php } else { ?>
                    <?= $post->nb_likes; ?> likes
                <?php } ?>
                —
                <a href="index.php?action=post&id=<?= urlencode($post->id) ?>">
                    <?= $post->nb_comments; ?>&nbsp;<?= $post->nb_comments == 1 ? 'comment' : 'comments' ?>
                </a>
                <?php if (isset($_SESSION['id']) && $post->author_id == $_SESSION['id']) { ?>
                    <br />
                    <button class="button is-danger"
                        onclick="window.location.href='index.php?action=delete_post&id=<?= urlencode($post->id) ?>&page=<?= $page ?>'">Delete</button>
                <?php } ?>
            </p>
        </div>
        <?php
    } ?>
    <nav class="pagination" role="navigation" aria-label="pagination">
        <a class="pagination-previous" href="index.php?action=gallery&page=<?= $page > 1 ? $page - 1 : 1 ?>">Previous</a>
        <a class="pagination-next"
            href="index.php?action=gallery&page=<?= $page >= $nb_of_pages ? $nb_of_pages : $page + 1 ?>">Next page</a>
        <ul class="pagination-list">
            <?php for ($p = 1; $p <= $nb_of_pages; $p++) {
                if ($p === $page) { ?>
                    <li>
                        <a class="pagination-link is-current" aria-label="Page <?= $p ?>" aria-current="page">
                            <?= $p ?>
                        </a>
                    </li>
                <?php } else { ?>
                    <li>
                        <a class="pagination-link" aria-label="Goto page <?= $p ?>"
                            href="index.php?action=gallery&page=<?= $p ?>"><?= $p ?></a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </nav>
    <br />
<?php } ?>
<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>