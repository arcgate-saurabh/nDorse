<?php

App::uses('Component', 'Controller');
/*
 * Video Encoder CakePHP Component 
 * Copyright (c) 2009 Andrew Weir 
 * http://andrw.new 
 * 
 * 
 * @author      Andrew Weir <andru.weir@gmail.com> 
 * @version     1.0 
 * @license     MIT 
 */

class VideoEncoderComponent extends Component {

    /**
     * Everything in this method can be placed into a global configuration 
     * file that is called at bootstrap/runtime. 
     * */
    function __construct() {
        // ffmpeg path 
        Configure::write('Video.ffmpeg_path', '/usr/bin/ffmpeg');

        // flvtool2 path 
        Configure::write('Video.flvtool2_path', '/bin/flvtool2');

        // Bitrate of audio (valid vaues are 16,32,64) 
        Configure::write('Video.bitrate ', 64);

        // Sampling rate (valid values are 11025, 22050, 44100) 
        Configure::write('Video.samprate ', 44100);
    }

    function convert_video($in, $out, $width = 480, $height = 360, $optimized = false) {
        if ($optimized == false) {
            $command = Configure::read('Video.ffmpeg_path') . " -i {
                $in
            } -y -s {
                $width
            }x {
                $height
            } -r 30 -b 500 -ar " . Configure::read('Video.samprate') . " -ab " . Configure::read('Video.bitrate') . " {
                $out
            }";
        } else {
            $command = Configure::read('Video.ffmpeg_path') . " -i {
                $in
            } -b 256k -ac 1 -ar 44100 {
                $out
            }";
        }
        echo exec($command);
    }

    function set_buffering($in) {
        $command = Configure::read('Video.flvtool2_path') . " -U {
            $in
        }";
        shell_exec($command);
    }

    function grab_image($in, $out) {
        $command = Configure::read('Video.ffmpeg_path') . " -y -i {
            $in
        } -f mjpeg -t 0.003 {
            $out
        }";
        shell_exec($command);
    }

    function get_duration($in) {
        $command = Configure::read('Video.ffmpeg_path') . " -i {
            $in
        } 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/, //"; 
        return shell_exec($command);
    }

    function get_filesize($in) {
        return 1;
    }

    function remove_uploaded_video($in) {
        unlink($in);
    }

}

?>