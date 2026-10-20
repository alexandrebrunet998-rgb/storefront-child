<?php
/**
 * En-tête des e-mails — Café Feuillette
 * Surcharge du template WooCommerce emails/email-header.php (US-8.2) :
 * logo de l'enseigne au-dessus de l'e-mail.
 *
 * @var string $email_heading Titre de l'e-mail.
 */

if (!defined('ABSPATH')) {
    exit;
}

$logo = get_stylesheet_directory_uri() . '/images/logo-feuillette-email.png';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo esc_html(get_bloginfo('name', 'display')); ?></title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    <table width="100%" id="outer_wrapper" role="presentation">
        <tr>
            <td></td>
            <td width="600">
                <div id="wrapper" dir="ltr">
                    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="inner_wrapper" role="presentation">
                        <tr>
                            <td align="center" valign="top">
                                <div id="template_header_image">
                                    <p style="margin-top: 0;">
                                        <img src="<?php echo esc_url($logo); ?>" alt="Le Café Feuillette" width="200" style="display: inline-block; width: 200px; height: auto; border: none; outline: none; text-decoration: none;" />
                                    </p>
                                </div>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container" role="presentation">
                                    <tr>
                                        <td align="center" valign="top">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header" role="presentation">
                                                <tr>
                                                    <td id="header_wrapper">
                                                        <h1><?php echo esc_html($email_heading); ?></h1>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" valign="top">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body" role="presentation">
                                                <tr>
                                                    <td valign="top" id="body_content">
                                                        <table border="0" cellpadding="20" cellspacing="0" width="100%" role="presentation">
                                                            <tr>
                                                                <td valign="top" id="body_content_inner_cell">
                                                                    <div id="body_content_inner">
