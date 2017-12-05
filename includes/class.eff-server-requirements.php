<?php

/**
 * Class EffServerRequirements
 */
class EffServerRequirements
{

    private $error;
    private $errorMessages;
    private $connectionType;

    /**
     * EffServerRequirements constructor.
     */
    public function __construct()
    {
        $this->error = false;
        $this->errorMessages = array();

        $this->checkPhpVersion();
        $this->checkConnection();
        $this->checkJsonEncode();
        $this->setConnectionType();
    }

    /**
     * Check curl and Fopen modules
     */
    private function checkConnection()
    {
        if (!$this->isCurlEnabled() && !$this->isFopenEnabled()) {
            $this->error = true;
            $this->errorMessages[] = 'Easy Facebook Feed requires allow_url_fopen or Curl to function, please contact your hosting provider to enable allow_url_fopen or Curl in your php.ini.';
        }
    }

    private function setConnectionType()
    {
        if ($this->isCurlEnabled()) {
            $this->connectionType = 'curl';
        } else if ($this->isFopenEnabled()) {
            $this->connectionType = 'fopen';
        } else {
            $this->error = true;
            $this->errorMessages[] = 'Unable to set connection type.';
        }
    }

    /**
     * @return bool
     */
    private function isCurlEnabled()
    {
        if (function_exists('curl_version')) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isFopenEnabled()
    {
        if (ini_get('allow_url_fopen')) {
            return true;
        }

        return false;
    }

    /**
     * Easy Facebook Feed requires PHP 5.3 or higher.
     */
    private function checkPhpVersion()
    {
        if (version_compare(PHP_VERSION, '5.3', '<')) {
            $this->error = true;
            $this->errorMessages[] = 'Easy Facebook Feed requires PHP 5.3 or higher. You’re still on ' . PHP_VERSION . '. Please update your PHP version.';
        }
    }

    /**
     * Easy Facebook Feed requires json_encode to function
     */
    private function checkJsonEncode()
    {
        if (!function_exists('json_encode')) {
            $this->error = true;
            $this->errorMessages[] = 'Easy Facebook Feed requires json_encode to function, please install and enable the PHP json extension.';
        }
    }

    /**
     * @return bool
     */
    public function getErrorStatus()
    {
        return $this->error;
    }

    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * @return string
     */
    public function errorMessages()
    {
        $errors = '';
        foreach ($this->errorMessages as $message) {
            $error = new EffError();
            $errors = $error->print_error_message($message);
        }

        return $errors;
    }

}