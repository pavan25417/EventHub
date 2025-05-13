<?php
// Load .env for local dev (optional)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Environment values (Railway or fallback)
$host = getenv("MYSQLHOST") ?: "railway";
$db   = getenv("MYSQLDATABASE") ?: "login_register";
$user = getenv("MYSQLUSER") ?: "root";
$pass = getenv("MYSQLPASSWORD") ?: "123";
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$registration_success = false;
$registration_error = "";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $registration_number = $_POST['registration_number'];
        $phone_number = $_POST['phone_number'];
        $username_input = $_POST['username'];
        $password_raw = $_POST['password'];
        $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

        // Check if email or username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username_input]);

        if ($stmt->rowCount() > 0) {
            $registration_error = "Username or Email already exists!";
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, registration_number, phone_number, username, password)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $registration_number, $phone_number, $username_input, $hashed_password]);
            $registration_success = true;
        }
    }
} catch (PDOException $e) {
    $registration_error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <style>
        .success-message {
            color: green;
            font-weight: bold;
        }
    </style>
      <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #1f1c2c 0%, #8B00FF 100%);
            /* Space Purple and Black Gradient */
            color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.1);
            /* Transparent white */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 800px;
            /* Increased width for the two-column layout */
            text-align: center;
            backdrop-filter: blur(10px);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            margin-bottom: 20px;
            color: #fff;
            animation: slideInDown 1s ease;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        form {
            display: flex;
            flex-wrap: wrap;
            /* Allow wrapping of columns */
            justify-content: center;
            position: relative;
            animation: fadeIn 1s ease;
        }

        .form-column {
            flex: 1 1 45%;
            /* Each column takes 45% of the width */
            padding: 0 20px;
            /* Add some padding for spacing */
            max-width: 45%;
        }

        .divider {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 34%;
            width: 2px;
            background-color: #fff;
            animation: grow 1s ease-out 0.5s forwards;
            opacity: 0;
        }

        @keyframes grow {
            from {
                height: 0;
                opacity: 0;
            }

            to {
                height: 65%;
                opacity: 1;
            }
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #fff;
            text-align: left;
            /* Align labels to the left */
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #6A00CC;
            /* Darker shade of Space Purple */
            border-radius: 4px;
            font-size: 14px;
            background-color: rgba(255, 255, 255, 0.3);
            /* Semi-transparent white */
            color: #fff;
            width: 100%;
            /* Full width of the column */
            opacity: 0;
            animation: fadeIn 1s ease forwards;
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="tel"]::placeholder,
        input[type="password"]::placeholder {
            color: #e0e0e0;
        }

        .form-center {
            flex-basis: 100%;
            /* Center section takes full width */
            text-align: center;
            margin: 20px 0;
            /* Add margin for spacing */
            width: 100%;
        }

        button {
            padding: 15px 30px;
            /* Extended button size */
            background-color: #8B00FF;
            /* Space Purple */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #6A00CC;
            /* Darker shade of Space Purple */
            transform: scale(1.05);
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #fff;
        }

        .alert-danger {
            background-color: #dc3545;
        }

        .alert-success {
            background-color: #28a745;
        }

        a {
            color: #8B00FF;
            /* Space Purple */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .already-registered {
            color: #fff;
        }

        .background-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

    .background-square {
    width: 150px;
    height: 150px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    position: absolute;
    animation: float 10s infinite ease-in-out;
}

.background-square.square1 {
    width: 150px;
    height: 150px;
    top: 3%;
    left: -2%;
    animation-delay: 0s;
}

.background-square.square2 {
    width: 400px;
    height: 400px;
    top: 50%;
    left: 70%;
    animation-delay: 1s;
}

.background-square.square3 {
    width: 180px;
    height: 180px;
    top: 10%;
    left: 85%;
    animation-delay: 2s;
}

.background-square.square4 {
    width: 300px;
    height: 300px;
    top: 50%;
    left: 5%;
    animation-delay: 3s;
}

.background-square.square5 {
    width: 100px;
    height: 100px;
    top: 5%;
    left: 50%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }

    50% {
        transform: translateY(-20px) rotate(45deg);
    }
}

  
        <?php include "styles.css"; ?>
    </style>
</head>
<body>
<div class="background-shapes">
    <div class="background-square square1"></div>
    <div class="background-square square2"></div>
    <div class="background-square square3"></div>
    <div class="background-square square4"></div>
    <div class="background-square square5"></div>
</div>

<?php if ($registration_success): ?>
    <script>
        window.onload = function() {
            alert("Registration successful! Please login to continue.");
            window.location.href = "login.php";
        };
    </script>
<?php else: ?>
    <div class="container">
        <h2>Sign Up</h2>
        <?php if (!empty($registration_error)): ?>
            <div class="alert alert-danger"><?php echo $registration_error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-column">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <label for="mail">Email:</label>
                <input type="email" id="mail" name="email" placeholder="Enter your email" required>

                <label for="regno">Registration Number:</label>
                <input type="text" id="regno" name="registration_number" placeholder="Enter your registration number" required>
            </div>

            <div class="divider"></div>

            <div class="form-column">
                <label for="phno">Phone Number:</label>
                <input type="tel" id="phno" name="phone_number" placeholder="Enter your phone number" required>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter a password" required>
            </div>

            <div class="form-center">
                <button type="submit" name="submit">Sign up</button>
                <div class="already-registered">
                    <p>Already Registered? <a href="login.php">Login Here</a></p>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>
</body>
</html>
