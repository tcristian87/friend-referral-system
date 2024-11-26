<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter("wp_mail_content_type", "referral_mail_content_type");
function referral_mail_content_type()
{
    return "text/html";
}

add_filter("wp_mail_from", "referral_mail_from");
function referral_mail_from()
{
    $admin_email = get_option('admin_email');
    return $admin_email;
}

add_filter("wp_mail_from_name", "referral_mail_from_name");
function referral_mail_from_name()
{
    $site_name = get_bloginfo('name');
    return $site_name;
}

