<?php
/**
 * @package Audiate
 */

/**
 * Plugin Name: Audioplace Me
 * Plugin URI: https://www.audioplace.me
 * Description: Transform your content into audio to reach a larger audience with our Text-to-Speech widget.
 * Version: 1.2.8
 * Author: Audioplace.Me
 * License: GPLv2 or later
 * Text Domain: audioplace
 * Tested up to: 5.9
 *
 * {Plugin Name} is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * {Plugin Name} is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with {Plugin Name}. If not, see {License URI}.
 **/

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!defined('AUDIATE_PLUGIN_DIR')) {
    define( 'AUDIATE_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
}

require_once AUDIATE_PLUGIN_DIR . 'includes/audiate-admin.php';
require_once AUDIATE_PLUGIN_DIR . 'includes/audiate-widget.php';

if(class_exists('AudiateWidget') ){
    $audiateWidget = new AudiateWidget();
}

function my_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=audiate">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function getShortCode() {

     global $audiateWidget;

    return $audiateWidget->get_code();
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'my_plugin_settings_link' );
