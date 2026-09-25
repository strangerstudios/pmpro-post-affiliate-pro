=== Paid Memberships Pro - Post Affiliate Pro Integration Add On ===
Contributors: strangerstudios
Tags: pmpro, paid memberships pro, ecommerce, affiliates
Requires at least: 5.3
Tested up to: 7.1
Stable tag: 0.4

Integrate Paid Memberships Pro with the Post Affiliate Pro platform.

== Description ==

This plugin will integrate with the Post Affiliate Pro platform.

== Installation ==

1. Upload the `pmpro-post-affiliate-pro` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add your Post Affiliate Pro URL, login email, and password to your site's wp-config.php file as the URL_TO_PAP, PAP_LOGIN, and PAP_PASS constants. See the comment at the top of pmpro-post-affiliate-pro.php for an example. Do not edit the plugin file itself, since your settings will be erased when the plugin updates.
. Create links in Post Affiliate Pro as you would normally. The plugin will track clicks and sales through the PAP API.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-post-affiliate-pro/issues

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at http://www.paidmembershipspro.com for more documentation and our support forums.

== Changelog ==
= 0.4 - 2026-09-25 =
* SECURITY: Post Affiliate Pro API requests now verify the server's SSL certificate. #13 (@dparker1005)
* ENHANCEMENT: Post Affiliate Pro settings can now be defined in `wp-config.php` so they are no longer erased when the plugin updates. If you previously edited `pmpro-post-affiliate-pro.php`, add your `URL_TO_PAP`, `PAP_LOGIN`, and `PAP_PASS` constants (and `PAP_ACCOUNT`, if you changed it from `default1`) to `wp-config.php` after updating. #14 (@dparker1005)

= 0.3 - 2025-05-27 =
* BUG FIX: Fixed a fatal error where curly braces are no longer supported in PHP 8 and above. (@andrewlimaza)

= .2.1.1 =
* Fixed bug where certain characters in membership level names were breaking sales tracking.

= .2.1 =
* Fixed bug with setting the product ID, now passing membership level name as product ID instead as well.

= .2 =
* Upgraded PAP class from(PAP version: 4.9.2.4, GPF version: 1.2.0.0) to (PAP version: 5.0.10.1, GPF version: 1.2.3.0)
* No longer dying if the connection to PAP breaks for any reason. Now an error message will be shown to WP admins, but other users will not notice.
* Now passing the membership level ID as the "product ID" to Post Affiliates Pro.

= .1 =
* Initial release.