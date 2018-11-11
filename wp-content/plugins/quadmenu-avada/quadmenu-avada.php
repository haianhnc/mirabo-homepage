<?php
/**
 * Plugin Name: QuadMenu - Avada Mega Menu
 * Plugin URI: http://www.quadmenu.com
 * Description: Integrates QuadMenu with the Avada theme.
 * Version: 1.1.6
 * Author: Avada Mega Menu
 * Author URI: http://www.quadmenu.com
 * License: codecanyon
 * Copyright: 2018 QuadMenu (http://www.quadmenu.com)
 */
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('QuadMenu_Avada')) :

    final class QuadMenu_Avada {

        function __construct() {

            add_action('admin_notices', array($this, 'notices'));
            add_filter('quadmenu_developer_options', array($this, 'developer'), 10);
            add_filter('quadmenu_default_themes', array($this, 'themes'), 10);
            add_filter('quadmenu_default_options', array($this, 'defaults'), 10);
            add_filter('quadmenu_default_options_social', array($this, 'social'), 10);
            add_filter('quadmenu_default_options_theme_avada', array($this, 'avada'), 10);
            add_filter('quadmenu_default_options_location_main_navigation', array($this, 'main_navigation'), 10);

            add_action('wp_enqueue_scripts', array($this, 'register'), 100);
            add_action('admin_enqueue_scripts', array($this, 'register'), 100);
        }

        function register() {
            if (defined('FUSION_LIBRARY_URL')) {
                wp_deregister_style('fontawesome');
                wp_register_style('fontawesome', FUSION_LIBRARY_URL . '/assets/fonts/fontawesome/font-awesome.min.css', array(), false);
            }
        }

        function notices() {

            $screen = get_current_screen();

            if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
                return;
            }

            $plugin = 'quadmenu/quadmenu.php';

            if (is_plugin_active($plugin)) {
                return;
            }

            if (is_quadmenu_installed()) {

                if (!current_user_can('activate_plugins')) {
                    return;
                }
                ?>
                <div class="error">
                    <p>
                        <a href="<?php echo wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1', 'activate-plugin_' . $plugin); ?>" class='button button-secondary'><?php _e('Activate QuadMenu', 'quadmenu'); ?></a>
                        <?php esc_html_e('QuadMenu Avada not working because you need to activate the QuadMenu plugin.', 'quadmenu'); ?>   
                    </p>
                </div>
                <?php
            } else {

                if (!current_user_can('install_plugins')) {
                    return;
                }
                ?>
                <div class="error">
                    <p>
                        <a href="<?php echo wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=quadmenu'), 'install-plugin_quadmenu'); ?>" class='button button-secondary'><?php _e('Install QuadMenu', 'quadmenu'); ?></a>
                        <?php esc_html_e('QuadMenu Avada not working because you need to install the QuadMenu plugin.', 'quadmenu'); ?>
                    </p>
                </div>
                <?php
            }
        }

        function themes($themes) {

            $themes['avada'] = 'Avada';

            return $themes;
        }

        function defaults($defaults) {

            if (!function_exists('Avada')) {
                return $defaults;
            }

            $defaults['viewport'] = 1;
            $defaults['styles'] = 1;
            $defaults['styles_normalize'] = 1;
            $defaults['styles_widgets'] = 1;
            $defaults['styles_icons'] = 'fontawesome5';
            $defaults['styles_pscrollbar'] = 1;
            $defaults['gutter'] = 40;


            return $defaults;
        }

        function main_navigation($defaults) {

            $defaults['theme'] = 'avada';

            return $defaults;
        }

        function developer($options) {

            if (!function_exists('Avada')) {
                return $options;
            }

            // Locations
            // -----------------------------------------------------------------
            $options['main_navigation_integration'] = 1;
            $options['main_navigation_unwrap'] = 0;

            // Themes
            // -----------------------------------------------------------------

            $options['avada_theme_title'] = 'Avada';
            $options['avada_layout'] = 'collapse';
            $options['avada_layout_width_selector'] = '';
            $options['avada_layout_sticky_divider'] = '';
            $options['avada_layout_sticky'] = 0;
            $options['avada_layout_sticky_offset'] = '86';
            $options['avada_layout_divider'] = 'hide';
            $options['avada_layout_current'] = 0;
            $options['avada_layout_hover_effect'] = '';
            $options['avada_layout_breakpoint'] = Avada()->settings->get('side_header_break_point');

            // Menu
            // -----------------------------------------------------------------

            $options['avada_navbar_background'] = 'color';
            $options['avada_navbar_background_color'] = 'transparent';
            $options['avada_navbar_background_to'] = 'transparent';
            $options['avada_navbar_background_deg'] = '0';

            $options['avada_sticky'] = '';
            $options['avada_sticky_height'] = '70';
            //$options['avada_sticky_background'] = array('color' => '#ffffff', 'alpha' => '0');
            //$options['avada_sticky_logo_height'] = '25';

            $logo = array(
                'url' => plugin_dir_url(__FILE__) . 'images/logo.png'
            );

            $options['avada_navbar_logo'] = wp_parse_args((array) Avada()->settings->get('logo'), $logo);

            $options['avada_mobile_shadow'] = 'hide';

            // CSS
            // -----------------------------------------------------------------

            $options['css'] = '
                                        body.side-header-right #side-header,
                                        body.side-header-left #side-header {
                                            overflow: auto;
                                        }
                                        
                                        body.side-header-right #quadmenu.quadmenu-avada .quadmenu-navbar-nav > li,
                                        body.side-header-left #quadmenu.quadmenu-avada .quadmenu-navbar-nav > li {
                                            border-radius: 0;
                                        }

                                        #quadmenu.quadmenu-avada .button {
						border-radius: 2px;
					}
                                        #quadmenu.quadmenu-avada .fusion-custom-menu-item {
						display: none!important;
					}
					#quadmenu.quadmenu-avada .fusion-custom-menu-item {
						display: none!important;
					}

					#quadmenu.quadmenu-avada .quadmenu-navbar-nav >li.quadmenu-item-type-mega> .quadmenu-dropdown-menu li.quadmenu-item-type-post_type,
					#quadmenu.quadmenu-avada .quadmenu-navbar-nav >li.quadmenu-item-type-tabs> .quadmenu-dropdown-menu li.quadmenu-item-type-post_type {
						background-color: rgba(0,0,0,0.3);
					}
                                        
					#quadmenu.quadmenu-avada.quadmenu-is-horizontal .quadmenu-navbar-nav >li.quadmenu-item-type-mega> .quadmenu-dropdown-menu li.quadmenu-item-type-post_type,
					#quadmenu.quadmenu-avada.quadmenu-is-horizontal .quadmenu-navbar-nav >li.quadmenu-item-type-tabs> .quadmenu-dropdown-menu li.quadmenu-item-type-post_type {
						margin-bottom: 15px!important;
					}

					#quadmenu.quadmenu-avada .quadmenu-navbar-nav .quadmenu-dropdown-menu li.quadmenu-item-type-post_type > a {
						border: none!important;
					}                

            ';

            return $options;
        }

        function avada($defaults) {

            if (!function_exists('Avada')) {
                return $defaults;
            }

            $defaults['theme_title'] = 'Avada Theme';
            $defaults['layout'] = 'offcanvas';
            $defaults['layout_align'] = 'right';
            $defaults['layout_offcanvas_float'] = 'right';
            $defaults['layout_breakpoint'] = Avada()->settings->get('side_header_break_point');
            $defaults['layout_width_selector'] = '.fusion-builder-row';
            $defaults['layout_trigger'] = 'click';
            $defaults['layout_current'] = '';
            $defaults['layout_animation'] = 'quadmenu_btt';
            $defaults['layout_classes'] = '';
            $defaults['layout_sticky'] = '0';
            $defaults['layout_sticky_offset'] = '90';
            $defaults['layout_divider'] = 'hide';
            $defaults['layout_caret'] = 'show';
            $defaults['layout_hover_effect'] = '';


            $defaults['navbar_divider'] = 'transparent';
            $defaults['navbar_text'] = '#eeeeee';
            $defaults['navbar_height'] = '90';
            $defaults['navbar_width'] = '260';
            $defaults['navbar_mobile_border'] = '#e9e9e9';
            $defaults['navbar_toggle_open'] = '#333333';
            $defaults['navbar_toggle_close'] = '#a0ce4e';
            $defaults['navbar_logo_height'] = '25';
            $defaults['navbar_logo_bg'] = 'transparent';
            $defaults['navbar_link_margin'] = array(
                'border-top' => '0px',
                'border-right' => '0px',
                'border-bottom' => '0px',
                'border-left' => '0px',
                'border-style' => '',
                'border-color' => ''
            );
            $defaults['navbar_link_radius'] = array(
                'border-top' => '2px',
                'border-right' => '2px',
                'border-bottom' => '2px',
                'border-left' => '2px',
                'border-style' => '',
                'border-color' => ''
            );
            $defaults['navbar_link_transform'] = 'none';
            $defaults['navbar_link'] = '#333333';
            $defaults['navbar_link_hover'] = '#a0ce4e';
            $defaults['navbar_link_bg'] = 'transparent';
            $defaults['navbar_link_bg_hover'] = 'transparent';
            $defaults['navbar_link_hover_effect'] = '#a0ce4e';
            $defaults['navbar_button'] = '#ffffff';
            $defaults['navbar_button_bg'] = '#a0ce4e';
            $defaults['navbar_button_hover'] = '#ffffff';
            $defaults['navbar_button_bg_hover'] = '#333333';
            $defaults['navbar_link_icon'] = '#a0ce4e';
            $defaults['navbar_link_icon_hover'] = '#333333';
            $defaults['navbar_link_subtitle'] = '#cccccc';
            $defaults['navbar_link_subtitle_hover'] = '#333333';
            $defaults['navbar_badge'] = '#a0ce4e';
            $defaults['navbar_badge_color'] = '#ffffff';
            $defaults['sticky_background'] = 'transparent';
            $defaults['sticky_height'] = '60';
            $defaults['sticky_logo_height'] = '25';
            $defaults['navbar_scrollbar'] = '#a0ce4e';
            $defaults['navbar_scrollbar_rail'] = '#ffffff';
            $defaults['dropdown_margin'] = '5';
            $defaults['dropdown_radius'] = '2';
            $defaults['dropdown_border'] = array(
                'border-top' => '0px',
                'border-right' => '',
                'border-bottom' => '',
                'border-left' => '',
                'border-style' => '',
                'border-color' => '#000000'
            );
            $defaults['dropdown_background'] = '#363839';
            $defaults['dropdown_scrollbar'] = '#a0ce4e';
            $defaults['dropdown_scrollbar_rail'] = '#333333';
            $defaults['dropdown_title'] = '#bfbfbf';
            $defaults['dropdown_title_border'] = array(
                'border-top' => '2px',
                'border-right' => '2px',
                'border-bottom' => '2px',
                'border-left' => '2px',
                'border-style' => 'solid',
                'border-color' => '#a0ce4e'
            );
            $defaults['dropdown_link'] = '#bfbfbf';
            $defaults['dropdown_link_hover'] = '#a0ce4e';
            $defaults['dropdown_link_bg_hover'] = '#282a2b';
            $defaults['dropdown_link_border'] = array(
                'border-top' => '1px',
                'border-right' => '',
                'border-bottom' => '',
                'border-left' => '',
                'border-style' => 'solid',
                'border-color' => '#505152'
            );
            $defaults['dropdown_link_transform'] = 'none';
            $defaults['dropdown_button'] = '#ffffff';
            $defaults['dropdown_button_hover'] = '#ffffff';
            $defaults['dropdown_button_bg'] = '#a0ce4e';
            $defaults['dropdown_button_bg_hover'] = '#282a2b';
            $defaults['dropdown_link_icon'] = '#a0ce4e';
            $defaults['dropdown_link_icon_hover'] = '#ffffff';
            $defaults['dropdown_link_subtitle'] = '#505152';
            $defaults['dropdown_link_subtitle_hover'] = '#ffffff';

            $font = array(
                'font-family' => 'Roboto Slab',
                'font-options' => '',
                'google' => '1',
                'font-weight' => '400',
                'font-style' => '',
                'subsets' => 'latin',
                'font-size' => '13px',
                'letter-spacing' => '1'
            );

            $defaults['font'] = wp_parse_args((array) Avada()->settings->get('body_typography'), $font);

            $navbar_font = array(
                'font-family' => 'Roboto Slab',
                'font-options' => '',
                'google' => '1',
                'font-weight' => '700',
                'font-style' => '',
                'subsets' => 'latin',
                'font-size' => '13px',
                'letter-spacing' => '1'
            );

            $defaults['navbar_font'] = wp_parse_args((array) Avada()->settings->get('h4_typography'), $navbar_font);

            $dropdown_font = array(
                'font-family' => 'PT Sans',
                'font-options' => '',
                'google' => '1',
                'font-weight' => '400',
                'font-style' => '',
                'subsets' => 'latin',
                'font-size' => '13px',
                'letter-spacing' => '1'
            );

            $defaults['dropdown_font'] = wp_parse_args((array) Avada()->settings->get('h4_typography'), $dropdown_font);

            return $defaults;
        }

        function social($social) {

            return array(
                array(
                    'title' => 'Facebook',
                    'icon' => 'fa fa-facebook ',
                    'url' => 'http://codecanyon.net/user/quadlayers/portfolio?ref=quadlayers',
                ),
                array(
                    'title' => 'Twitter',
                    'icon' => 'fa fa-twitter',
                    'url' => 'http://codecanyon.net/user/quadlayers/portfolio?ref=quadlayers',
                ),
                array(
                    'title' => 'Google',
                    'icon' => 'fa fa-google-plus',
                    'url' => 'http://codecanyon.net/user/quadlayers/portfolio?ref=quadlayers',
                ),
                array(
                    'title' => 'RSS',
                    'icon' => 'fa fa-rss',
                    'url' => 'http://codecanyon.net/user/quadlayers/portfolio?ref=quadlayers',
                ),
            );
        }

        static function activation() {

            update_option('_quadmenu_compiler', true);

            if (class_exists('QuadMenu')) {

                QuadMenu_Redux::add_notification('blue', esc_html__('Thanks for install QuadMenu Avada. We have to create the stylesheets. Please wait.', 'quadmenu-avada'));

                QuadMenu_Activation::activation();
            }
        }

    }

    endif; // End if class_exists check

new QuadMenu_Avada();

if (!function_exists('avada_logo')) {

    function avada_logo() {
        return;
    }

}

if (!function_exists('avada_main_menu')) {

    function avada_main_menu() {

        if (Avada()->settings->get('header_position') !== 'Top') {

            wp_nav_menu(array(
                'theme_location' => 'main_navigation',
                'layout' => 'inherit'
                    )
            );
        } else {

            wp_nav_menu(array('theme_location' => 'main_navigation'));
        }
    }

}

if (!function_exists('is_quadmenu_installed')) {

    function is_quadmenu_installed() {

        $file_path = 'quadmenu/quadmenu.php';

        $installed_plugins = get_plugins();

        return isset($installed_plugins[$file_path]);
    }

}

register_activation_hook(__FILE__, array('QuadMenu_Avada', 'activation'));
