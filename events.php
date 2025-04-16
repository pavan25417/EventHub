<?php
session_start(); // Start session if not already started

// Database connection details
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

// SQL query to fetch event data
$sql = "SELECT * FROM event_registrations"; // Assuming events are stored in an 'event_registrations' table
$result = $conn->query($sql);

// Collect event data
$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <link rel="stylesheet" href="eventstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* === RESET === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        /* === BASE STYLES === */
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 0% 50%, #282828 23.5%, transparent 0) 21px 30px,
                radial-gradient(circle at 0% 50%, #2c3539 24%, transparent 0) 19px 30px,
                linear-gradient(#282828 14%, transparent 0, transparent 85%, #282828 0) 0 0,
                linear-gradient(150deg, #282828 24%, #2c3539 0, #2c3539 26%, transparent 0, transparent 74%, #2c3539 0, #2c3539 76%, #282828 0) 0 0,
                linear-gradient(30deg, #282828 24%, #2c3539 0, #2c3539 26%, transparent 0, transparent 74%, #2c3539 0, #2c3539 76%, #282828 0) 0 0,
                linear-gradient(90deg, #2c3539 2%, #282828 0, #282828 98%, #2c3539 0%) 0 0 #282828;
            background-size: 40px 60px;
        }

        /* === HEADER & NAVBAR === */
        header {
            position: sticky;
            top: 3rem;
            width: 90%;
            margin: 0 auto;
            background-color: black;
            border-radius: 100px;
            box-shadow: 0px 0px 50px #8B00FF;
            z-index: 2;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .logo h1 {
            font-size: 2rem;
            color: #8B00FF;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            list-style: none;
            margin-left: auto;
        }

        .nav-links li {
            margin-left: 2rem;
        }

        .nav-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .nav-links a:hover {
            color: #8B00FF;
        }

        .nav-links .button {
            background: #8B00FF;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: 0.3s ease;
        }

        .nav-links .button:hover {
            background: #6A00CC;
            transform: scale(1.05);
        }

        /* === SEARCH BAR === */
        #searchInput {
            margin: 0 14rem;
            width: 70%;
            padding: 0.5rem;
            border: none;
            border-radius: 100px;
            background-color: #4a2d4a;
            color: white;
            font-size: large;
        }
        
        #searchInput::placeholder {
            color: #fff;
        }

        #searchInput:focus {
            outline: none;
            box-shadow: 0px 0px 5px #8B00FF;
            background: rgba(35, 35, 35, 0.9);
        }

        /* === SLIDE NAVIGATION === */
        .slide-nav,
        .filter-section {
            display: flex;
            justify-content: center;
            margin: 5rem 0;
            flex-wrap: wrap;
        }

        .slide-nav-button,
        .filter-button {
            background: #8B00FF;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .slide-nav-button:hover,
        .filter-button:hover {
            background: #6A00CC;
            transform: scale(1.05);
        }

        .slide-nav-button.active {
            background: #6A00CC;
        }

        /* === EVENT CARD === */
        .slides-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 3rem;
            margin-top: 15px;
            justify-content: center;
        }

        .event-card {
            width: 100%;
            max-width: 450px;
            background: rgba(6, 6, 6, 0.8);
            padding: 2rem;
            border-radius: 25% 5px / 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease;
        }

        .event-card:hover {
            box-shadow: 10px 10px 5px #8B00FF;
        }

        .event-card h3 {
            color: #e0e0e0;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .event-card p {
            color: #b0b0b0;
        }

        .venue-time {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .event-card .button {
            background: #8B00FF;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s ease;
            display: inline-block;
        }

        .event-card .button:hover {
            background: #6A00CC;
            transform: scale(1.05);
        }

        .event-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #ffffff;
            text-shadow: 0 0 5px #8B00FF;
        }

        .event-description {
            font-size: 1rem;
            color: #c0c0c0;
            margin-bottom: 1rem;
            font-style: italic;
        }

        .venue-time {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .event-info {
            display: flex;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .event-info .label {
            font-weight: bold;
            color: #8B00FF;
        }

        .event-info .value {
            color: #e0e0e0;
        }

        .register-form {
            display: flex;
            justify-content: center;
        }

        .register-form .button {
            background: linear-gradient(to right, #8B00FF, #5e00c8);
            border: none;
            padding: 0.7rem 1.5rem;
            font-size: 1rem;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .register-form .button:hover {
            background: linear-gradient(to right, #9d4edd, #7b2cbf);
            transform: scale(1.05);
            box-shadow: 0 0 10px #8B00FF;
        }


        /* === FOOTER === */
        .footer {
            background: rgba(30, 30, 30, 0.8);
            padding: 2rem 0;
            text-align: center;
            border-radius: 10px;
            margin-top: auto;
        }

        .footer ul {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
        }

        .item a {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #252627;
            border: 3px solid #141414;
            overflow: hidden;
            position: relative;
            text-decoration: none;
        }

        .item a::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: var(--bg-color);
            scale: 1 0;
            transform-origin: bottom;
            transition: scale 0.5s ease;
            z-index: 0;
        }

        .item:hover a::before {
            scale: 1 1;
        }

        .icon {
            font-size: 1.5rem;
            color: hsl(321, 89%, 42%);
            transition: 0.5s ease;
            z-index: 2;
        }

        .item a:hover .icon {
            color: #fff;
            transform: rotateY(360deg);
        }

        .item:nth-child(1) {
            --bg-color: linear-gradient(to bottom right, #f9ce34, #ee2a7b, #6228d7);
        }

        .item:nth-child(2) {
            --bg-color: #0077b5;
        }

        .item:nth-child(3) {
            --bg-color: #ff0000;
        }

        .item:nth-child(4) {
            --bg-color: #000;
        }

        .item:nth-child(5) {
            --bg-color: blue;
        }

        .item:nth-child(6) {
            --bg-color: #000;
        }

        /* === BACK TO TOP === */
        .btt {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            background-color: #8B00FF;
            color: black;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            z-index: 99;
            transition: 0.3s ease;
        }

        .btt:hover {
            background-color: #b474e9;
        }

        /* === MEDIA QUERIES === */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
            }

            .nav-links {
                flex-direction: column;
                background: rgba(0, 0, 0, 0.9);
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                height: 100vh;
                display: none;
                padding: 1em 0;
                gap: 1.5em;
            }

            .navbar.active .nav-links {
                display: flex;
            }

            .burger {
                display: block;
                cursor: pointer;
            }

            .burger div {
                width: 25px;
                height: 3px;
                background: #fff;
                margin: 5px;
                transition: 0.3s ease;
            }
        }

        @media (max-width: 480px) {

            .slide,
            .event-card {
                width: 90%;
            }

            #searchInput {
                width: 90%;
            }

            .filter-button {
                padding: 0.3rem 0.7rem;
                font-size: 0.8rem;
            }
        }
    </style>
    <script>
        function showAlert(message) {
            alert(message);
        }

        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status === 'success') {
                showAlert('Event registered successfully.');
            } else if (status === 'already_registered') {
                showAlert('You have already registered for this event.');
            } else if (status === 'error') {
                showAlert('Error registering event.');
            }
        };
    </script>
</head>

<body class="container">
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>EventHub</h1>
                <hr class="animated" />
            </div>
            <ul class="nav-links">
                <li><a href="index2.php ">Home</a></li>
                <li><a href="cclist.html">Clubs&Chapters</a></li>
                <li><a href="registered_events.php">Registered Events</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="events" class="events-section">
            <div class="filter-section">
                <button class="filter-button" data-filter="all">All</button>
                <button class="filter-button" data-filter="workshop">Workshops</button>
                <button class="filter-button" data-filter="conference">Conferences</button>
                <button class="filter-button" data-filter="seminar">Seminars</button>
            </div>
            <div>
                <input type="text" id="searchInput" placeholder="Search events...">

            </div>

            <div class="slides-container">
                <?php
                if (!empty($events)) {
                    foreach ($events as $row) {
                        $event_id = htmlspecialchars($row['id']);
                        $event_name = htmlspecialchars($row['event_name']);
                        ?>
                        <div class="event-card">
                            <div class="event-header">
                                <h3 class="event-title"><?php echo htmlspecialchars($row['event_name']); ?></h3>
                                <p class="event-description"><?php echo htmlspecialchars($row['event_description']); ?></p>
                            </div>

                            <div class="event-details">
                                <div class="detail event-venue"><span>📍 Venue:</span>
                                    <?php echo htmlspecialchars($row['venue']); ?></div>
                                <div class="detail"><span>📅 Date:</span> <?php echo htmlspecialchars($row['event_date']); ?>
                                </div>
                                <div class="detail event-time"><span>⏰ Time:</span>
                                    <?php echo htmlspecialchars($row['event_time']); ?></div>
                            </div>

                            <form action="register_event.php" method="POST" class="register-form">
                                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <button type="submit" class="register-button">Register Now</button>
                            </form>
                        </div>



                        <?php
                    }
                } else {
                    echo "<p>No events found.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <!-- <section class="footer">
        <ul>
            <li class="item">
                <a href="#">
                    <i class="fa-brands fa-instagram icon"></i>
                </a>
            </li>
            <li class="item">
                <a href="#">
                    <i class="fa-brands fa-linkedin icon"></i>
                </a>
            </li>
            <li class="item">
                <a href="#">
                    <i class="fa-brands fa-youtube icon"></i>
                </a>
            </li>
            <li class="item">
                <a href="#">
                    <i class="fa-brands fa-x-twitter icon"></i>
                </a>
            </li>
            <li class="item">
                <a href="#">
                    <i class="fa-brands fa-facebook icon"></i>
                </a>
            </li>
            <li class="item">
                <a href="#">
                    <i class="fa-brands fa-github icon"></i>
                </a>
            </li>
        </ul>
        <div class="name">
            <ul>
                <a href="index.html">Home</a>
                <a href="event.html">Events</a>
                <a href="cclist.html">Clubs&Chapters</a>
            </ul>
        </div>
    </section> -->
    <button class="btt" id="backToTop" onclick="topFunction()"><i class="fas fa-arrow-up"></i></button>

    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            let filter = this.value.toLowerCase();
            let eventCards = document.querySelectorAll('.event-card');

            eventCards.forEach(function (card) {
                let title = card.querySelector('.event-title').textContent.toLowerCase();
                let description = card.querySelector('.event-description').textContent.toLowerCase();
                let venue = card.querySelector('.event-venue').textContent.toLowerCase();
                let time = card.querySelector('.event-time').textContent.toLowerCase();

                if (title.includes(filter) || description.includes(filter) || venue.includes(filter) || time.includes(filter)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>