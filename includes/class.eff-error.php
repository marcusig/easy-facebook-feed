<?php

class EffError
{

    /**
     * @return string
     */
    public function print_error_message($message)
    {
        $template = new Template('eff-error.html');
        $template->set('error-message', $message);

        return $template->output();
    }

}
