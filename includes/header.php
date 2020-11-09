<?php
include("includes/config.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");

if (isset($_SESSION['userLoggedIn'])) {
    $userLoggedIn = $_SESSION['userLoggedIn'];
    echo "<script>userLoggedIn = '$userLoggedIn';</script>";
} else {
    header("Location: register.php");
}

?>

<html lang="en">
<head>
    <title>Music Player~~</title>

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
</head>
<body>

<!--<script>-->
<!--    const audioElement = new Audio();-->
<!--    audioElement.setTrack("assets/music/Loi-Yeu-Em-Vu.mp3");-->
<!--    audioElement.audio.play();-->
<!--</script>-->

<div id="mainContainer">

    <div id="topContainer">

        <?php include("includes/navBarContainer.php"); ?>

        <div id="mainViewContainer">

            <div id="mainContent">