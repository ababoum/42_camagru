<?php $title = "Camagru"; ?>

<?php ob_start(); ?>

<section class="hero is-primary">
    <div class="hero-body">
        <p class="title has-text-centered">
            Take a stylized picture with Camagru!
        </p>
    </div>
</section>

<section class="hero is-fullheight">
    <div class="hero-body">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <!-- Webcam Preview -->
                    <div class="box">
                        <div class="columns is-centered">
                            <div class="column is-half">
                                <!-- Webcam preview container -->
                                <div id="webcamPreviewContainer" class="mb-1">
                                    <video id="webcamPreviewVideo" width="100%" height="auto" autoplay
                                        playsinline></video>
                                </div>
                                <div class="columns is-centered is-vcentered">
                                    <div class="column is-flex is-justify-content-center">
                                        <button id="takeSnapshotButton" class="button is-primary">Take Snapshot</button>
                                    </div>
                                </div>
                                <div class="columns is-centered is-vcentered">
                                    <b class="has-text-centered">OR</b>
                                </div>
                                <div class="columns is-centered is-vcentered">
                                    <div class="column is-flex is-justify-content-center">
                                        <input type="file" id="imageUpload" accept="image/png"
                                            onchange="previewImage(event);updateImageInput(event)">
                                    </div>
                                </div>
                                <canvas id="snapshotCanvas" style="display: none;"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Snapshot Preview -->
                    <div class="box">
                        <div id="snapshotPreviewContainer" class="has-text-centered">
                            <h3 class="title is-5 has-text-centered">Snapshot preview</h3>
                            <div class="columns is-centered">
                                <div class="column is-half">
                                    <figure class="image is-4by3">
                                        <img id="snapshotPreviewImage" src="assets/empty.png" alt="Snapshot Preview">
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <!-- Sticker Selection -->
                    <div class="box">
                        <form id="myForm" action="index.php?action=save_shot" method="POST"
                            enctype="multipart/form-data">
                            <h3 class="title is-5 has-text-centered">Sticker selection</h3>
                            <div class="is-flex is-justify-content-space-between">
                                <?php foreach ($stickers as $sticker) { ?>
                                    <div>
                                        <label class="radio image-item" onclick="enableSubmit()">
                                            <div class="columns is-vcentered">
                                                <div class="column is-narrow">
                                                    <input type="radio" name="selectedSticker"
                                                        value="<?= $sticker->image_path ?>" required>
                                                </div>
                                                <div class="column">
                                                    <img width="50" height="50" src="<?= $sticker->image_path; ?>"
                                                        alt="<?= $sticker->title; ?>" class="image">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="field is-grouped is-grouped-centered">
                                <div class="control">
                                    <button type="submit" class="button is-primary" id="submitButton"
                                        disabled>Submit</button>
                                </div>
                            </div>
                            <input id="webcamImage" type="hidden" name="webcamImage" required>
                        </form>
                    </div>
                    <!-- PREVIEW OF POSTS -->
                    <div class="box">
                        <h3 class="title is-5 has-text-centered">Last pictures taken</h3>
                        <?php if (empty($posts)) { ?>
                            <p class="has-text-centered">No posts available.</p>
                        <?php } ?>
                        <?php foreach ($posts as $post) { ?>
                            <div class="box is-flex is-justify-content-center">
                                <div class="columns is-vcentered">
                                    <div class="column">
                                        <img src="<?= $post->image_path; ?>" alt="<?= $post->title; ?>" width="100"
                                            height="auto" />
                                    </div>
                                    <div class="column">
                                        <button class="button is-danger"
                                            onclick="window.location.href='index.php?action=delete_post&id=<?= $post->id; ?>&source=cam'">Delete</button>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    function enableSubmit() {
        document.getElementById('submitButton').disabled = false;
    }

    // Handle webcam preview and snapshot
    document.addEventListener('DOMContentLoaded', function () {
        var video = document.getElementById('webcamPreviewVideo');
        var canvas = document.getElementById('snapshotCanvas');
        var takeSnapshotButton = document.getElementById('takeSnapshotButton');
        var webcamImageInput = document.getElementById('webcamImage');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Access webcam
            navigator.mediaDevices.getUserMedia({
                video: true
            })
                .then(function (stream) {
                    // Display webcam preview
                    video.srcObject = stream;
                })
                .catch(function (error) {
                    console.log('Error accessing webcam:', error);
                });
        }

        // Handle snapshot capture
        takeSnapshotButton.addEventListener('click', function () {
            // Capture snapshot from the video stream
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert the canvas image to base64 data URL
            var imageDataURL = canvas.toDataURL('image/png');

            // Set the base64 image data as the value of the webcam image input field
            webcamImageInput.value = imageDataURL;

            // Display the snapshot preview image
            snapshotPreviewImage.src = imageDataURL;
            snapshotPreviewImage.style.display = 'block';

            // Console log the size of the snapshot (width and height in pixels)
            console.log('Snapshot size: ' + canvas.width + 'x' + canvas.height);
        });
    });

    // Handle preview fir uploaded images
    function previewImage(event) {
        const input = event.target;
        const snapshotPreviewImage = document.getElementById('snapshotPreviewImage');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                snapshotPreviewImage.src = e.target.result;
                snapshotPreviewImage.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Handle update of image input
    function updateImageInput(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('webcamImage').value = reader.result;
        };
        reader.readAsDataURL(file);
    }
</script>



<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>