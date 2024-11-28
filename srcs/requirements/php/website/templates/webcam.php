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
            <div class="columns is-desktop">
                <div class="column is-half-desktop">
                    <!-- Webcam Preview -->
                    <div class="box">
                        <div class="columns is-centered">
                            <div class="column">
                                <!-- Webcam preview container -->
                                <div id="webcamPreviewContainer" class="mb-1" style="position: relative;">
                                    <video id="webcamPreviewVideo" width="100%" height="auto" autoplay playsinline></video>
                                    <canvas id="overlayCanvas" style="position: absolute; top: 0; left: 0;"></canvas>
                                </div>
                                <div class="columns is-centered is-vcentered">
                                    <div class="column is-flex is-justify-content-center">
                                        <button id="takeSnapshotButton" class="button is-primary is-fullwidth">Take Snapshot</button>
                                    </div>
                                </div>
                                <div class="columns is-centered is-vcentered">
                                    <b class="has-text-centered">OR</b>
                                </div>
                                <div class="columns is-centered is-vcentered">
                                    <div class="column is-flex is-justify-content-center">
                                        <input type="file" id="imageUpload" accept="image/png, image/jpeg" onchange="previewImage(event);updateImageInput(event)" class="is-fullwidth">
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
                                <div class="column">
                                    <figure class="image is-4by3">
                                        <img id="snapshotPreviewImage" src="assets/empty.png" alt="Snapshot Preview" class="is-responsive">
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column is-half-desktop">
                    <!-- Sticker Selection -->
                    <div class="box">
                        <form id="myForm" action="index.php?action=save_shot" method="POST" enctype="multipart/form-data">
                            <h3 class="title is-5 has-text-centered">Sticker selection</h3>
                            <div class="is-flex is-flex-wrap-wrap is-justify-content-space-around">
                                <?php foreach ($stickers as $sticker) { ?>
                                    <div class="m-2">
                                        <label class="radio image-item" onclick="enableSubmit()">
                                            <div class="columns is-vcentered">
                                                <div class="column is-narrow">
                                                    <input type="radio" name="selectedSticker" value="<?= $sticker->image_path ?>" required>
                                                </div>
                                                <div class="column">
                                                    <img width="50" height="50" src="<?= $sticker->image_path; ?>" alt="<?= $sticker->title; ?>" class="image is-responsive">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="field is-grouped is-grouped-centered mt-4">
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth" id="submitButton" disabled>Submit</button>
                                </div>
                            </div>
                            <input id="webcamImage" type="hidden" name="webcamImage" required>
                            <input id="stickerX" type="hidden" name="stickerX" value="0">
                            <input id="stickerY" type="hidden" name="stickerY" value="0">
                            <input id="stickerSize" type="hidden" name="stickerSize" value="0">
                            <input id="canvasWidth" type="hidden" name="canvasWidth">
                            <input id="canvasHeight" type="hidden" name="canvasHeight">
                        </form>
                    </div>
                    <!-- PREVIEW OF POSTS -->
                    <div class="box">
                        <h3 class="title is-5 has-text-centered">Last pictures taken</h3>
                        <div class="field is-grouped is-grouped-centered mb-4">
                            <div class="control">
                                <input type="number" id="numPictures" class="input is-small" min="1" max="20" value="5" style="width: 100px;">
                            </div>
                            <div class="control">
                                <button class="button is-small" onclick="updatePicturesCount()">Update</button>
                            </div>
                            <div class="control">
                                <button class="button is-small" onclick="showAllPictures()">MAX</button>
                            </div>
                        </div>

                        <?php if (empty($posts)) { ?>
                            <p class="has-text-centered">No posts available.</p>
                        <?php } ?>
                        <?php 
                        $numPictures = isset($_GET['num']) ? min((int)$_GET['num'], 20) : 5;
                        foreach (array_slice($posts, 0, $numPictures) as $post) { ?>
                            <div class="box is-flex is-justify-content-center">
                                <div class="columns is-vcentered is-mobile">
                                    <div class="column">
                                        <img src="<?= $post->image_path; ?>" alt="<?= $post->title; ?>" style="width: 150px;" />
                                    </div>
                                    <div class="column">
                                        <button class="button is-danger is-fullwidth" onclick="window.location.href='index.php?action=delete_post&id=<?= $post->id; ?>&source=cam'">Delete</button>
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
    let isDragging = false;
    let currentX = 0;
    let currentY = 0;
    let initialX = 0;
    let initialY = 0;
    let stickerImage = null;
    let stickerSize = 0;

    // Add these event listeners after creating the overlay canvas
    const overlayCanvas = document.getElementById('overlayCanvas');
    overlayCanvas.style.cursor = 'move';

    overlayCanvas.addEventListener('mousedown', startDragging);
    overlayCanvas.addEventListener('mousemove', drag);
    overlayCanvas.addEventListener('mouseup', stopDragging);

    function startDragging(e) {
        initialX = e.clientX - currentX;
        initialY = e.clientY - currentY;
        isDragging = true;
    }

    function drag(e) {
        if (isDragging) {
            e.preventDefault();
            currentX = e.clientX - initialX;
            currentY = e.clientY - initialY;
            drawSticker(); // Redraw sticker at new position
        }
    }

    function stopDragging() {
        isDragging = false;
    }

    function drawSticker() {
        const ctx = overlayCanvas.getContext('2d');
        ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
        
        if (stickerImage) {
            // Calculate relative positions (0-1)
            const relativeX = currentX / overlayCanvas.width;
            const relativeY = currentY / overlayCanvas.height;
            const relativeStickerSize = stickerSize / overlayCanvas.width;
            
            // Draw sticker at current position
            ctx.drawImage(stickerImage, currentX, currentY, stickerSize, stickerSize);
            
            // Update hidden form fields with relative values
            document.getElementById('stickerX').value = Math.max(0, Math.min(1, relativeX));
            document.getElementById('stickerY').value = Math.max(0, Math.min(1, relativeY));
            document.getElementById('stickerSize').value = Math.max(0, Math.min(1, relativeStickerSize));
            document.getElementById('canvasWidth').value = document.getElementById('snapshotCanvas').width;
            document.getElementById('canvasHeight').value = document.getElementById('snapshotCanvas').height;

        }
    }


    let hasImage = false;
    let hasSticker = false;

    function enableSubmit() {
        hasSticker = true;
        updateSubmitButton();
        
        const selectedSticker = document.querySelector('input[name="selectedSticker"]:checked');
        const video = document.getElementById('webcamPreviewVideo');
        const overlayCanvas = document.getElementById('overlayCanvas');
        
        overlayCanvas.width = video.offsetWidth;
        overlayCanvas.height = video.offsetHeight;
        
        if (selectedSticker) {
            stickerImage = new Image();
            stickerImage.src = selectedSticker.value;
            stickerSize = video.offsetWidth * 0.3;
            
            stickerImage.onload = function() {
                // Initial center position
                currentX = (overlayCanvas.width - stickerSize) / 2;
                currentY = (overlayCanvas.height - stickerSize) / 2;
                
                // Update hidden form fields with initial position
                document.getElementById('stickerX').value = currentX;
                document.getElementById('stickerY').value = currentY;
                document.getElementById('stickerSize').value = stickerSize;
                
                drawSticker();
            };
        }
    }


    // Add resize handler to keep overlay aligned
    window.addEventListener('resize', function() {
        const video = document.getElementById('webcamPreviewVideo');
        const overlayCanvas = document.getElementById('overlayCanvas');
        overlayCanvas.width = video.offsetWidth;
        overlayCanvas.height = video.offsetHeight;
    });

    function updateSubmitButton() {
        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = !(hasImage && hasSticker);
    }

    // Handle webcam preview and snapshot
    document.addEventListener('DOMContentLoaded', function () {
        var video = document.getElementById('webcamPreviewVideo');
        var canvas = document.getElementById('snapshotCanvas');
        var takeSnapshotButton = document.getElementById('takeSnapshotButton');
        var webcamImageInput = document.getElementById('webcamImage');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Access webcam
            navigator.mediaDevices.getUserMedia({ video: true })
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
            hasImage = true;
            updateSubmitButton();
            // Console log the size of the snapshot (width and height in pixels)
            console.log('Snapshot size: ' + canvas.width + 'x' + canvas.height);
        });
    });

    // Handle preview for uploaded images
    function previewImage(event) {
        const input = event.target;
        const snapshotPreviewImage = document.getElementById('snapshotPreviewImage');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                snapshotPreviewImage.src = e.target.result;
                snapshotPreviewImage.style.display = 'block';
                hasImage = true;
                updateSubmitButton();
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

    function updatePicturesCount() {
        const num = document.getElementById('numPictures').value;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('num', num);
        window.location.href = currentUrl.toString();
    }

    function showAllPictures() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('num', <?= count($posts) ?>);
    window.location.href = currentUrl.toString();
    }

</script>

<style>
    .image.is-responsive {
        max-width: 100%;
        height: auto;
    }

    @media screen and (max-width: 768px) {
        .button, input[type="file"] {
            padding: 0.5em 1em;
            font-size: 1.1em;
        }

        .section {
            padding: 1.5rem 1rem;
        }

        .box {
            margin-bottom: 1rem;
        }
    }
</style>

<?php $content = ob_get_clean(); ?>
<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>
