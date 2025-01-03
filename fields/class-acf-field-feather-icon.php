<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('acf_field_feather_icon')):

    class acf_field_feather_icon extends ACF_Field
{

        public function initialize()
    {
            // Basic field properties
            $this->name     = 'feather_icon';
            $this->label    = __('Feather Icon Picker');
            $this->category = 'choice';

            // Default settings
            $this->defaults = array(
                'default_value' => '',
                'layout'        => 'vertical', // vertical | horizontal
                'return_format' => 'icon_name', // icon_name | html | component
            );

            // Do the parent constructor
            parent::initialize();

            // Register assets directory
            $this->settings = array(
                'version' => '1.0.0',
                'url'     => plugin_dir_url(__FILE__),
                'path'    => plugin_dir_path(__FILE__),
            );

            // Actions for enqueueing assets
            add_action('acf/input/admin_enqueue_scripts', array($this, 'input_admin_enqueue_scripts'));
            add_action('acf/input/admin_head', array($this, 'input_admin_head'));
        }

        public function render_field_settings($field)
    {
            // Layout
            acf_render_field_setting($field, array(
                'label'        => __('Layout', 'acf'),
                'instructions' => __('Radio button alignment', 'acf'),
                'type'         => 'radio',
                'name'         => 'layout',
                'layout'       => 'horizontal',
                'choices'      => array(
                    'vertical'   => __('Vertical', 'acf'),
                    'horizontal' => __('Horizontal', 'acf'),
                ),
            ));

            // Return Format
            acf_render_field_setting($field, array(
                'label'        => __('Return Format', 'acf'),
                'instructions' => __('Specify the returned value format', 'acf'),
                'type'         => 'radio',
                'name'         => 'return_format',
                'layout'       => 'horizontal',
                'choices'      => array(
                    'icon_name' => __('Icon Name'),
                    'html'      => __('HTML'),
                    'component' => __('Uppercase Name'),
                ),
            ));
        }

        public function render_field($field)
    {
            // Normalize field settings
            $field = array_merge($this->defaults, $field);
            $e     = '';

            // Create a unique ID for the search input
            $search_id = 'feather-icon-search-' . $field['id'];

            // Get icons
            $icons = $this->get_feather_icons();

            // Begin HTML
            $e .= '<div class="acf-feather-icon-wrapper">';

            // Add search input
            $e .= '<input type="text" id="' . $search_id . '" class="feather-icon-search" placeholder="Search icons..." />';

            // Begin radio wrapper
            $e .= '<div class="acf-feather-icon-radio-wrapper ' . $field['layout'] . '">';

            // Generate radio buttons for each icon
            foreach ($icons as $icon) {
                $id      = $field['id'] . '-' . $icon;
                $checked = ($field['value'] === $icon) ? 'checked="checked"' : '';

                $e .= '<div class="acf-feather-icon-option">';
                $e .= '<input type="radio" id="' . esc_attr($id) . '" name="' . esc_attr($field['name']) . '" value="' . esc_attr($icon) . '" ' . $checked . '>';
                $e .= '<label for="' . esc_attr($id) . '">';
                $e .= '<i data-feather="' . esc_attr($icon) . '"></i>';
                $e .= '<span class="icon-name">' . esc_html($icon) . '</span>';
                $e .= '</label>';
                $e .= '</div>';
            }

            $e .= '</div>'; // Close radio wrapper
            $e .= '</div>'; // Close main wrapper

            echo $e;
        }

        public function input_admin_enqueue_scripts()
    {
            // Get the absolute URL to your plugin's assets directory
            $url = trailingslashit(plugin_dir_url(dirname(__FILE__))) . 'assets/';

            // Register & enqueue Feather Icons
            if (!wp_script_is('feather-icons', 'registered')) {
                wp_register_script('feather-icons',
                    'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js',
                    array(),
                    '4.29.0'
                );
            }
            wp_enqueue_script('feather-icons');

            // Register & enqueue your custom script
            wp_register_script('acf-feather-icon-picker',
                $url . 'js/input.js',
                array('jquery', 'feather-icons'),
                $this->settings['version']
            );
            wp_enqueue_script('acf-feather-icon-picker');

            // Register & enqueue your custom styles
            wp_register_style('acf-feather-icon-picker',
                $url . 'css/input.css',
                array('acf-input'),
                $this->settings['version']
            );
            wp_enqueue_style('acf-feather-icon-picker');
        }

        public function input_admin_head()
    {
            // Initialize Feather icons after page load
            ?>
	        <script>
	        (function($) {
	            acf.add_action('ready', function() {
	                if (typeof feather !== 'undefined') {
	                    feather.replace();
	                }
	            });
	        })(jQuery);
	        </script>
	        <?php
    }

        public function format_value($value, $post_id, $field)
    {
            if (empty($value)) {
                return '';
            }

            switch($field['return_format']) {
                case 'html':
                    // Add the feather script to the footer
                    if (!wp_script_is('feather-icons', 'enqueued')) {
                        wp_enqueue_script('feather-icons',
                            'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js',
                            array(),
                            '4.29.0',
                            true
                        );

                        // Add initialization script
                        wp_add_inline_script('feather-icons', 'window.addEventListener("DOMContentLoaded", function() { feather.replace(); });');
                    }

                    // Return the HTML with additional wrapper for styling
                    return sprintf(
                        '<span class="acf-feather-icon" data-acf-feather="%1$s"><i data-feather="%1$s"></i></span>',
                        esc_attr($value)
                    );

                case 'component':
                    return str_replace(' ', '', ucwords(str_replace('-', ' ', $value)));

                case 'icon_name':
                default:
                    return $value;
            }
        }

        private function get_feather_icons()
    {
            return array(
                'activity',
                'airplay',
                'alert-circle',
                'alert-octagon',
                'alert-triangle',
                'align-center',
                'align-justify',
                'align-left',
                'align-right',
                'anchor',
                'aperture',
                'archive',
                'arrow-down-circle',
                'arrow-down-left',
                'arrow-down-right',
                'arrow-down',
                'arrow-left-circle',
                'arrow-left',
                'arrow-right-circle',
                'arrow-right',
                'arrow-up-circle',
                'arrow-up-left',
                'arrow-up-right',
                'arrow-up',
                'at-sign',
                'award',
                'bar-chart-2',
                'bar-chart',
                'battery-charging',
                'battery',
                'bell-off',
                'bell',
                'bluetooth',
                'bold',
                'book-open',
                'book',
                'bookmark',
                'box',
                'briefcase',
                'calendar',
                'camera-off',
                'camera',
                'cast',
                'check-circle',
                'check-square',
                'check',
                'chevron-down',
                'chevron-left',
                'chevron-right',
                'chevron-up',
                'chevrons-down',
                'chevrons-left',
                'chevrons-right',
                'chevrons-up',
                'chrome',
                'circle',
                'clipboard',
                'clock',
                'cloud-drizzle',
                'cloud-lightning',
                'cloud-off',
                'cloud-rain',
                'cloud-snow',
                'cloud',
                'code',
                'codepen',
                'codesandbox',
                'coffee',
                'columns',
                'command',
                'compass',
                'copy',
                'corner-down-left',
                'corner-down-right',
                'corner-left-down',
                'corner-left-up',
                'corner-right-down',
                'corner-right-up',
                'corner-up-left',
                'corner-up-right',
                'cpu',
                'credit-card',
                'crop',
                'crosshair',
                'database',
                'delete',
                'disc',
                'divide-circle',
                'divide-square',
                'divide',
                'dollar-sign',
                'download-cloud',
                'download',
                'dribbble',
                'droplet',
                'edit-2',
                'edit-3',
                'edit',
                'external-link',
                'eye-off',
                'eye',
                'facebook',
                'fast-forward',
                'feather',
                'figma',
                'file-minus',
                'file-plus',
                'file-text',
                'file',
                'film',
                'filter',
                'flag',
                'folder-minus',
                'folder-plus',
                'folder',
                'framer',
                'frown',
                'gift',
                'git-branch',
                'git-commit',
                'git-merge',
                'git-pull-request',
                'github',
                'gitlab',
                'globe',
                'grid',
                'hard-drive',
                'hash',
                'headphones',
                'heart',
                'help-circle',
                'hexagon',
                'home',
                'image',
                'inbox',
                'info',
                'instagram',
                'italic',
                'key',
                'layers',
                'layout',
                'life-buoy',
                'link-2',
                'link',
                'linkedin',
                'list',
                'loader',
                'lock',
                'log-in',
                'log-out',
                'mail',
                'map-pin',
                'map',
                'maximize-2',
                'maximize',
                'meh',
                'menu',
                'message-circle',
                'message-square',
                'mic-off',
                'mic',
                'minimize-2',
                'minimize',
                'minus-circle',
                'minus-square',
                'minus',
                'monitor',
                'moon',
                'more-horizontal',
                'more-vertical',
                'mouse-pointer',
                'move',
                'music',
                'navigation-2',
                'navigation',
                'octagon',
                'package',
                'paperclip',
                'pause-circle',
                'pause',
                'pen-tool',
                'percent',
                'phone-call',
                'phone-forwarded',
                'phone-incoming',
                'phone-missed',
                'phone-off',
                'phone-outgoing',
                'phone',
                'pie-chart',
                'play-circle',
                'play',
                'plus-circle',
                'plus-square',
                'plus',
                'pocket',
                'power',
                'printer',
                'radio',
                'refresh-ccw',
                'refresh-cw',
                'repeat',
                'rewind',
                'rotate-ccw',
                'rotate-cw',
                'rss',
                'save',
                'scissors',
                'search',
                'send',
                'server',
                'settings',
                'share-2',
                'share',
                'shield-off',
                'shield',
                'shopping-bag',
                'shopping-cart',
                'shuffle',
                'sidebar',
                'skip-back',
                'skip-forward',
                'slack',
                'slash',
                'sliders',
                'smartphone',
                'smile',
                'speaker',
                'square',
                'star',
                'stop-circle',
                'sun',
                'sunrise',
                'sunset',
                'table',
                'tablet',
                'tag',
                'target',
                'terminal',
                'thermometer',
                'thumbs-down',
                'thumbs-up',
                'toggle-left',
                'toggle-right',
                'tool',
                'trash-2',
                'trash',
                'trello',
                'trending-down',
                'trending-up',
                'triangle',
                'truck',
                'tv',
                'twitch',
                'twitter',
                'type',
                'umbrella',
                'underline',
                'unlock',
                'upload-cloud',
                'upload',
                'user-check',
                'user-minus',
                'user-plus',
                'user-x',
                'user',
                'users',
                'video-off',
                'video',
                'voicemail',
                'volume-1',
                'volume-2',
                'volume-x',
                'volume',
                'watch',
                'wifi-off',
                'wifi',
                'wind',
                'x-circle',
                'x-octagon',
                'x-square',
                'x',
                'youtube',
                'zap-off',
                'zap',
                'zoom-in',
                'zoom-out',
                // Add more icons as needed
            );
        }
    }

// Initialize
    acf_register_field_type('acf_field_feather_icon');

endif;

function acf_feather_icon_frontend_styles() {
    ?>
    <style>
        .acf-feather-icon {
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
        }

        .acf-feather-icon svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
    </style>
    <?php
}
add_action('wp_head', 'acf_feather_icon_frontend_styles');