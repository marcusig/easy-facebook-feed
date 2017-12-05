<?php

class Template
{
    protected $template;

    public function __construct($file)
    {
        $this->template = $this->getTemplate($file);
    }

    private function getTemplate($file)
    {
        if (!file_exists(EFF_PLUGIN_DIR . 'templates/' . $file)) {
            return 'Error loading template file (' . $file . ').';
        }

        return file_get_contents(EFF_PLUGIN_DIR . 'templates/' . $file);
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function set($key, $value = null)
    {
        if ($value) {
            $this->template = str_replace('{{' . $key . '}}', $value, $this->template);
        } else {
            $this->remove($key);
        }
    }

    public function remove($tag)
    {
        $this->template = str_replace('{{' . $tag . '}}', '', $this->template);
    }

    public function output()
    {
        return $this->template;
    }

    public static function merge($template1, $template2)
    {
        $output = str_replace('{{data-content}}', $template2, $template1);

        return $output;
    }
}
