<?php

/*
Plugin Name: Flohkiste - Simple calendar list output via shortcode
Description: Displays a list of events from CPT
Version: 0.1
Author: Lukas Niebler
Author URI: https://lukas-niebler.de
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: flohkiste
*/

namespace LN\Flohkiste;

defined('ABSPATH') || exit;

const FLOHKISTE_PHP_VERSION = '7.4';
const FLOHKISTE_WP_VERSION = '6.0';

/**
 * SPL Autoloader (PSR-4).
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__;
    $base_dir = __DIR__ . '/includes';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Register plugin hooks
register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');

add_action('plugins_loaded', __NAMESPACE__ . '\loaded');

// Load translations
function loadTextdomain()
{
    load_plugin_textdomain('flohkiste', false, basename(dirname(__FILE__)) . '/languages');
}

/**
 * System requirements verification
 * @return string Return an error message
 */
function systemRequirements(): string
{
    loadTextdomain();
    $error = '';
    if (version_compare(PHP_VERSION, FLOHKISTE_PHP_VERSION, '<')) {
        $error = sprintf(
            /* translators: 1: Server PHP version number, 2: Required PHP version number. */
            __('The server is running PHP version %1$s. The Plugin requires at least PHP version %2$s.', 'like-the-wind'),
            PHP_VERSION,
            FLOHKISTE_PHP_VERSION
        );
    } elseif (version_compare($GLOBALS['wp_version'], FLOHKISTE_WP_VERSION, '<')) {
        $error = sprintf(
            /* translators: 1: Server WordPress version number, 2: Required WordPress version number. */
            __('The server is running WordPress version %1$s. The Plugin requires at least WordPress version %2$s.', 'like-the-wind'),
            $GLOBALS['wp_version'],
            FLOHKISTE_WP_VERSION
        );
    }
    return $error;
}


//Activation callback function
function activation()
{
    if ($error = systemRequirements()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            sprintf(
                /* translators: 1: The plugin name, 2: The error string. */
                __('Plugins: %1$s: %2$s', 'flohkiste'),
                plugin_basename(__FILE__),
                $error
            )
        );
    }
}


//Deactivation callback function
function deactivation()
{
    // Nothing to do
}

/**
 * Instantiate the plugin class
 * @return object Plugin
 */
function plugin()
{
    static $instance;
    if (null === $instance) {
        $instance = new Plugin(__FILE__);
    }
    return $instance;
}


/**
 * Loaded callback function
 * @return void
 */
function loaded()
{
    add_action('init', __NAMESPACE__ . '\loadTextdomain');
    plugin()->loaded();
    if ($error = systemRequirements()) {
        add_action('admin_init', function () use ($error) {
            if (current_user_can('activate_plugins')) {
                $pluginData = get_plugin_data(plugin()->getFile());
                $pluginName = $pluginData['Name'];
                $tag = is_plugin_active_for_network(plugin()->getBaseName()) ? 'network_admin_notices' : 'admin_notices';
                add_action($tag, function () use ($pluginName, $error) {
                    printf(
                        '<div class="notice notice-error"><p>' .
                            /* translators: 1: The plugin name, 2: The error string. */
                            __('Plugins: %1$s: %2$s', 'like-the-wind') .
                            '</p></div>',
                        esc_html($pluginName),
                        esc_html($error)
                    );
                });
            }
        });
        return;
    }

    new Main(__FILE__);
}
