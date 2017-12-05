<?php

class EffConnect
{
    private $accessToken = '1492018151012834|U3qsH98pUZxv5watRRC4c-rg1rc';
    private $error;

    public function __construct()
    {
        $this->error = new EffError();
    }

    /**
     * @param $pageId
     * @return array|mixed|object
     */
    public function eff_get_page($pageId)
    {
        $fields = 'link,name,cover,picture';
        $accessToken = $this->accessToken;
        $url = "https://graph.facebook.com/v2.6/{$pageId}?fields={$fields}&access_token={$accessToken}";
        $page = $this->eff_connect($url);

        return $page;
    }

    /**
     * @param $pageId
     * @param $postLimit
     * @return array|mixed|object
     */
    public function eff_get_page_feed($pageId, $postLimit)
    {
        $accessToken = $this->accessToken;
        $fields = 'full_picture,type,message,link,name,description,from,source,created_time,permalink_url';
        $fields = apply_filters('effp-page-feed-fields', $fields);
        $url = "https://graph.facebook.com/v2.6/{$pageId}/posts?fields={$fields}&access_token={$accessToken}&limit={$postLimit}";
        $feed = $this->eff_connect($url);

        return $feed;
    }

    /**
     * @param $url
     * @return array|mixed|object
     */
    private function eff_connect($url)
    {
        $req = new EffServerRequirements;
        if ($req->getConnectionType() === "curl") {
            $result = json_decode($this->connect_with_curl($url));
        } else {
            $result = json_decode($this->connect_with_fopen($url));
        }

        return $result;
    }

    private function connect_with_curl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // disable ssl verifypeer to avoid trouble on some configs
        $json = curl_exec($ch);
        if ($json === false) {
            echo $this->error->print_error_message(curl_error($ch));
            exit();
        }
        curl_close($ch);

        return $json;
    }

    private function connect_with_fopen($url)
    {
        if (file_get_contents($url)) {
            $json = file_get_contents($url);
        } else {
            $arr = array('error' => array('message' => "Unknown file_get_contents connection error with Facebook."));
            $json = json_encode($arr);
        }

        return $json;
    }
}