<?php

$songQuery = mysqli_query($connection, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");

$resultArray = array();

while ($row = mysqli_fetch_array($songQuery)) {
    array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);

?>


<script>

    let repeat = false;
    let shuffle = false;
    let mouseDown = false;
    let audioElement = null;

    //Start this as soon as the page loads
    $(document).ready(() => {
        currentPlaylist = <?php echo $jsonArray; ?>;

        //Create an audio element from class Audio in script.js
        audioElement = new Audio();

        audioElement.audio.addEventListener("canplay", () => {
            //'this' refers to the object that the event was called on
            let duration = formatTime(audioElement.audio.duration);
            $(".progressTime.remaining").text(duration);
        });

        audioElement.audio.addEventListener("timeupdate", () => {
            if (audioElement.audio.duration) {
                updateTimeProgressBar(audioElement.audio);
            }
        });

        setTrack(currentPlaylist[0], currentPlaylist, false);

        updateVolumeProgressBar(audioElement.audio);

        //Prevent users from highlighting the icons
        $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", (e) => {
            e.preventDefault();
        })



        const playbackBar = $(".playbackBar .progressBar");

        playbackBar.mousedown(() => {
            mouseDown = true;
        });

        playbackBar.mousemove((e) => {
            if (mouseDown) {
                //Set time of song depending on position of mouse
                timeFromOffset(e, this);
            }
        });

        playbackBar.mouseup((e) => {
            timeFromOffset(e, this);
        });


        const volumeBar = $(".volumeBar .progressBar");

        volumeBar.mousedown(() => {
            mouseDown = true;
        });

        volumeBar.mousemove((e) => {
            if (mouseDown) {
                const percentage = e.offsetX / $(this).width();
                if (percentage >= 0 && percentage <= 1) {
                    audioElement.audio.volume = percentage * 10;
                }
            }
        });

        volumeBar.mouseup((e) => {
            const percentage = e.offsetX / $(this).width();
            if (percentage >= 0 && percentage <= 1) {
                audioElement.audio.volume = percentage * 10;
            }
        });

        $(document).mouseup(() => {
            mouseDown = false;
        });
    });

    function timeFromOffset(mouse, progressBar) {
        const percentage = mouse.offsetX / $(progressBar).width() * 100;
        const seconds = audioElement.audio.duration * (percentage / 100);
        audioElement.setTime(seconds);
    }

    function prevSong() {
        if (audioElement.audio.currentTime >= 3 || currentIndex === 0) {
            audioElement.setTime(0);
        } else {
            currentIndex--;
            setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
        }
    }

    function nextSong() {

        if (repeat) {
            audioElement.setTime(0);
            playSong();
            return;
        }


        if (currentIndex === currentPlaylist.length - 1) {
            currentIndex = 0;
        } else {
            currentIndex++;
        }

        let trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
        setTrack(trackToPlay, currentPlaylist, true);
    }

    function setRepeat() {
        repeat = !repeat;
        let imageName = repeat ? "repeat_50px.png" : "repeat_50px.png";
        $(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
    }

    function setMute() {
        audioElement.audio.muted = !audioElement.audio.muted;
        let imageName = audioElement.audio.muted ? "mute_50px.png" : "sound_50px.png";
        $(".controlButton.volume img").attr("src", "assets/images/icons/" + imageName);
    }

    function setShuffle() {
        shuffle = !shuffle;
        let imageName = shuffle ? "shuffle_50px.png" : "shuffle_50px.png";
        $(".controlButton.shuffle img").attr("src", "assets/images/icons/" + imageName);


        if (shuffle) {
            //randomize Playlist
            shuffleArray(shufflePlaylist);
            currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
        } else {
            //Go back to regular playlist
            currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
        }
    }

    function shuffleArray(a) {
        let j, x, i;
        for (i = a.length; i ; i - 1) {
            j = Math.floor(Math.random() * i);
            x = a[i - 1];
            a[i - 1] = a[j];
            a[j] = x;
        }
    }

    function setTrack(trackId, newPlaylist, play) {


        if (newPlaylist !== currentPlaylist) {
            currentPlaylist = newPlaylist;
            shufflePlaylist = currentPlaylist.slice();
            shuffleArray(shufflePlaylist);
        }

        if (shuffle) {
            currentIndex = shufflePlaylist.indexOf(trackId);
        } else {
            currentIndex = currentPlaylist.indexOf(trackId);
        }
        pauseSong();

        $.post("includes/handlers/ajax/getSongJson.php", {songId: trackId}, function (data) {

            const track = JSON.parse(data);

            $(".trackName span").text(track.title);

            $.post("includes/handlers/ajax/getArtistJson.php", {artistId: track.artist}, function (data) {
                const artist = JSON.parse(data);

                $(".artistName span").text(artist.name);
            });

            $.post("includes/handlers/ajax/getAlbumJson.php", {albumId: track.album}, function (data) {
                const album = JSON.parse(data);

                $(".albumLink img").attr("src", album.artworkPath);
            });

            audioElement.setTrack(track);
        });

        if (play) {
            audioElement.play();
        }
    }

    function playSong() {

        if (audioElement.audio.currentTime === 0) {
            $.post("includes/handlers/ajax/updatePlays.php", {songId: audioElement.currentlyPlaying.id});
        }

        $(".controlButton.play").hide();
        $(".controlButton.pause").show();
        audioElement.play();
    }

    function pauseSong() {
        $(".controlButton.play").show();
        $(".controlButton.pause").hide();
        audioElement.pause();
    }

</script>


<div id="nowPlayingBarContainer">

    <div id="nowPlayingBar">

        <div id="nowPlayingLeft">

            <div class="content">

                    <span class="albumLink">
                        <img src="" class="albumArtwork" alt="Album artwork">
                    </span>

                <div class="trackInfo">

                        <span class="trackName">
                            <span></span>
                        </span>

                    <span class="artistName">
                            <span></span>
                        </span>

                </div>

            </div>

        </div>

        <div id="nowPlayingCenter">

            <div class="content playerControls">

                <div class="buttons">

                    <button class="controlButton shuffle" title="Shuffle button" onclick="setShuffle()">
                        <img src="assets/images/icons/shuffle_50px.png" alt="Shuffle" style="width: 20px">
                    </button>

                    <button class="controlButton previous" title="Previous button" onclick="prevSong()">
                        <img src="assets/images/icons/skip_to_start_50px.png" alt="Previous" style="width: 20px">
                    </button>

                    <button class="controlButton play" title="Play button" onclick="playSong()">
                        <img src="assets/images/icons/play_50px.png" alt="Play" style="width: 32px">
                    </button>

                    <button class="controlButton pause" title="Pause button" style="display: none"
                            onclick="pauseSong()">
                        <img src="assets/images/icons/pause_50px.png" alt="Pause" style="width: 32px">
                    </button>

                    <button class="controlButton next" title="Next button" onclick="nextSong()">
                        <img src="assets/images/icons/end_50px.png" alt="Next" style="width: 20px">
                    </button>

                    <button class="controlButton repeat" title="Repeat button" onclick="setRepeat()">
                        <img src="assets/images/icons/repeat_50px.png" alt="Repeat" style="width: 20px">
                    </button>

                </div>

                <div class="playbackBar">

                    <span class="progressTime current">0.00</span>

                    <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress">

                            </div>
                        </div>
                    </div>

                    <span class="progressTime remaining">0.00</span>

                </div>

            </div>

        </div>

        <div id="nowPlayingRight">

            <div class="volumeBar">

                <button class="controlButton volume" title="Volume button" onclick="setMute()">
                    <img src="assets/images/icons/sound_50px.png" alt="Volume" style="width: 20px">
                </button>

                <div class="progressBar">
                    <div class="progressBarBg">
                        <div class="progress">

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>