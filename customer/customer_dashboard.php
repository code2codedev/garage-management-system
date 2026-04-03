<?php
session_start();
$page_title = "Customer Dashboard";
$role = "customer";
$dashboard_name = "CUSTOMER DASHBOARD";

include_once("../public/includes/db_connect.php");
include_once("../public/includes/header.php");
include_once("../public/includes/sidebar.php");
include_once("../public/includes/popup.php");
?>

<main class="main-content">
    <!-- Video Background Area -->
    <div class="video-border">
        <!-- Background video -->
        <video autoplay muted loop class="background-video">
            <source src="../public/images/c2.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Overlay welcome message -->
        <div class="welcome-message">
            <h1>Welcome, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!</h1>
        </div>
    </div>
</main>

<style>
/* Container aligned with content area */
.video-border {
    position: relative;
    width: 100%;                  /* fill full width of content area */
    height: calc(100vh - 129px);   /* fills space between header & footer */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;              /* prevents video overflow/duplicates */
    margin: 0;                     /* remove side gaps */
    padding: 0;
    box-sizing: border-box;
    border: 3px solid white;       /* keep visible until you’re happy with fit */
}

/* Background video fills container */
.background-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;             /* ensures video covers entire area */
    z-index: 0;
}

/* Overlay welcome message */
.welcome-message {
    position: relative;
    z-index: 1;
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #fff;                   /* white text for contrast */
    text-shadow: 2px 2px 6px rgba(0,0,0,0.7); /* improves readability */
}
</style>

<?php include_once("../public/includes/footer.php"); ?>