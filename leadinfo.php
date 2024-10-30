<?php
/**
 * Plugin Name: Leadinfo
 * Plugin URI: https://wordpress.org/plugins/leadinfo/
 * Description: Leadinfo Plugin
 * Version: 1.1
 * Author: Leadinfo
 * Author URI:  https://www.leadinfo.com/
 * Copyright 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if (!defined('WPINC')) {
    exit;
}
require_once plugin_dir_path(__FILE__) . 'leadinfo.class.php';

register_activation_hook(__FILE__, 'leadinfo_activate');
register_deactivation_hook(__FILE__, 'leadinfo_deactivate');
register_uninstall_hook(__FILE__, 'leadinfo_uninstall');

add_action('rest_api_init', function () {
    register_rest_route('/leadinfo/v1', '/tracker_code', array(
        'methods' => 'POST',
        'callback' => 'add_leadinfo_tracker_code',
    ));
});

function add_leadinfo_tracker_code($data) {
    if(empty($data['tracker_code'])){
        return;
    }

    update_option('leadinfo_id', $data['tracker_code']);
}

$leadinfo = new Leadinfo();
$leadinfo->run();

function leadinfo_activate()
{
    add_option('leadinfo_id', '', '', 'yes');
}
//test
function leadinfo_deactivate()
{
    delete_option('leadinfo_id');
}

function leadinfo_uninstall()
{
    delete_option('leadinfo_id');
}

function leadinfo_settings_link($links)
{
    $settings_link = '<a href=' . admin_url("admin.php?page=leadinfo>Settings") . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_" . $plugin, 'leadinfo_settings_link');
