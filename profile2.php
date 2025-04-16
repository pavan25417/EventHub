<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login2.php");
  exit;
}

$username = $_SESSION['username'];
$conn = new mysqli('localhost', 'root', '', 'login_register');

// Fetch user information from database
$sql = "SELECT * FROM club_heads WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
  <title>Profile - <?php echo $username; ?></title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <style>
    body {
      background-color: black;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 40px 20px;
      color: #ba55c9;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h1 {
      color: #ba55c9;
      font-size: 2.5em;
      margin-bottom: 30px;
    }

    .profile-container {
      background-color: #1a1a1a;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(186, 85, 201, 0.4);
      width: 100%;
      max-width: 500px;
      text-align: center;
    }

    p {
      font-size: 1.1em;
      margin-bottom: 15px;
      color: #e0b0ff;
    }

    .btn-container {
      margin-top: 30px;
      display: flex;
      justify-content: space-between;
      gap: 20px;
    }

    .btn {
      background-color: transparent;
      color: #ba55c9;
      border: 2px solid #ba55c9;
      border-radius: 8px;
      padding: 10px 20px;
      font-size: 1em;
      cursor: pointer;
      flex: 1;
      transition: all 0.3s ease;
      text-align: center;
      text-decoration: none;
    }

    .btn:hover {
      background-color: #ba55c9;
      color: white;
    }

    .btn:focus {
      outline: none;
    }
  </style>
</head>

<body>

  <h1>Welcome, <?php echo $username; ?>!</h1>

  <div class="profile-container">
    <?php if ($user): ?>
      <p>Email: <?php echo $user['email']; ?></p>
      <!-- Add more user info here -->
    <?php else: ?>
      <p>An error occurred while fetching user information.</p>
    <?php endif; ?>

    <div class="btn-container">
      <form action="login2.php" method="POST" style="margin: 0;">
        <input type="submit" value="Logout" class="btn">
      </form>
      <a href="index4.php" class="btn">Home</a>
    </div>
  </div>

</body>

</html>