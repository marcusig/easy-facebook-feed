<?php

class load_language
{

    public function __construct()
    {
        add_action('init', array($this, 'load_my_transl'));
    }

    public function load_my_transl()
    {
        load_plugin_textdomain('easy-facebook-feed', FALSE, dirname(plugin_basename(__FILE__)) . '/../languages/');
    }
}

$zzzz = new load_language;