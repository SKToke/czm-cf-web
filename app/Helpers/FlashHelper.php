<?php
/*******************************************************************************
 * @package     CZM-CF-Web
 * @author      Md. Akil Tahsin<akil@nascenia.com
 ******************************************************************************/
namespace App\Helpers;

class FlashHelper{
    public static function trigger($message, $status): void
    {
        if (!function_exists('trigger')) {
            session(['flash' => json_encode(['message' => $message, 'status' => $status])]);
        }
    }
}
