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
        if (strpos($data->source, 'fbcdn')) {
            $template = new Template('eff-video.html');
            $template->set('data-source', $data->source);
            $template->set('data-picture', $data->full_picture);
            $template->set('data-link', $data->link);
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
    public function eff_makeEvent($data)
    {
        $template = new Template('eff-event.html');
        $template->set('data-link', $data->link);
        $template->set('data-name', $data->name);
        $template->set('data-description', nl2br($data->description));

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

}