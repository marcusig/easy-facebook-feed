<?php
/**
 * Plugin Name: Easy Facebook Feed
 * Plugin URI: http://easy-facebook-feed.nl
 * Description: Easy Facebook Feed shows your Facebook feed in an easy way!
 * Version: 3.0.15
 * Author: timwass
 * Text Domain: easy-facebook-feed
 * License: GPLv2 or later
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('EFF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EFF_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(EFF_PLUGIN_DIR . 'includes/class.eff-error.php');
require_once(EFF_PLUGIN_DIR . 'includes/class.eff-server-requirements.php');
require_once(EFF_PLUGIN_DIR . 'includes/class.eff-connect.php');
require_once(EFF_PLUGIN_DIR . 'includes/class.eff-post.php');
require_once(EFF_PLUGIN_DIR . 'includes/class.eff-language.php');
require_once(EFF_PLUGIN_DIR . 'includes/class.eff-template.php');
require_once(EFF_PLUGIN_DIR . 'includes/class.eff.php');

add_shortcode('easy_facebook_feed', array(new Eff, 'eff_easy_facebook_feed'));

require_once(EFF_PLUGIN_DIR . 'includes/class.eff-widget.php');

if (is_admin()) {
    require_once(EFF_PLUGIN_DIR . 'includes/class.eff-admin.php');

    // Add settings link on plugin page
    function eff_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=easy-facebook-feed">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", 'eff_settings_link');
}


