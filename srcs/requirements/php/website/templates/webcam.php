<?php $title = "Camagru"; ?>

<?php ob_start(); ?>
<h1 class="title is-2 has-text-centered">Take a stylized picture with Camagru!</h1>


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
                                <div id="webcamPreviewContainer">
                                    <video id="webcamPreviewVideo" width="100%" height="auto" autoplay playsinline></video>
                                </div>
                                <div class="field is-grouped is-grouped-centered">
                                    <div class="control">
                                        <button id="takeSnapshotButton" class="button is-primary">Take Snapshot</button>
                                    </div>
                                </div>
                                <canvas id="snapshotCanvas" style="display: none;"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Snapshot Preview -->
                    <div class="box">
                        <div id="snapshotPreviewContainer" class="has-text-centered">
                            <h3 class="title is-5">Snapshot Preview</h3>
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
                    <!-- Image Selection List -->
                    <div class="box">
                        <form id="myForm" action="index.php?action=save_shot" method="POST" enctype="multipart/form-data">
                            <div class="columns is-multiline">
                                <!-- Image/Sticker Previews -->
                                <?php foreach ($stickers as $sticker) { ?>
                                    <div class="column is-one-third">
                                        <div class="box">
                                            <!-- Image/Sticker Preview -->
                                            <img width="250" height="250" src="<?= $sticker->image_path; ?>" alt="<?= $sticker->title; ?>" class="image" onclick="selectImage('<?= $sticker->image_path; ?>')">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <input id="webcamImage" type="hidden" name="webcamImage">
                            <input id="selectedImage" type="hidden" name="selectedImage">
                            <button type="submit" class="button is-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // JavaScript function to handle image selection
    function selectImage(imageUrl) {
        // Set the selected image value in the form input
        document.getElementById('selectedImage').value = imageUrl;
    }

    // JavaScript function to handle webcam preview and snapshot
    document.addEventListener('DOMContentLoaded', function() {
        var video = document.getElementById('webcamPreviewVideo');
        var canvas = document.getElementById('snapshotCanvas');
        var takeSnapshotButton = document.getElementById('takeSnapshotButton');
        var webcamImageInput = document.getElementById('webcamImage');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Access webcam
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    // Display webcam preview
                    video.srcObject = stream;
                })
                .catch(function(error) {
                    console.log('Error accessing webcam:', error);
                });
        }

        // Handle snapshot capture
        takeSnapshotButton.addEventListener('click', function() {
            // Capture snapshot from the video stream
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert the canvas image to base64 data URL
            var imageDataURL = canvas.toDataURL('image/png');

            // Set the base64 image data as the value of the webcam image input field
            webcamImageInput.value = imageDataURL;

            // Display the snapshot preview image
            snapshotPreviewImage.src = imageDataURL;
            snapshotPreviewImage.style.display = 'block';
        });
    });
</script>



<?php $content = ob_get_clean(); ?>

<?php require_once('header.php') ?>
<?php require('layout.php') ?>
<?php require_once('footer.php') ?>