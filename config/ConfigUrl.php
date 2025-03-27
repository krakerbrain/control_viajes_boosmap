<?php

class ConfigUrl
{
    public static function get()
    {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            return 'http://localhost/control_viajes_boosmap/';
        } else {
            return 'https://boosterapp.site/';
        }
    }
}

/**
 * USAR
  require_once __DIR__ . '/classes/ConfigUrl.php';
  $baseUrl = ConfigUrl::get();
 * 
 */
