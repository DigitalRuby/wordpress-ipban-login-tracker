=== IPBan Login Tracker ===
Tags: security, login, ipban, firewall
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.0
License: MIT
License URI: https://opensource.org/license/mit
Detect failed and successful logins and report to IPBan or IPBan Pro.

== Description ==

Tracks both failed and successful login attempts and writes them to a custom log file for IPBan or IPBan Pro to process, securing your WordPress site by monitoring login activity and blocking malicious IP addresses automatically.

**Note:** If you are using a proxy such as Cloudflare, you should enable the Cloudflare module for IPBan Pro. The free IPBan version doesn't have this integration, but can be achieved using ProcessToRunOnBan.

== Installation ==

1. Upload the `ipban-login-tracker` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Ensure that your server has the correct log file permissions, particularly if you're running Linux.
4. Test success and fail logins.
5. Check log file. Windows path: C:/IPBanCustomLogs/login_attempts_wp.log. Linux path: /var/log/ipbancustom_login_attempts_wp.log.

### Linux Setup

For Linux, if you have root access, you should run these commands to ensure correct log file permissions:

```bash
sudo touch /var/log/ipbancustom_login_attempts_wp.log
sudo chmod 0664 /var/log/ipbancustom_login_attempts_wp.log
sudo chown www-data:www-data /var/log/ipbancustom_login_attempts_wp.log
```
