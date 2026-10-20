<?php
/**
 * Pied de page des e-mails — Café Feuillette
 * Surcharge du template WooCommerce emails/email-footer.php (US-8.2) :
 * adresse du café et texte du pied de page défini dans le back office.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">
                                <table border="0" cellpadding="10" cellspacing="0" width="100%" id="template_footer" role="presentation">
                                    <tr>
                                        <td valign="top">
                                            <table border="0" cellpadding="10" cellspacing="0" width="100%" role="presentation">
                                                <tr>
                                                    <td colspan="2" valign="middle" id="credit">
                                                        <p><strong>Café Feuillette Limoges</strong><br />5 Pl. Saint-Michel, 87000 Limoges</p>
                                                        <?php echo wp_kses_post(wpautop(wptexturize(apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'))))); ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td></td>
        </tr>
    </table>
</body>
</html>
