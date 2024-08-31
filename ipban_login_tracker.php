<?php
/*
* Plugin Name: IPBan Login Tracker
* Description: Tracks both failed and successful login attempts and writes them to a custom log file for IPBan to process.
* Version: 1.0
* Author: IPBan Pro (Jeff Johnson)
* Author URI: https://ipban.com
* Plugin URI: https://ipban.com/wordpress-plugin
* Text Domain: ipban-login-tracker
* License: MIT
* License URI: https://opensource.org/license/mit
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!function_exists('wp_filesystem')) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

function ipban_append_log($file, $content) {
	// Open the file in append mode
	$file_handle = fopen($file, 'a');
	fwrite($file_handle, $content);
	fclose($file_handle);
}

function ipban_on_activation() {
	ipban_append_log(IPBAN_LOG_FILE, gmdate('Y-m-d H:i:s') . " - IPBan WordPress Logging Initialized!\n");
}

try {
    // Determine the log file path based on the operating system
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows environment
        define('IPBAN_LOG_FILE', 'C:/IPBanCustomLogs/login_attempts_wp.log');
    } else {
        // Linux environment
        define('IPBAN_LOG_FILE', '/var/log/ipbancustom_login_attempts_wp.log');
    }

    // Register the activation hook to log initialization only once
    register_activation_hook(__FILE__, 'ipban_on_activation');

    // Register authenticate filter for tracking logins
    add_filter('authenticate', 'ipban_track_login', 1000, 3);
} catch (Exception $e) {
    // Handle the exception (e.g., log it somewhere)
}

// Function to get the real IP address of the user, accounting for proxies like Cloudflare
function get_user_ip_address() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return esc_url_raw(wp_unslash($_SERVER['HTTP_CF_CONNECTING_IP']));
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_list = explode(',', esc_url_raw(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])));
        return trim($ip_list[0]); // The first IP in the list is the client's IP
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return esc_url_raw(wp_unslash($_SERVER['REMOTE_ADDR']));
    }

    return '';
}

function ipban_track_login($user, $username, $password) {
    $ip_address = get_user_ip_address();
    if (empty($ip_address)) {
        return $user;
    }

    $timestamp = gmdate('Y-m-d\TH:i:s\Z');
    $source = 'WordPress';
    $log_entry = '';

    if ($user instanceof WP_User) {
        $log_entry = "{$timestamp}, ipban success login, ip address: {$ip_address}, source: {$source}, user: {$username}\n";
    } else {
        $log_entry = "{$timestamp}, ipban failed login: {$ip_address}, source: {$source}, user: {$username}\n";
    }

    ipban_append_log(IPBAN_LOG_FILE, $log_entry);

    return $user;
}

?>
