<?php

class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $myErrors;

    /**
     * Start up
     */
    public function __construct()
    {
        $this->myErrors = new WP_Error();
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_head', array($this, 'eff_stylesheet'));
        add_action('admin_footer', array($this, 'eff_clear_cache_javascript'));
        add_action('wp_ajax_eff_clear_cache', array($this, 'eff_clear_cache'));

        add_filter('admin_footer_text', array($this, 'admin_footer_text'));
    }

    /**
     * Load stylesheets
     */
    function eff_stylesheet()
    {
        wp_register_style('eff_style', plugins_url('../css/eff_style.css?8', __FILE__));
        wp_enqueue_style('eff_style');
    }

    function admin_footer_text($footer_text)
    {
        $current_screen = get_current_screen();

        //only display on easy facebook feed settings page
        if ($current_screen->id === "settings_page_easy-facebook-feed") {
            $footer_text = sprintf(__('If you like <strong>Easy Facebook Feed</strong> please leave us a %s rating. A huge thanks in advance!', 'easy-facebook-feed'), '<a href="https://wordpress.org/support/plugin/easy-facebook-feed/reviews?rate=5#new-post" target="_blank" class="wc-rating-link" data-rated="' . esc_attr__('Thanks :)', 'easy-facebook-feed') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>');
        }

        return $footer_text;
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Easy Facebook Feed Settings',
            'Easy Facebook Feed',
            'manage_options',
            'easy-facebook-feed',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        $defaults = array(
            'facebook_page_id' => 'bbcnews',
            'facebook_post_limit' => '5',
            'caching_refresh_time' => '30'
        );

        // Set class property
        $this->options = get_option('eff_options', $defaults);
        ?>
        <div class="wrap">
            <h1><?php _e("Easy Facebook Feed", 'easy-facebook-feed'); ?></h1>

            <?php $active_tab = (isset($_GET[ 'tab' ]) ? $_GET[ 'tab' ] : 'general'); ?>

            <h2 class="nav-tab-wrapper">
                    <a href="?page=easy-facebook-feed&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e("Settings", 'easy-facebook-feed'); ?></a>
                <?php if ( is_plugin_active( 'easy-facebook-feed-pro/easy-facebook-feed-pro.php' ) ): ?>
                    <a href="?page=easy-facebook-feed&tab=display" class="nav-tab <?php echo $active_tab == 'display' ? 'nav-tab-active' : ''; ?>"><?php _e("Easy Facebook Feed Pro", 'easy-facebook-feed'); ?></a>
                <?php else: ?>
                    <a href="http://easy-facebook-feed.nl/product/easy-facebook-feed-pro/" target="_blank" class="nav-tab"><?php _e("Easy Facebook Feed Pro", 'easy-facebook-feed'); ?></a>
                <?php endif; ?>
            </h2>

            <form method="post" action="options.php" class="eff-admin">
                <?php
                    switch ($active_tab) {
                        case 'general':
                            settings_fields('my_option_group');
                            do_settings_sections('my-setting-admin');
                            submit_button();
                            break;
                        case 'display':
                            if ( is_plugin_active( 'easy-facebook-feed-pro/easy-facebook-feed-pro.php' ) ) {
                                do_action('eff_display_admin_settings');
                            } else {
                                echo "<p>Easy Facebook Feed Pro not found.</p>";
                            }
                            break;
                    }
                ?>
            </form>
                
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'eff_options', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            __("General", 'easy-facebook-feed'), // Title
            array($this, 'print_section_info'), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'facebook_page_id', // ID
            __("Facebook page ID", 'easy-facebook-feed'), // Title
            array($this, 'facebook_page_id_callback'), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );

        add_settings_field(
            'facebook_post_limit',
            __("Number of posts", 'easy-facebook-feed'),
            array($this, 'facebook_post_limit_callback'),
            'my-setting-admin',
            'setting_section_id'
        );

        // caching section
        add_settings_section(
            'caching_section', // ID
            __("Caching", 'easy-facebook-feed'), // Title
            null, // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'caching_refresh_time', // ID
            __("Refresh time", 'easy-facebook-feed'), // Title
            array($this, 'caching_refresh_time'), // Callback
            'my-setting-admin', // Page
            'caching_section' // Section
        );

        add_settings_field(
            'clear_cache', // ID
            __("Clear", 'easy-facebook-feed'), // Title
            array($this, 'clear_cache'), // Callback
            'my-setting-admin', // Page
            'caching_section' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $eff = new Eff();
        $options = $eff->eff_getOptions();
        $new_input = array();
        $input = array_map('trim', $input);

        if (!empty($input['facebook_page_id'])) {
            $new_input['facebook_page_id'] = sanitize_text_field($input['facebook_page_id']);
        } else {
            $new_input['facebook_page_id'] = $options['facebook_page_id'];
            add_settings_error( 'facebook_page_id', 'facebook_page_id', __('Invalid Facebook page id', 'easy-facebook-feed') );
        }

        if (!empty($input['facebook_post_limit']) && absint($input['facebook_post_limit']) !== 0) {
            $new_input['facebook_post_limit'] = absint($input['facebook_post_limit']);
        } else {
            $new_input['facebook_post_limit'] = $options['facebook_post_limit'];
            add_settings_error( 'facebook_post_limit', 'facebook_post_limit', __('Invalid Post limit value', 'easy-facebook-feed') );
        }

        if (!empty($input['caching_refresh_time']) && absint($input['caching_refresh_time']) !== 0) {
            $new_input['caching_refresh_time'] = absint($input['caching_refresh_time']);
        } else {
            $new_input['caching_refresh_time'] = $options['caching_refresh_time'];
            add_settings_error( 'caching_refresh_time', 'caching_refresh_time', __('Invalid refresh time value', 'easy-facebook-feed') );
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print "<div class='notice notice-info'>
            <p><strong>" . __("Quickstart:", "easy-facebook-feed") . " </strong>" . __("Add [easy_facebook_feed] on your page to display the facebook feed on your page. Or you can add the Easy Facebook Feed widget to your widget area.", 'easy-facebook-feed') . "</p>
        </div>";
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function facebook_page_id_callback()
    {
        printf(
            '<input type="text" id="facebook_page_id" name="eff_options[facebook_page_id]" value="%s" />
                    <p class="description">' . __("Example: in https://www.facebook.com/bbcnews ‘bbcnews’ is your Facebook page id", 'easy-facebook-feed') . '</p>',
            isset($this->options['facebook_page_id']) ? esc_attr($this->options['facebook_page_id']) : 'bbcnews'
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function facebook_post_limit_callback()
    {
        printf(
            '<input type="text" id="facebook_post_limit" name="eff_options[facebook_post_limit]" value="%s" />
                    <p class="description">' . __("Number of posts to display", 'easy-facebook-feed') . '</p>',
            isset($this->options['facebook_post_limit']) ? esc_attr($this->options['facebook_post_limit']) : '5'
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function caching_refresh_time()
    {
        printf(
            '<input type="text" id="caching_refresh_time" name="eff_options[caching_refresh_time]" value="%s" />
                    <p class="description">' . __("Time in minutes", 'easy-facebook-feed') . '.</p>',
            isset($this->options['caching_refresh_time']) ? esc_attr($this->options['caching_refresh_time']) : '30'
        );
    }

    public function clear_cache()
    {
        printf(
            '<input type="button" name="button" id="clear_cache" class="button button-default" value="' . __('Clear cache', 'easy-facebook-feed') . '">
                    <p class="description">' . __("Manually clear cache", 'easy-facebook-feed') . '.</p>'
        );
    }

    public function eff_clear_cache_javascript() { ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {

                var data = {
                    'action': 'eff_clear_cache'
                };

                jQuery('#clear_cache').on('click', function() {
                    jQuery.post(ajaxurl, data, function(response) {
                        alert(response);
                    });
                });

            });
        </script> <?php
    }

    function eff_clear_cache() {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $results = 0;
        $results += $wpdb->query('DELETE FROM `'. $prefix . 'options` WHERE `option_name` LIKE (\'_transient_%\');');
        $results += $wpdb->query('DELETE FROM `'. $prefix . 'options` WHERE `option_name` LIKE (\'_site_transient_%\');');

        if($results > 0) {
            _e('The data cache was cleared successfully', 'easy-facebook-feed');
        } else {
            _e('No cache found', 'easy-facebook-feed');
        }

        wp_die(); // this is required to terminate immediately and return a proper response
    }

}

if (is_admin())
    $my_settings_page = new MySettingsPage();
