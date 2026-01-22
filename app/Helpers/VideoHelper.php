<?php
/*******************************************************************************
 * @package     CZM-CF-Web
 * @author      Md. Akil Tahsin<akil@nascenia.com
 ******************************************************************************/
namespace App\Helpers;

class VideoHelper{
    public static function youtube_video_id($youtube_url)
    {
        $regex = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';

        preg_match($regex, $youtube_url, $matches);

        return isset($matches[1]) ? $matches[1] : null;
    }
}
