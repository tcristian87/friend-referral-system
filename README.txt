=== Friend Referral System ===

Contributors: LeraTech Solutions 
Tags: Referral, WooCommerce, WordPress
Requires at least: 5.1.0
Tested up to: 6.6
Requires PHP: 7.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Boost your user registration and orders with the Friend Referral System.

== Description ==

Introducing the Friend Referral System – the ultimate solution designed to supercharge your e-commerce 
site with powerful referral capabilities. This plugin seamlessly integrates with WooCommerce, enabling you to 
leverage word-of-mouth marketing by rewarding your customers for referring friends and family.

This plugin can only be used with WooCommerce installed and requires an SMTP plugin for email functionality.

Features:
- Easy integration with any WooCommerce store.
- Automatically sends referral emails with coupon details.
- Rewards both the referrer and the referred friend with customizable coupons.
- The reward coupon for the sender is offered only after the referral friend places an order.
- Coupons usage is restricted to the email address used during the referral, with a default usage limit of one per coupon.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

== Requirements ==

- WooCommerce: This plugin is an extension for WooCommerce and requires WooCommerce to be installed and activated on your WordPress site.
- SMTP Plugin: An SMTP plugin must be installed and configured to handle outgoing emails for referral notifications.

== Features ==

1. **General Settings**:
   - **Coupon Amount**: Define the value of the discount provided by the referral coupon.
   - **Coupon Type**: Choose the type of discount (e.g., fixed amount, percentage discount).
   - **Coupon Validity**: Set the duration for which the coupon remains valid, measured in days.

2. **Coupon Restrictions**:
   - **Individual Use Only**: Restrict the coupon to be used on its own, preventing combination with other offers.
   - **Exclude Sale Items**: Opt to exclude sale items from being eligible for the coupon discount, ensuring promotions are used as intended.

== Uninstallation ==

To uninstall:
1. Deactivate the plugin through the 'Plugins' screen in WordPress.
2. Delete the plugin through the 'Plugins' screen in WordPress.

IMPORTANT NOTICE: Upon deletion, the plugin will remove all referral data from the database.
 Ensure you have backed up any necessary data before proceeding with plugin deletion.

== Screenshots ==
1. Admin panel showing the general settings configuration.
2. Frontend view of the referral coupon received by a friend.

== Changelog ==
= 1.0 =
* Initial release.
