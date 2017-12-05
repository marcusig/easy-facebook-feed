<?php

class Eff
{

    private $serverRequirements;
    private $post;
    private $error;

    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this, 'eff_stylesheet' ));
        $this->serverRequirements = new EffServerRequirements;
        $this->post = new Post;
        $this->error = new EffError;
    }

    /**
     * @param $atts
     * @return null|string
     */
    public function eff_easy_facebook_feed($atts)
    {
        // get default values
        $options = $this->eff_getOptions();

        // check server environment on requirements
        if ($this->serverRequirements->getErrorStatus()) {
            return $this->serverRequirements->errorMessages();
        }

        // get shortcode parameters
        $shortcode_atts = shortcode_atts(array(
            'id' => $options['facebook_page_id'],
            'limit' => $options['facebook_post_limit'],
        ), $atts);

        $shortcode_atts['id'] = array_map('trim', array_filter(explode(',', $shortcode_atts['id'])));
        if (empty($shortcode_atts['id'])) {
            echo $this->error->print_error_message("No Facebook page id found, please check your Easy Facebook Feed settings and/or shortcode if the Facebook page id is set correctly");
            return false;
        }

        // check if cached version is available
        $cacheKey = md5('eff' . serialize($shortcode_atts));
        if (false !== ($cachedFeed = get_transient($cacheKey))) {
            if (is_array($cachedFeed)) {
                $cachedFeed = implode('', $cachedFeed);
            }
            return $cachedFeed;
        }

        // get facebook posts and set templates
        foreach ($shortcode_atts['id'] as $id) {
            $con = new EffConnect();
            $feed = $con->eff_get_page_feed($id, $shortcode_atts['limit']);
            $page = $con->eff_get_page($id);

            if (isset($feed->error)) {
                return $this->error->print_error_message($feed->error->message);
            }

            if (isset($page->error)) {
                return $this->error->print_error_message($page->error->message);
            }

            foreach ($feed->data as $key => $data) {
                $postTemplate = $this->post->eff_makePost($data, $page);

                switch ($data->type) {
                    case 'photo':
                        $photoTemplate = $this->post->eff_makePhoto($data);
                        $items[$data->created_time] = Template::merge($postTemplate, $photoTemplate);
                        break;
                    case 'link':
                        $linkTemplate = $this->post->eff_makeLink($data);
                        $items[$data->created_time] = Template::merge($postTemplate, $linkTemplate);
                        break;
                    case 'video':
                        $videoTemplate = $this->post->eff_makeVideo($data);
                        $items[$data->created_time] = Template::merge($postTemplate, $videoTemplate);
                        break;
                    case 'event':
                        $eventTemplate = $this->post->eff_makeEvent($data);
                        $items[$data->created_time] = Template::merge($postTemplate, $eventTemplate);
                        break;
                    case 'status':
                        $postTemplate = str_replace("{{data-content}}", '', $postTemplate);
                        $items[$data->created_time] = $postTemplate;
                        break;
                }
            }
        }

        krsort($items);
        $items = array_slice($items, 0, $shortcode_atts['limit']);
        $items = implode('', $items);

        // Save to cache for 30 minutes
        $options['caching_refresh_time'] = (0 < (int)$options['caching_refresh_time']) ? (int)$options['caching_refresh_time'] : 30; //just in case
        set_transient($cacheKey, $items, $options['caching_refresh_time'] * 60);

        return $items;
    }

    /**
     * Load stylesheets
     */
    public function eff_stylesheet()
    {
        wp_register_style('eff_style', plugins_url('../css/eff_style.css?8', __FILE__));
        wp_enqueue_style('eff_style');
    }

    /**
     * @return mixed
     */
    public function eff_getOptions()
    {
        $defaults = array(
            'facebook_page_id' => 'bbcnews',
            'facebook_post_limit' => '5',
            'caching_refresh_time' => '30'
        );

        return get_option('eff_options', $defaults);
    }

}
