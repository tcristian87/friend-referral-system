<?php if ( ! defined( 'ABSPATH' ) ) exit ;?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border: 1px solid #dddddd;
        }
        .email-header {
            background-color: #0073aa;
            color: #ffffff;
            padding: 10px 20px;
            text-align: center;
        }
        .email-body {
            padding: 20px;
            line-height: 1.5;
            color: #333333;
        }
        .email-footer {
            font-size: 12px;
            text-align: center;
            padding: 20px;
        }
        .coupon-code {
            display: block;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #eee;
            border: dashed 2px #ccc;
            font-weight: bold;
            color: #0073aa;
        }
        .button {
            display: inline-block;
            background-color: #0073aa;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer-container{
            display: flex;
            flex-direction: row;
            width: 100%;
            background-color: #f8f8f8;
            justify-content: space-around;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1><?php echo esc_html($heading); ?></h1>
        </div>
        <div class="email-body">
            <h2><?php echo esc_html($subheading); ?></h2>
            <p><?php echo esc_html($content_1); ?></p>
            <p><?php echo esc_html($content_2); ?></p>
            <a href="<?php echo esc_url(site_url()) . '/refer/' . esc_html($coupon_code); ?>" class="coupon-code"><?php echo esc_html($coupon_code); ?></a>
            <p><?php echo esc_html__("Click the code above to automatically apply the discount to your cart.", "friend-referral-system"); ?></p>
            <p><?php echo esc_html__(" You need to create an account with this email address", "friend-referral-system"); ?></p>
        </div>
        <div class="footer-container">
        <div class="email-footer">
            <p><?php echo esc_html($footer); ?></p>
            <p><?php echo esc_html($footer_signature_1) . ' ' . esc_html($footer_signature_2);  ?></p>
        </div>
        <div class="email-logo">
        <a href="<?php echo esc_url(site_url());?>"> <img src='<?php echo esc_url($logo_url) ;?>' alt="logo" width="50"/></a>
        </div>
        </div>
    </div>
</body>
</html>
