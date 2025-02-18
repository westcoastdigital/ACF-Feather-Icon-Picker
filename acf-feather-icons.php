<?php
/*
Plugin Name:  ACF Feather Icon Picker
Plugin URI:   https://gist.github.com/westcoastdigital/ACF-Feather-Icon-Picker
Description:  Add feather icons to ACF
Version:      1.0.0
Author:       Jon Mather
Author URI:   https://jonmather.au
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  translate
Domain Path:  /languages
*/

// exit if accessed directly
if (!defined('ABSPATH')) exit;

// include field
function include_acf_field_types() {
    include_once('fields/class-acf-field-feather-icon.php');
}
add_action('acf/include_field_types', 'include_acf_field_types');
