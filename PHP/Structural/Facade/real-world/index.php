<?php

namespace Facade\RealWord;

class YouTubeDownloader {
    protected $youtube;
    protected $ffmpeg;

    public function __construct(string $youtubeApiKey) {
        $this->youtube = new Youtube($youtubeApiKey);
        $this->ffmpeg = new FFMpeg();
    }

    public function downloadVideo(string $url): void {
        echo 'Fetch Meta Data';

        echo 'Save video file';

        echo 'Convert video with FFMpeg';

        echo "Saving video in target formats...<br>";

        echo "Done";
    }
}

class Youtube {
    public function fetchVideo(): string {
        /* ... */
        return 'something';
    }
}

class FFMpeg {
    public static function create(): FFMpeg {
        return new FFMpeg();
    }

    public function open(string $video): void {
        echo 'open';
    }
}

class FFMpegVideo {
    public function filters(): self {
        echo 'Fileters';
        return self;
    }

    public function resize(): self {
        return self;
    }

    public function save(string $path): self {
        echo "hello";
    }
}

function clientCode (YouTubeDownloader $facade) {
    $facade->downloadVideo("some url");
}

$facade = new YouTubeDownloader("API-KEY");

clientCode($facade);