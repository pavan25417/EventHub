<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION["user"];

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_register";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data including profile photo
$stmt = $conn->prepare("SELECT name, email, registration_number, phone_number, profile_photo FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($name, $email, $regno, $phno, $profile_photo);

if ($stmt->fetch()) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Page</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
        <style>
            /* Basic CSS Reset */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            /* General Styles */
            body {
                font-family: 'Roboto', sans-serif;
                line-height: 1.6;
                background-color: #121212;
                color: #e0e0e0;
                overflow-x: hidden;
                /* Ensure no horizontal scrollbars appear */
                margin: 0;
                padding: 0;
            }

            header {
                position: sticky;
                top: 0;
                background-color: #121212;
                padding: 1rem 0;
                z-index: 1000;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }

            .navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 90%;
                margin: 0 auto;
            }

            .logo h1 {
                font-size: 2rem;
                color: #8B00FF;
                /* Space Purple */
                font-weight: bold;
            }

            .nav-links {
                list-style: none;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .nav-links li {
                margin-left: 2rem;
            }

            .nav-links a {
                color: #e0e0e0;
                text-decoration: none;
                font-weight: bold;
                transition: color 0.3s ease;
            }

            .nav-links a:hover {
                color: #6A00CC;
                /* Darker shade of Space Purple */
            }

            .nav-links .button {
                padding: 0.5rem 1rem;
                background: #8B00FF;
                /* Space Purple */
                border-radius: 5px;
                transition: background 0.3s ease, transform 0.3s ease;
            }

            .nav-links .button:hover {
                background: #6A00CC;
                /* Darker shade of Space Purple */
                transform: scale(1.05);
            }

            .container {
                width: 80%;
                max-width: 800px;
                margin: 2rem auto;
                background-color: #282828;
                padding: 2rem;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            }

            h2 {
                text-align: center;
                margin-bottom: 20px;
                color: #e0e0e0;
            }

            .profile-details {
                list-style-type: none;
                padding: 0;
            }

            .profile-details li {
                margin-bottom: 10px;
                color: #b0b0b0;
            }

            .profile-details span {
                font-weight: bold;
                color: #e0e0e0;
            }

            @media (max-width: 768px) {
                .container {
                    width: 90%;
                    max-width: 500px;
                }
            }

            @media (max-width: 480px) {
                .container {
                    width: 90%;
                    max-width: 300px;
                }
            }

            .btt {
                display: none;
                position: fixed;
                bottom: 20px;
                right: 30px;
                z-index: 99;
                border: none;
                outline: none;
                background-color: #8B00FF;
                /* Space Purple */
                color: #000;
                cursor: pointer;
                padding: 15px;
                border-radius: 10px;
                transition: background-color 0.3s ease;
            }

            .btt:hover {
                background-color: #b474e9;
                /* Lighter shade on hover */
            }

            .profile-photo {
                position: relative;
                width: 150px;
                height: 150px;
                border-radius: 50%;
                overflow: hidden;
                background-color: #f4f4f4;
                margin: 0 auto;
                /* centers container horizontally */
                border: 2px solid #8B00FF;
                /* Space Purple */
                display: flex;
                justify-content: center;
                align-items: center;
            }

            #preview-photo {
                max-width: 100%;
                max-height: 100%;
                object-fit: cover;
                display: block;
            }


            .photo-upload-label {
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative;
                width: 100%;
                height: 100%;
                cursor: pointer;
            }

            .photo-upload-label input[type="file"] {
                display: none;
            }



            .overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                /* horizontal center */
                align-items: center;
                /* vertical center */
                background-color: rgba(0, 0, 0, 0.4);
                opacity: 0;
                transition: opacity 0.3s ease;
                color: white;
                font-size: 2rem;
                pointer-events: none;
            }

            .photo-upload-label:hover .overlay {
                opacity: 1;
                pointer-events: auto;
            }


            .overlay:hover {
                background-color: rgba(0, 0, 0, 0.8);
            }

            .overlay i {
                color: #fff;
                font-size: 1.5rem;
            }

            .avatar-selection {
                text-align: center;
                margin-top: 20px;
            }

            .avatars {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 20px;
                margin-top: 10px;
            }

            .avatar-option {
                position: relative;
                cursor: pointer;
            }

            .avatar-option input[type="radio"] {
                display: none;
                /* Hide the radio input */
            }

            .avatar-option img {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                border: 2px solid transparent;
                transition: border-color 0.3s ease;
            }

            .avatar-option input[type="radio"]:checked+img {
                border-color: #8B00FF;
                /* Highlight selected avatar */
            }
        </style>
    </head>

    <body>
        <header>
            <div class="navbar">
                <div class="logo">
                    <h1>Dashboard</h1>
                </div>
                <ul class="nav-links">
                    <li><a href="index2.php">Home</a></li>
                    <li><a href="logout.php" class="button">Logout</a></li>
                </ul>
            </div>
        </header>

        <main>
            <section class="container">
                <h2>Profile Page</h2>
                <form action="upload_photo.php" method="post" enctype="multipart/form-data">
                    <ul class="profile-details">
                        <div class="profile-photo">
                            <label for="photo-upload" class="photo-upload-label">
                                <input type="file" id="photo-upload" name="profile_photo" accept="image/*">
                                <?php if ($profile_photo): ?>
                                    <img id="preview-photo" src="<?= htmlspecialchars($profile_photo) ?>" alt="Profile Photo">
                                <?php else: ?>
                                    <img id="preview-photo" src="default_profile_photo.jpg" alt="Profile Photo">
                                <?php endif; ?>
                                <div class="overlay">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </label>
                        </div>
                        <br><br>
                        <li><span>Name:</span> <?= htmlspecialchars($name) ?></li>
                        <li><span>Email:</span> <?= htmlspecialchars($email) ?></li>
                        <li><span>Registration Number:</span> <?= htmlspecialchars($regno) ?></li>
                        <li><span>Phone Number:</span> <?= htmlspecialchars($phno) ?></li>
                    </ul>

                    <?php if ($profile_photo): ?>
                        <button type="submit" name="remove" id="remove-btn">Remove Profile Photo</button>
                    <?php endif; ?>


                </form>

            </section>
        </main>

        <button class="btt" onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Back to top button functionality
            window.onscroll = function () {
                scrollFunction();
            };

            function scrollFunction() {
                document.getElementById("myBtn").style.display =
                    document.body.scrollTop > 20 || document.documentElement.scrollTop > 20
                        ? "block"
                        : "none";
            }

            function topFunction() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            }

            const photoUpload = document.getElementById('photo-upload');
            const previewPhoto = document.getElementById('preview-photo');
            const overlayIcon = document.querySelector('.overlay i');
            const defaultPhoto = "default_profile_photo.jpg";
            const originalPhoto = previewPhoto.src;

            // Click on overlay icon to open file picker
            overlayIcon.addEventListener('click', function (e) {
                e.preventDefault();
                photoUpload.click();
            });

            // Auto submit form after selecting a photo
            photoUpload.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    this.form.submit();
                }
            });

            const uploadBtn = document.getElementById('upload-btn');
            const removeBtn = document.getElementById('remove-btn');

            // Preview logic (if you're still using it)
            if (photoUpload) {
                photoUpload.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewPhoto.src = e.target.result;
                            if (uploadBtn) uploadBtn.style.display = 'none';
                            if (removeBtn) removeBtn.style.display = 'inline-block';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    photoUpload.value = '';
                    previewPhoto.src = defaultPhoto;
                    if (uploadBtn) uploadBtn.style.display = 'inline-block';
                    removeBtn.style.display = 'none';
                });
            }

            // Show SweetAlert on successful upload
            <?php if (isset($_GET['uploaded']) && $_GET['uploaded'] === "1"): ?>
                window.onload = function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Profile photo uploaded successfully.',
                        confirmButtonColor: '#8B00FF'
                    });
                };
            <?php endif; ?>
        </script>




    </body>

    </html>

    <?php
} else {
    echo "<p class='message'>No user found with the provided email.</p>";
}

$stmt->close();
$conn->close();
?>