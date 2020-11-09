<?php include("includes/header.php");

if (isset($_GET['id'])) {
    $albumId = $_GET['id'];
} else {
    header("Location: index.php");
}

$album = new Album($connection, $albumId);

$artist = $album->getArtist();

?>

<div class="entityInfo">

    <div class="leftSection">
        <img src="<?php echo $album->getArtworkPath(); ?>" alt="Album Artwork">
    </div>

    <div class="rightSection">
        <h2><?php echo $album->getTitle(); ?></h2>
        <p>By <?php echo $artist->getName(); ?></p>
        <p><?php echo $album->getNumberOfSongs(); ?> song(s)</p>
    </div>

</div>

<div class="trackListContainer">

    <ul class="trackList">

        <?php

            $songIdArray = $album->getSongId();

            $i = 1;
            foreach($songIdArray as $songId) {
                $albumSong = new Song($connection, $songId);
                $albumArtist = $albumSong->getArtist();

                echo "<li class='trackListRow'>
                    <div class='trackCount'>
                        <img class='play' src='assets/images/icons/play_50px.png' alt='play button' style='width: 20px; visibility: hidden; position: absolute; cursor: pointer'>
                        <span class='trackNumber'>$i</span>
                    </div>
                    
                    <div class='trackInfo'>
                        <span class='trackName'>" . $albumSong->getTitle() . "</span>
                        <span class='artistName'>" . $albumArtist->getName() . "</span>
                    </div>
                    
                    <div class='trackOption'>
                        <img class='optionButton' src='assets/images/icons/' alt='Option Button' style='width: 15px; visibility: hidden'>
                    </div>
                    
                    <div class='trackDuration'>
                        <span class='duration'>" . $albumSong->getDuration() . "</span>
                    </div>
                    
                </li>";

                $i++;
            }

        ?>

    </ul>

</div>

<?php include("includes/footer.php"); ?>
