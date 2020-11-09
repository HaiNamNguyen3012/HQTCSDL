let currentPlaylist = [];
let shufflePlaylist = [];
let currentIndex = 0;
let userLoggedIn;

function openPage(url) {

    if (url.indexOf("?") === -1) {
        url = url + "?";
    }

    const encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
    console.log(encodedUrl);
    $("#mainContent").load(encodedUrl);
}

function formatTime(seconds) {
    const time = Math.round(seconds);
    const minutes = Math.floor(time / 60);
    seconds = time - minutes * 60;

    let extraZero = (seconds < 10) ? "0" : "";

    return minutes + ":" + extraZero + seconds;
}

function updateTimeProgressBar(audio) {
    $(".progressTime.current").text(formatTime(audio.currentTime));
    $(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

    let progress = audio.currentTime / audio.duration * 100;
    $(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio) {
    let volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

function Audio() {

    this.currentlyPlaying = null;
    this.audio = document.createElement("audio");

    this.audio.addEventListener("ended", () => {
        nextSong();
    });

    // this.audio.addEventListener("canplay", () => {
    //     //'this' refers to the object that the event was called on
    //     let duration = formatTime(this.duration);
    //     $(".progressTime.remaining").text(duration);
    // });

    // this.audio.addEventListener("timeupdate", () => {
    //     if (this.duration) {
    //         updateTimeProgressBar(this);
    //     }
    // });

    this.audio.addEventListener("volumechange", () => {
        updateVolumeProgressBar(this);
    });

    this.setTrack = (track) => {
        this.currentlyPlaying = track;
        this.audio.src = track.path;
    }

    this.play = () => {
        this.audio.play();
    }

    this.pause = () => {
        this.audio.pause();
    }

    this.setTime = (seconds) => {
        this.audio.currentTime = seconds;
    }

}