<?php

namespace LN\Flohkiste;

defined('ABSPATH') || exit;
class Main
{
    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
        require_once plugin_dir_path($pluginFile) . 'vendor/cmb2/cmb2/init.php';
        
        new Cpt($pluginFile);
        new Helper();
        //new Shortcode();
    }
}