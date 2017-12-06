<?php

class Post
{
    /**
     * @param $data
     * @param $page
     * @return mixed
     */
    public function eff_makePost($data, $page)
    {
        $template = new Template('eff-post.html');
        $template = apply_filters('effp-post', $template, $data);

        $template->set('link-text', __('View', 'easy-facebook-feed'));
        $template->set('page-link', $page->link);
        $template->set('page-cover-source', $page->picture->data->url);
        $template->set('data-from-name', $data->from->name);
        isset($data->permalink_url) ? $template->set('data-link', $data->permalink_url) : $template->set('data-link', $page->link);
        $template->set('data-created_time', $this->eff_time_elapsed_string($data->created_time));
        if (isset($data->message)) {
            $message = $this->setUrls($data->message);
            $message = $this->setHashtags($message);
            $template->set('data-message', nl2br($message));
        } else {
            $template->set('data-message', '');
        }

        return $template->output();
    }

    /**
     * @param $data
     * @return mixed|string
     */
    public function eff_makePhoto($data)
    {
        $template = new Template('eff-photo.html');
        $template->set('image-url', $data->full_picture);

        return $template->output();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function eff_makeVideo($data)
    {
        // added akamaihd to the cdns
        if (strpos($data->source, 'fbcdn') || strpos($data->source, 'akamaihd') ) {
            // proposition: WP supports embeding of facebook videos. We can use it by default and use the html5 <video> as a fallback
            $embed = wp_oembed_get( $data->link );
            if( $embed ) return $embed;
            $template = new Template('eff-video.html');
            $template->set('data-source', $data->source);
            $template->set('data-picture', $data->full_picture);
            $template->set('data-link', $data->link);
        } elseif( strpos($data->source, 'youtube.com') ) {
            // oembed youtube videos, or fallback to picture + link. could add other providers
            $embed = wp_oembed_get( $data->link );
            if( ! $embed ){
                $template = new Template('eff-photo.html');
                $template->set('image-url', $data->full_picture);                    
            } else { 
                return $embed;
            }
        } else {
            $template = new Template('eff-photo.html');
            $template->set('image-url', $data->full_picture);
        }

        return $template->output();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function eff_makeEvent($data,$eventDetails)
    {
        $template = new Template('eff-event.html');
        $template->set('data-link', $data->link);
        $template->set('data-name', $data->name);
        // event date
        $date = strtotime( $eventDetails->start_time );
        $template->set('data-month', strftime( '%b' ,$date ) );
        $template->set('data-day', strftime( '%d' ,$date ) );
        
        //$template->set('data-description', nl2br($data->description));
        // if event has a tickets link
        if (isset($eventDetails->ticket_uri)) {
            $template_ticket_link = new Template( 'eff-event-ticket-link.html' );
            $template_ticket_link->set('data-link', $eventDetails->ticket_uri );
            $domain = $this->get_domain( $eventDetails->ticket_uri );
            if(!$domain) {
                $domain = $eventDetails->ticket_uri;
            }
            $template_ticket_link->set('data-name', sprintf( __('Get your tickets on %1$s','easy-facebook-feed'), $domain ) );
            $template_ticket_link = $template_ticket_link->output();
            $template->set('data-ticket', $template_ticket_link);
        } else {
            $template->remove('data-ticket'); 
        }
        // if event has a cover picture
        if (isset($eventDetails->cover)) {
            $template2 = new Template('eff-event-cover.html');
            $template2->set('data-picture', $eventDetails->cover->source);
            $margin = isset( $eventDetails->cover->offset_y ) ? '-'. $eventDetails->cover->offset_y : 0;
            $template2->set('data-margin', $margin);
        $template = Template::merge($template->output(), $template2->output());
        } else {
            $template->remove('data-content');
            $template = $template->output();
        }

        return $template;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function eff_makeLink($data)
    {
        $template = new Template('eff-link.html');
        $template->set('data-link', $data->link);
        $template->set('data-name', $data->name);
        if (isset($data->description)) {
            $template->set('data-description', nl2br($data->description));
        } else {
            $template->set('data-description', '');
        }

        if (isset($data->full_picture)) {
            $template2 = new Template('eff-link-picture.html');
            $template2->set('data-picture', $data->full_picture);
            $template = Template::merge($template->output(), $template2->output());
        } else {
            $template->remove('data-content');
            $template = $template->output();
        }

        return $template;
    }

    /**
     * @param $datetime
     * @param bool|false $full
     * @return string
     */
    private function eff_time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = (array)$now->diff($ago);

        $diff['w'] = floor($diff['d'] / 7);
        $diff['d'] -= $diff['w'] * 7;

        $string = array(
            'y' => array('singular' => __('year', 'easy-facebook-feed'), 'plural' => __('years', 'easy-facebook-feed')),
            'm' => array('singular' => __('month', 'easy-facebook-feed'), 'plural' => __('months', 'easy-facebook-feed')),
            'w' => array('singular' => __('week', 'easy-facebook-feed'), 'plural' => __('weeks', 'easy-facebook-feed')),
            'd' => array('singular' => __('day', 'easy-facebook-feed'), 'plural' => __('days', 'easy-facebook-feed')),
            'h' => array('singular' => __('hour', 'easy-facebook-feed'), 'plural' => __('hours', 'easy-facebook-feed')),
            'i' => array('singular' => __('minute', 'easy-facebook-feed'), 'plural' => __('minutes', 'easy-facebook-feed')),
            's' => array('singular' => __('second', 'easy-facebook-feed'), 'plural' => __('seconds', 'easy-facebook-feed')),
        );

        foreach ($string as $key => &$value) {
            if ($diff[$key]) {
                $value = $diff[$key] . ' ' . ($diff[$key] > 1 ? $value['plural'] : $value['singular']);
            } else {
                unset($string[$key]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ' . __('ago', 'easy-facebook-feed') : __('just now', 'easy-facebook-feed');
    }

    private function setHashtags($message)
    {
        if (preg_match_all("/#(\\w+)/", $message, $matches)) {
            foreach ($matches[1] as $key => $match) {
                $url = "<a href='https://www.facebook.com/hashtag/" . $match . "'>#" . $match . "</a>";
                $message = str_replace('#' . $match, $url, $message);
            }
        }

        return $message;
    }

    private function setUrls($message)
    {
        $message = preg_replace("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", "<a href=\"\\0\">\\0</a>", $message);

        return $message;
    }
    // utility function to get domain name
    private function get_domain( $url ) {
        $url = parse_url( $url );
        if( $url && isset( $url['host'] ) ) {
            return $url['host'];
        } 
        return false;
    }    

}