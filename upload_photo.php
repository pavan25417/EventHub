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


$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES["profile_photo"])) {
    $fileTmpPath = $_FILES["profile_photo"]["tmp_name"];
    $fileName = $_FILES["profile_photo"]["name"];
    $fileSize = $_FILES["profile_photo"]["size"];
    $fileType = $_FILES["profile_photo"]["type"];

    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $newFileName = uniqid() . '.' . $fileExtension;
    $destination = $targetDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destination)) {
        // Save to DB
        $conn = new mysqli("localhost", "root", "", "login_register");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE email = ?");
        $stmt->bind_param("ss", $destination, $userEmail);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        header("Location: profile.php?uploaded=1");
        exit();
    } else {
        echo "Error moving the uploaded file.";
    }
} else {
    echo "No file uploaded.";
}
if (isset($_POST['remove'])) {
    // Fetch current photo path
    $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($profile_photo);
    $stmt->fetch();
    $stmt->close();

    // Delete the photo file (optional)
    if ($profile_photo && file_exists($profile_photo)) {
        unlink($profile_photo); // Delete the file
    }

    // Update DB to remove profile photo
    $stmt = $conn->prepare("UPDATE users SET profile_photo = NULL WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    exit();
}



$conn->close();
?>