=== Paid Memberships Pro - Post Affiliate Pro Integration Add On ===
Contributors: strangerstudios
Tags: pmpro, paid memberships pro, ecommerce, affiliates
Requires at least: 3.1
Tested up to: 5.2
Stable tag: .2.2

Integrate Paid Memberships Pro with the Post Affiliate Pro platform.


== Description ==

This plugin will integrate with the Post Affiliate Pro platform.

== Installation ==

1. Upload the `pmpro-post-affiliate-pro` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Edit pmpro-post-affiliate-pro.php to enter your affiliate login link, username, and password.
. Create links in Post Affiliate Pro as you would normally. The plugin will track clicks and sales through the PAP API.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-post-affiliate-pro/issues

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at http://www.paidmembershipspro.com for more documentation and our support forums.

== Changelog === .2.2 =* updated Post Affilate Pro API to version 5.7.17.7 = .2.1.1 =* Fixed bug where certain characters in membership level names were breaking sales tracking.= .2.1 =* Fixed bug with setting the product ID, now passing membership level name as product ID instead as well.= .2 =* Upgraded PAP class from(PAP version: 4.9.2.4, GPF version: 1.2.0.0) to (PAP version: 5.0.10.1, GPF version: 1.2.3.0)* No longer dying if the connection to PAP breaks for any reason. Now an error message will be shown to WP admins, but other users will not notice.* Now passing the membership level ID as the "product ID" to Post Affiliates Pro.= .1 =* Initial release.