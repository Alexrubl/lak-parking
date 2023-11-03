<?php

namespace Alexrubl\Video;

use Laravel\Nova\Card;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Video extends Card
{
    public $controller = null;
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/2';

    public function __construct(Controller $controller = null) {
        $this->controller = $controller;
        // $this->exec_ffmpeg();
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'video';
    }

    public function exec_ffmpeg() {
       $process = new Process(["ffmpeg", "-i", "rtsp://test:123456789qQ@5.165.25.145:55554", "-fflags", "flush_packets", "-max_delay", "5", "-flags", "-global_header", "-hls_time", "5", "-hls_list_size", "3", "-hls_flags", "delete_segments", "-vcodec", "copy", "-y", "/var/www/html/public/1/file.m3u8"]);
        // $cmd = 'ffmpeg -i rtsp://test:123456789qQ@5.165.25.145:55554 -fflags flush_packets -max_delay 5 -flags -global_header -hls_time 5 -hls_list_size 3 -hls_flags delete_segments -vcodec copy -y /var/www/html/public/1/file.m3u8';
        // $last_line = system($cmd, $retval);
        // info($last_line);
        // info($retval);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        info ($process->getOutput());
    }
}
