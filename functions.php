<?php
/**
 * Thème enfant Storefront — Café Feuillette
 */

/**
 * Charge le style du thème enfant APRÈS les styles de Storefront et de WooCommerce,
 * pour que nos règles CSS aient toujours le dernier mot.
 */
add_action('wp_enqueue_scripts', function () {
    $dependances = ['storefront-style'];

    if (wp_style_is('storefront-woocommerce-style', 'registered')) {
        $dependances[] = 'storefront-woocommerce-style';
    }

    wp_enqueue_style(
        'storefront-child-style',
        get_stylesheet_uri(),
        $dependances,
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}, 25);

/* ==========================================================================
   Header (US-2.1)
   ========================================================================== */

/**
 * Deux emplacements de menus, de part et d'autre du logo.
 * Ils se remplissent dans le back office : Apparence > Menus.
 */
add_action('after_setup_theme', function () {
    register_nav_menus([
        'menu-gauche' => 'Header — menu de gauche',
        'menu-droite' => 'Header — menu de droite',
    ]);
});

/**
 * On retire les éléments du header de Storefront (titre du site, recherche,
 * menu, mini-panier...) pour les remplacer par le header de la maquette.
 * Le lien d'évitement « Aller au contenu » (accessibilité) est conservé.
 */
add_action('init', function () {
    remove_action('storefront_header', 'storefront_header_container', 0);
    remove_action('storefront_header', 'storefront_social_icons', 10);
    remove_action('storefront_header', 'storefront_site_branding', 20);
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);
    remove_action('storefront_header', 'storefront_product_search', 40);
    remove_action('storefront_header', 'storefront_header_container_close', 41);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper', 42);
    remove_action('storefront_header', 'storefront_primary_navigation', 50);
    remove_action('storefront_header', 'storefront_header_cart', 60);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper_close', 68);
});

/**
 * Bouton « Panier · N » du header.
 */
function feuillette_lien_panier() {
    $nombre = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $libelle = $nombre > 1 ? $nombre . ' articles' : $nombre . ' article';
    ?>
    <a class="header-feuillette__panier"
       href="<?php echo esc_url(wc_get_cart_url()); ?>"
       aria-label="<?php echo esc_attr('Voir le panier (' . $libelle . ')'); ?>">
        Panier · <?php echo esc_html($nombre); ?>
    </a>
    <?php
}

/**
 * Met à jour le bouton « Panier · N » sans recharger la page
 * quand on ajoute un produit depuis la Boutique.
 */
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start();
    feuillette_lien_panier();
    $fragments['a.header-feuillette__panier'] = ob_get_clean();

    return $fragments;
});

/**
 * Le header de la maquette : menu de gauche, enseigne au centre, menu de droite + panier.
 */
add_action('storefront_header', function () {
    ?>
    <div class="header-feuillette col-full">
        <nav id="site-navigation" class="header-feuillette__nav header-feuillette__nav--gauche" aria-label="Menu principal">
            <?php
            wp_nav_menu([
                'theme_location' => 'menu-gauche',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 1,
            ]);
            ?>
        </nav>

        <a class="header-feuillette__logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/logo-feuillette.svg'); ?>"
                 alt="Le Café Feuillette — retour à l'accueil"
                 width="313" height="145">
        </a>

        <nav class="header-feuillette__nav header-feuillette__nav--droite" aria-label="Menu secondaire">
            <?php
            wp_nav_menu([
                'theme_location' => 'menu-droite',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 1,
            ]);

            feuillette_lien_panier();
            ?>
        </nav>
    </div>
    <?php
}, 20);

/* ==========================================================================
   Footer (US-2.1)
   ========================================================================== */

/**
 * On retire le footer de Storefront (zones de widgets et « Built with WooCommerce »).
 */
add_action('init', function () {
    remove_action('storefront_footer', 'storefront_footer_widgets', 10);
    remove_action('storefront_footer', 'storefront_credit', 20);
});

/**
 * Affiche une liste de liens [texte => adresse].
 * Un lien dont l'adresse est vide (page pas encore publiée) n'est pas affiché.
 */
function feuillette_liste_liens($liens) {
    echo '<ul class="footer-liens">';

    foreach ($liens as $texte => $url) {
        if ($url) {
            echo '<li><a href="' . esc_url($url) . '">' . esc_html($texte) . '</a></li>';
        }
    }

    echo '</ul>';
}

/**
 * Adresse d'une catégorie de produits à partir de son slug ('' si elle n'existe pas).
 */
function feuillette_url_categorie($slug) {
    $url = get_term_link($slug, 'product_cat');

    return is_wp_error($url) ? '' : $url;
}

/**
 * Adresse d'une page, seulement si elle est publiée ('' sinon).
 */
function feuillette_url_page_publiee($page_id) {
    return ($page_id && get_post_status($page_id) === 'publish') ? get_permalink($page_id) : '';
}

/**
 * Footer vert sauge : adresse du café + deux colonnes de liens.
 */
add_action('storefront_footer', function () {
    ?>
    <div class="footer-feuillette">
        <div class="footer-feuillette__colonne">
            <p class="footer-feuillette__titre">Café Feuillette Limoges</p>
            <address class="footer-feuillette__adresse">
                5 Pl. Saint-Michel,<br>
                87000 Limoges
            </address>
        </div>

        <nav class="footer-feuillette__colonne" aria-label="Nos catégories">
            <?php
            feuillette_liste_liens([
                'Boissons chaudes' => feuillette_url_categorie('boissons-chaudes'),
                'Boissons froides' => feuillette_url_categorie('boissons-froides'),
                'Viennoiserie'     => feuillette_url_categorie('viennoiserie'),
            ]);
            ?>
        </nav>

        <nav class="footer-feuillette__colonne" aria-label="Liens utiles">
            <?php
            feuillette_liste_liens([
                'Notre équipe'  => get_post_type_archive_link('membre'),
                'Mon compte'    => wc_get_page_permalink('myaccount'),
                'Mes commandes' => wc_get_account_endpoint_url('orders'),
            ]);
            ?>
        </nav>
    </div>
    <?php
}, 10);

/**
 * Barre du bas vert foncé, sur toute la largeur de l'écran
 * (accrochée après le footer pour sortir du conteneur centré de Storefront).
 * Chaque lien légal s'affiche automatiquement dès que sa page est publiée (itération 22).
 */
add_action('storefront_after_footer', function () {
    $mentions = get_page_by_path('mentions-legales');
    ?>
    <div class="footer-barre">
        <div class="footer-barre__contenu col-full">
            <p class="footer-barre__copyright">© <?php echo esc_html(date_i18n('Y')); ?> Café Feuillette</p>

            <nav aria-label="Informations légales">
                <?php
                feuillette_liste_liens([
                    'Confidentialité'  => get_privacy_policy_url(),
                    'Mentions légales' => feuillette_url_page_publiee($mentions ? $mentions->ID : 0),
                    'CGV'              => feuillette_url_page_publiee(wc_terms_and_conditions_page_id()),
                ]);
                ?>
            </nav>
        </div>
    </div>
    <?php
}, 10);

/* ==========================================================================
   Grille boutique et fiche produit (US-2.3 / US-2.4)
   ========================================================================== */

/**
 * Renvoie le nom de la catégorie principale du produit affiché.
 */
function feuillette_nom_categorie() {
    global $product;

    $categories = get_the_terms($product->get_id(), 'product_cat');

    if (empty($categories) || is_wp_error($categories)) {
        return '';
    }

    return $categories[0]->name;
}

/**
 * Grille boutique (US-2.3) : catégorie au-dessus du nom du produit.
 */
add_action('woocommerce_shop_loop_item_title', function () {
    $categorie = feuillette_nom_categorie();

    if ($categorie) {
        echo '<span class="product-card__category">' . esc_html($categorie) . '</span>';
    }
}, 5);

/**
 * Fiche produit (US-2.4) : catégorie au-dessus du titre.
 */
add_action('woocommerce_single_product_summary', function () {
    $categorie = feuillette_nom_categorie();

    if ($categorie) {
        echo '<span class="product-summary__category">' . esc_html($categorie) . '</span>';
    }
}, 4);

/**
 * Fiche produit (US-2.4) : réorganisation selon la maquette.
 */
add_action('init', function () {
    // Prix déplacé sous la description courte
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);

    // Ligne « Catégorie : ... » retirée (déjà affichée au-dessus du titre)
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

    // Navigation produit précédent / suivant de Storefront retirée
    remove_action('woocommerce_after_single_product_summary', 'storefront_single_product_pagination', 30);

    // « Produits similaires » retirés (remplacés par les ventes croisées à l'itération 13)
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

    // Boutique : menu « Tri par défaut » retiré (absent de la maquette), en haut et en bas de la grille
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10);
    remove_action('woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10);

    // Boutique : le nombre de résultats n'est gardé qu'au-dessus de la grille
    remove_action('woocommerce_after_shop_loop', 'woocommerce_result_count', 20);
});

/* ==========================================================================
   Bloc « Nos offres » (US-2.3)
   ========================================================================== */

/**
 * Sur quelles pages afficher le bloc ?
 * Page d'accueil, Boutique et pages de catégories de produits.
 */
function feuillette_est_page_offres() {
    return is_front_page() || is_shop() || is_product_category();
}

/**
 * Bloc « Nos offres » : bandeau Click & Collect, titre et boutons de catégories.
 * Accroché avant le contenu, pour que le bandeau prenne toute la largeur de l'écran.
 */
add_action('storefront_before_content', function () {
    if (!feuillette_est_page_offres()) {
        return;
    }

    $categories = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => [get_option('default_product_cat')], // sans « Non classé »
    ]);

    $tout_actif = is_shop() || is_front_page();
    ?>
    <section class="offres">
        <div class="offres__hero">
            <h1 class="offres__hero-title">Click &amp; Collect</h1>
            <p class="offres__hero-subtitle">Commandez en ligne ? Retirez à votre café</p>
            <p class="offres__retrait">
                <strong>Retrait :</strong> Café Feuillette Limoges, 5 Pl. Saint-Michel
            </p>
        </div>

        <div class="offres__intro col-full">
            <h2 class="offres__title"><strong>Nos offres</strong> pour vos pauses déjeuner</h2>
            <p class="offres__text">Composez votre commande, personnalisez chaque produit, puis passez la récupérer sans attendre.</p>

            <nav class="offres__filtres" aria-label="Catégories de produits">
                <a class="offres__filtre<?php echo $tout_actif ? ' is-active' : ''; ?>"
                   href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                   <?php echo $tout_actif ? 'aria-current="page"' : ''; ?>>Tout</a>

                <?php foreach ($categories as $categorie) :
                    $actif = is_product_category($categorie->slug); ?>
                    <a class="offres__filtre<?php echo $actif ? ' is-active' : ''; ?>"
                       href="<?php echo esc_url(get_term_link($categorie)); ?>"
                       <?php echo $actif ? 'aria-current="page"' : ''; ?>><?php echo esc_html($categorie->name); ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </section>
    <?php
}, 5);

/**
 * Sur ces pages, le bandeau remplace le titre et le fil d'Ariane par défaut.
 */
add_filter('woocommerce_show_page_title', function ($afficher) {
    return feuillette_est_page_offres() ? false : $afficher;
});

add_action('wp', function () {
    if (!feuillette_est_page_offres()) {
        return;
    }

    remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);

     if (is_front_page()) {
        remove_action('storefront_page', 'storefront_page_header', 10);
    }
});


/* ==========================================================================
   Produits variables (US-3.2)
   ========================================================================== */

/**
 * Script qui transforme les menus déroulants des options en boutons.
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_product()) {
        return;
    }

    $fichier = '/js/feuillette-variations.js';

    wp_enqueue_script(
        'feuillette-variations',
        get_stylesheet_directory_uri() . $fichier,
        ['jquery', 'wc-add-to-cart-variation'],
        filemtime(get_stylesheet_directory() . $fichier),
        true
    );
}, 30);

/**
 * Sous chaque menu déroulant d'options, une rangée de boutons (S, M, L...).
 * Le menu reste dans la page, caché : les boutons le pilotent.
 */
add_filter('woocommerce_dropdown_variation_attribute_options_html', function ($html, $args) {
    $produit  = $args['product'];
    $attribut = $args['attribute'];
    $options  = $args['options'];
    $nom      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribut);

    if (empty($options) && $produit && $attribut) {
        $attributs = $produit->get_variation_attributes();
        $options   = $attributs[$attribut] ?? [];
    }

    if (empty($options) || !$produit) {
        return $html;
    }

    $choix = [];

    if (taxonomy_exists($attribut)) {
        $termes = wc_get_product_terms($produit->get_id(), $attribut, ['fields' => 'all']);

        foreach ($termes as $terme) {
            if (in_array($terme->slug, $options, true)) {
                $choix[$terme->slug] = $terme->name;
            }
        }
    } else {
        foreach ($options as $option) {
            $choix[$option] = $option;
        }
    }

    $boutons = '';

    foreach ($choix as $valeur => $libelle) {
        $boutons .= sprintf(
            '<button type="button" class="feuillette-options__bouton" data-valeur="%s" aria-pressed="false">%s</button>',
            esc_attr($valeur),
            esc_html($libelle)
        );
    }

    return '<div class="feuillette-options__menu">' . $html . '</div>'
        . '<div class="feuillette-options" data-attribut="' . esc_attr($nom) . '">' . $boutons . '</div>';
}, 10, 2);

/**
 * Prix d'un produit variable : « à partir de 4,20 € » au lieu de « 4,20 € – 5,60 € ».
 */
add_filter('woocommerce_variable_price_html', function ($prix_html, $produit) {
    $prix_min = $produit->get_variation_price('min', true);

    return '<span class="prix-a-partir">à partir de</span> ' . wc_price($prix_min);
}, 10, 2);

/**
 * Grille boutique : « Personnaliser » sur le bouton des produits variables.
 */
add_filter('woocommerce_product_add_to_cart_text', function ($texte, $produit) {
    return $produit->is_type('variable') ? 'Personnaliser' : $texte;
}, 10, 2);
/* Fin du bloc US-3.2 */

/* ==========================================================================
   Informations produit : composition, allergènes, valeurs nutritionnelles (US-4.3)
   Les champs sont créés dans SCF > Groupes de champs (groupe « Informations produit »).
   ========================================================================== */
add_action('woocommerce_single_product_summary', function () {
    if (!function_exists('get_field')) {
        return;
    }

    $produit_id  = get_the_ID();
    $composition = get_field('composition', $produit_id);
    $allergenes  = get_field('allergenes', $produit_id);
    $base        = get_field('nutrition_base', $produit_id);

    $champs_nutrition = [
        'energie'          => ['Énergie', 'kcal'],
        'sucres'           => ['Sucres', 'g'],
        'matieres_grasses' => ['Matières grasses', 'g'],
    ];

    $nutrition = [];

    foreach ($champs_nutrition as $champ => [$libelle, $unite]) {
        $valeur = get_field($champ, $produit_id);

        if ($valeur !== null && $valeur !== '' && $valeur !== false) {
            $nutrition[$libelle] = wc_format_localized_decimal($valeur) . ' ' . $unite;
        }
    }

    if (!$composition && empty($allergenes) && empty($nutrition)) {
        return;
    }
    ?>
    <div class="infos-produit">
        <?php if ($composition) : ?>
            <h2 class="infos-produit__titre">Composition</h2>
            <p class="infos-produit__texte"><?php echo nl2br(esc_html($composition)); ?></p>
        <?php endif; ?>

        <?php if (!empty($allergenes)) : ?>
            <h2 class="infos-produit__titre">Allergènes</h2>
            <ul class="infos-produit__allergenes">
                <?php foreach ((array) $allergenes as $allergene) : ?>
                    <li><?php echo esc_html($allergene); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($nutrition)) : ?>
            <h2 class="infos-produit__titre">Valeurs nutritionnelles<?php echo $base ? ' – ' . esc_html($base) : ''; ?></h2>
            <table class="infos-produit__nutrition">
                <?php foreach ($nutrition as $libelle => $valeur) : ?>
                    <tr>
                        <th scope="row"><?php echo esc_html($libelle); ?></th>
                        <td><?php echo esc_html($valeur); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    <?php
}, 22);
/* Fin du bloc US-4 */

/* ==========================================================================
   Page Panier (US-5.3)
   ========================================================================== */

/**
 * Sous-titre sous « Mon panier ».
 */
add_action('woocommerce_before_cart', function () {
    echo '<p class="panier__intro">Vérifiez votre panier, choisissez votre mode de réception, puis validez.</p>';
}, 5);

/**
 * Lien « Retirer » à la place de la petite croix, comme dans la maquette.
 */
add_filter('woocommerce_cart_item_remove_link', function ($lien) {
    return str_replace('&times;', 'Retirer', $lien);
});

/**
 * Lien « Continuer mes achats » sous le bouton de validation.
 */
add_action('woocommerce_proceed_to_checkout', function () {
    echo '<a class="panier__continuer" href="' . esc_url(wc_get_page_permalink('shop')) . '">Continuer mes achats</a>';
}, 30);
/* Fin du bloc US-5 */

/* ==========================================================================
   Checkout (US-6.1 / US-6.2)
   ========================================================================== */

/**
 * Le client a-t-il choisi le retrait en boutique ?
 * (Notre « Retrait en boutique » utilise le mode « Livraison gratuite » de WooCommerce.)
 */
function feuillette_retrait_choisi() {
    if (!WC()->session) {
        return false;
    }

    $modes = (array) WC()->session->get('chosen_shipping_methods');

    foreach ($modes as $mode) {
        if (strpos((string) $mode, 'free_shipping') === 0 || strpos((string) $mode, 'local_pickup') === 0) {
            return true;
        }
    }

    return false;
}

/**
 * Encadré vert « Instructions pour le retrait » dans le récapitulatif de commande,
 * juste après le mode de réception. WooCommerce recalcule ce récapitulatif à chaque
 * changement de mode : l'encadré apparaît ou disparaît donc tout seul.
 * Le texte se modifie dans le back office : Pages > Commande > « Instructions pour le retrait ».
 */
add_action('woocommerce_review_order_after_shipping', function () {
    if (!function_exists('get_field') || !feuillette_retrait_choisi()) {
        return;
    }

    $instructions = get_field('instructions_pour_le_retrait', wc_get_page_id('checkout'));

    if (!$instructions) {
        return;
    }
    ?>
    <tr class="instructions-retrait-ligne">
        <td colspan="2">
            <div class="instructions-retrait">
                <p class="instructions-retrait__titre">Instructions pour le retrait</p>
                <p class="instructions-retrait__texte"><?php echo nl2br(esc_html($instructions)); ?></p>
            </div>
        </td>
    </tr>
    <?php
});

/**
 * Formulaire plus court : pas de champ « Entreprise » ni de complément d'adresse.
 */
add_filter('woocommerce_checkout_fields', function ($champs) {
    unset($champs['billing']['billing_company'], $champs['billing']['billing_address_2']);
    unset($champs['shipping']['shipping_company'], $champs['shipping']['shipping_address_2']);

    return $champs;
});
/* Fin du bloc US-6 */

/* ==========================================================================
   Espace client (US-7.2)
   ========================================================================== */

/**
 * Menu du compte : on retire « Téléchargements », la boutique ne vend aucun produit téléchargeable.
 */
add_filter('woocommerce_account_menu_items', function ($onglets) {
    unset($onglets['downloads']);

    return $onglets;
});
/* Fin du bloc US-7 */

/* ==========================================================================
   E-mails transactionnels (US-8.2 / US-8.3)
   ========================================================================== */

/**
 * Couleurs de l'enseigne dans les e-mails. WooCommerce « incruste » ce CSS
 * dans chaque balise (les messageries ignorent souvent les feuilles de style).
 * Les e-mails ne chargent pas la police Arena Uno : on utilise Arial.
 */
add_filter('woocommerce_email_styles', function ($css) {
    return $css . '
        #wrapper { background-color: #F3F0E8; }
        #template_container { border: 1px solid #1D1D1B; border-radius: 8px; box-shadow: none; }
        #template_header { background-color: #1D1D1B; border-radius: 8px 8px 0 0; }
        #template_header h1 { color: #FFFFFF; font-family: Arial, sans-serif; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; text-shadow: none; }
        #body_content { background-color: #FFFFFF; }
        #body_content_inner { color: #1D1D1B; font-family: Arial, sans-serif; }
        h2 { color: #A98E58; font-family: Arial, sans-serif; font-size: 16px; text-transform: uppercase; }
        a { color: #A98E58; }
        #credit { color: #1D1D1B; font-family: Arial, sans-serif; }
        .instructions-retrait-email { margin: 0 0 24px; padding: 16px 20px; border-radius: 8px; background-color: #A4B08B; color: #1D1D1B; }
        .instructions-retrait-email h2 { margin: 0 0 8px; color: #1D1D1B; font-size: 14px; }
        .instructions-retrait-email p { margin: 0; }
    ';
});

/**
 * Instructions de retrait dans les e-mails des commandes en retrait en boutique (US-8.3).
 * Le texte est celui du champ SCF de la page Commande (itération 6) :
 * l'administrateur ne le modifie qu'à un seul endroit.
 */
add_action('woocommerce_email_before_order_table', function ($commande, $envoye_admin, $texte_brut, $email) {
    if ($envoye_admin || !function_exists('get_field')) {
        return;
    }

    $emails_concernes = ['customer_on_hold_order', 'customer_processing_order'];

    if (!$email || !in_array($email->id, $emails_concernes, true)) {
        return;
    }

    $retrait = false;

    foreach ($commande->get_shipping_methods() as $mode) {
        if (in_array($mode->get_method_id(), ['free_shipping', 'local_pickup', 'pickup_location'], true)) {
            $retrait = true;
        }
    }

    $instructions = get_field('instructions_pour_le_retrait', wc_get_page_id('checkout'));

    if (!$retrait || !$instructions) {
        return;
    }

    if ($texte_brut) {
        echo "\nINSTRUCTIONS POUR LE RETRAIT\n" . wp_strip_all_tags($instructions) . "\n\n";
        return;
    }

    echo '<div class="instructions-retrait-email">';
    echo '<h2>Instructions pour le retrait</h2>';
    echo '<p>' . nl2br(esc_html($instructions)) . '</p>';
    echo '</div>';
}, 10, 4);
/* Fin du bloc US-8 */

/* ==========================================================================
   Commandes (US-9.3)
   ========================================================================== */

/**
 * Le client peut annuler lui-même, depuis son espace client, une commande
 * en attente de paiement ou en préparation (pas encore retirée ni expédiée).
 */
add_filter('woocommerce_valid_order_statuses_for_cancel', function ($statuts) {
    return array_unique(array_merge($statuts, ['on-hold', 'processing']));
});
/* Fin du bloc US-9 */

/* ==========================================================
 * US-10.2 : champ code promo dans l'encadré récapitulatif
 * ========================================================== */

// 1. Formulaire « Code promo » en haut de l'encadré Total panier
add_action( 'woocommerce_before_cart_totals', 'feuillette_coupon_dans_recap' );
function feuillette_coupon_dans_recap() {
	if ( ! wc_coupons_enabled() ) {
		return;
	}
	$codes = WC()->cart->get_applied_coupons();
	?>
	<form class="feuillette-coupon" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<label for="feuillette-coupon-code" class="feuillette-coupon__titre">Code promo</label>
		<div class="feuillette-coupon__ligne">
			<input type="text" name="coupon_code" id="feuillette-coupon-code" placeholder="Votre code" autocomplete="off">
			<button type="submit" name="apply_coupon" value="1" class="feuillette-coupon__ok">OK</button>
		</div>
		<?php if ( $codes ) : ?>
			<p class="feuillette-coupon__info">Code appliqué : <?php echo esc_html( strtoupper( implode( ', ', $codes ) ) ); ?></p>
		<?php endif; ?>
		<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce', false ); ?>
	</form>
	<?php
}

// 2. Libellé de la ligne de remise : « Code CAPPU20 »
add_filter( 'woocommerce_cart_totals_coupon_label', 'feuillette_libelle_coupon', 10, 2 );
function feuillette_libelle_coupon( $label, $coupon ) {
	return 'Code ' . strtoupper( $coupon->get_code() );
}

// 3. Lien de retrait : « Retirer » au lieu de « [Enlever] »
add_filter( 'woocommerce_cart_totals_coupon_html', 'feuillette_lien_retirer_coupon' );
function feuillette_lien_retirer_coupon( $html ) {
	return str_replace( __( '[Remove]', 'woocommerce' ), 'Retirer', $html );
}
/* Fin du bloc US-10.2 */

/* ==========================================================
 * US-10.2 (finition) : codes utilisés affichés dans la ligne Remise
 * (e-mails, page de confirmation, Mon compte)
 * ========================================================== */
add_filter( 'woocommerce_get_order_item_totals', 'feuillette_remise_avec_codes', 10, 2 );
function feuillette_remise_avec_codes( $lignes, $order ) {
	$codes = $order->get_coupon_codes();
	if ( isset( $lignes['discount'] ) && $codes ) {
		$lignes['discount']['label'] = 'Remise (' . esc_html( strtoupper( implode( ', ', $codes ) ) ) . ') :';
	}
	return $lignes;
}
/* Fin du bloc US-10.2 finition */

/* ==========================================================
 * US-11.1 : type de contenu « Membre de l'équipe »
 * Liste : /equipe/  —  Profil : /equipe/prenom-nom/
 * ========================================================== */
add_action( 'init', 'feuillette_type_membre' );
function feuillette_type_membre() {
	register_post_type( 'membre', array(
		'labels' => array(
			'name'                  => 'Équipe',
			'singular_name'         => 'Membre',
			'menu_name'             => 'Équipe',
			'all_items'             => 'Tous les membres',
			'add_new'               => 'Ajouter un membre',
			'add_new_item'          => 'Ajouter un membre de l’équipe',
			'edit_item'             => 'Modifier le membre',
			'new_item'              => 'Nouveau membre',
			'view_item'             => 'Voir le profil',
			'search_items'          => 'Rechercher un membre',
			'not_found'             => 'Aucun membre trouvé',
			'not_found_in_trash'    => 'Aucun membre dans la corbeille',
			'featured_image'        => 'Portrait',
			'set_featured_image'    => 'Choisir le portrait',
			'remove_featured_image' => 'Retirer le portrait',
			'use_featured_image'    => 'Utiliser comme portrait',
			'archives'              => 'Notre équipe',
		),
		'public'        => true,
		'has_archive'   => 'equipe',
		'rewrite'       => array( 'slug' => 'equipe', 'with_front' => false ),
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'show_in_rest'  => true,
	) );
}

// Texte d'aide dans le champ titre : « Prénom Nom »
add_filter( 'enter_title_here', 'feuillette_titre_membre', 10, 2 );
function feuillette_titre_membre( $texte, $post ) {
	return ( 'membre' === $post->post_type ) ? 'Prénom Nom' : $texte;
}
/* Fin du bloc US-11.1 */

/* ==========================================================
 * US-11.3 : réglages communs aux pages Équipe
 * ========================================================== */

// Pleine largeur (pas de barre latérale) sur la liste et les profils
add_filter( 'body_class', 'feuillette_equipe_pleine_largeur' );
function feuillette_equipe_pleine_largeur( $classes ) {
	if ( is_post_type_archive( 'membre' ) || is_singular( 'membre' ) ) {
		$classes[] = 'storefront-full-width-content';
		$classes   = array_diff( $classes, array( 'right-sidebar', 'left-sidebar' ) );
	}
	return $classes;
}

// Fil d'Ariane retiré du haut de page : les templates le placent eux-mêmes
add_action( 'wp', 'feuillette_equipe_retirer_fil' );
function feuillette_equipe_retirer_fil() {
	if ( is_post_type_archive( 'membre' ) || is_singular( 'membre' ) ) {
		remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
		remove_action( 'storefront_content_top', 'woocommerce_breadcrumb', 10 );
	}
}

// Fil d'Ariane des pages Équipe (séparateur « / » comme la maquette)
function feuillette_equipe_afficher_fil() {
	woocommerce_breadcrumb( array(
		'delimiter'   => '<span class="equipe-fil__sep" aria-hidden="true">/</span>',
		'wrap_before' => '<nav class="woocommerce-breadcrumb equipe-fil" aria-label="Fil d’Ariane">',
		'wrap_after'  => '</nav>',
	) );
}

// Nom du membre : « Claire <strong>Martin</strong> »
function feuillette_nom_membre( $post_id = null ) {
	$titre  = get_the_title( $post_id );
	$parties = explode( ' ', $titre, 2 );
	if ( count( $parties ) < 2 ) {
		return esc_html( $titre );
	}
	return esc_html( $parties[0] ) . ' <strong>' . esc_html( $parties[1] ) . '</strong>';
}
/* Fin du bloc US-11.3 reglages */

/* ==========================================================
 * US-11.5 : accès à l'équipe (fil d'Ariane et menu)
 * ========================================================== */

// Fil d'Ariane : « Équipe » au lieu de « Membre »
add_filter( 'woocommerce_get_breadcrumb', 'feuillette_fil_equipe_libelle' );
function feuillette_fil_equipe_libelle( $crumbs ) {
	$lien = get_post_type_archive_link( 'membre' );
	foreach ( $crumbs as $i => $crumb ) {
		if ( isset( $crumb[1] ) && $crumb[1] === $lien ) {
			$crumbs[ $i ][0] = 'Équipe';
		}
	}
	return $crumbs;
}

// Lien « Notre équipe » allumé sur la liste et sur chaque profil
add_filter( 'nav_menu_css_class', 'feuillette_menu_equipe_actif', 10, 2 );
function feuillette_menu_equipe_actif( $classes, $item ) {
	if ( ( is_post_type_archive( 'membre' ) || is_singular( 'membre' ) )
		&& untrailingslashit( $item->url ) === untrailingslashit( get_post_type_archive_link( 'membre' ) ) ) {
		$classes[] = 'current-menu-item';
	}
	return $classes;
}
/* Fin du bloc US-11.5 */

/* ==========================================================
 * US-12.1 : taxonomie « Collection » pour les produits
 * Indépendante des catégories. Pages : /collection/nom-de-la-collection/
 * ========================================================== */
add_action( 'init', 'feuillette_taxonomie_collection' );
function feuillette_taxonomie_collection() {
	register_taxonomy( 'collection', array( 'product' ), array(
		'labels' => array(
			'name'          => 'Collections',
			'singular_name' => 'Collection',
			'menu_name'     => 'Collections',
			'all_items'     => 'Toutes les collections',
			'edit_item'     => 'Modifier la collection',
			'view_item'     => 'Voir la collection',
			'update_item'   => 'Mettre à jour la collection',
			'add_new_item'  => 'Ajouter une collection',
			'new_item_name' => 'Nom de la nouvelle collection',
			'search_items'  => 'Rechercher une collection',
			'not_found'     => 'Aucune collection trouvée',
			'back_to_items' => '← Retour aux collections',
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'collection', 'with_front' => false ),
	) );
}
/* Fin du bloc US-12.1 */

/* US-12.1 (finition) : colonne « Collections » lisible dans la liste des produits */
add_action( 'admin_head', 'feuillette_admin_colonne_collections' );
function feuillette_admin_colonne_collections() {
	echo '<style>.wp-list-table .column-taxonomy-collection { width: 11%; }</style>';
}
/* Fin du bloc US-12.1 finition */

/* US-12.3 : pages Collection, fil d'Ariane sous le bandeau (comme la page Équipe) */
add_action( 'wp', 'feuillette_collection_fil_sous_bandeau' );
function feuillette_collection_fil_sous_bandeau() {
	if ( is_tax( 'collection' ) ) {
		remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
		remove_action( 'storefront_content_top', 'woocommerce_breadcrumb', 10 );
		add_action( 'woocommerce_before_shop_loop', 'feuillette_equipe_afficher_fil', 5 );
	}
}
/* Fin du bloc US-12.3 fil */

/* ==========================================================
 * US-12.3 : bloc « Nos collections » (maquette : sous la grille, avant le footer)
 * Accueil, Boutique, catégories et pages de collection
 * ========================================================== */
add_action( 'storefront_before_footer', 'feuillette_bloc_collections', 5 );
function feuillette_bloc_collections() {
	if ( ! ( feuillette_est_page_offres() || is_tax( 'collection' ) ) ) {
		return;
	}
	$collections = get_terms( array(
		'taxonomy'   => 'collection',
		'hide_empty' => true,
		'orderby'    => 'term_id',
	) );
	if ( empty( $collections ) || is_wp_error( $collections ) ) {
		return;
	}
	?>
	<section class="collections-bloc" aria-labelledby="collections-bloc-titre">
		<div class="col-full">
			<h2 id="collections-bloc-titre" class="collections-bloc__titre">Nos collections</h2>
			<p class="collections-bloc__texte">Parcourez la carte autrement</p>
			<ul class="collections-bloc__liste">
				<?php foreach ( $collections as $collection ) :
					$active = is_tax( 'collection', $collection->term_id ); ?>
					<li>
						<a class="collections-bloc__lien<?php echo $active ? ' is-active' : ''; ?>"
						   href="<?php echo esc_url( get_term_link( $collection ) ); ?>"
						   <?php echo $active ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $collection->name ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
}
/* Fin du bloc US-12.3 collections */

/* ==========================================================
 * US-13.2 / US-13.3 : suggestions « Pour accompagner » (ventes croisées)
 * ========================================================== */

// Cartes de suggestions, partagées entre la fiche produit et le panier
function feuillette_cartes_suggestions( $ids, $titre_suite ) {
	$ids = array_slice( array_filter( array_map( 'absint', (array) $ids ) ), 0, 3 );
	if ( empty( $ids ) ) {
		return;
	}
	?>
	<section class="suggestions" aria-labelledby="suggestions-titre">
		<h2 id="suggestions-titre" class="suggestions__titre"><strong>Pour accompagner</strong> <?php echo esc_html( $titre_suite ); ?></h2>
		<ul class="suggestions__liste">
			<?php foreach ( $ids as $id ) :
				$produit = wc_get_product( $id );
				if ( ! $produit || ! $produit->is_visible() ) {
					continue;
				}
				$simple = $produit->is_type( 'simple' ) && $produit->is_purchasable() && $produit->is_in_stock();
				$prix   = $produit->is_type( 'variable' )
					? 'dès ' . wc_price( $produit->get_variation_price( 'min', true ) )
					: wc_price( wc_get_price_to_display( $produit ) );
				?>
				<li class="suggestion">
					<a class="suggestion__photo" href="<?php echo esc_url( $produit->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
						<?php echo $produit->get_image( 'woocommerce_thumbnail', array( 'alt' => '' ) ); ?>
					</a>
					<div class="suggestion__texte">
						<h3 class="suggestion__nom"><a href="<?php echo esc_url( $produit->get_permalink() ); ?>"><?php echo esc_html( $produit->get_name() ); ?></a></h3>
						<p class="suggestion__prix"><?php echo wp_kses_post( $prix ); ?></p>
						<a class="suggestion__bouton"
						   href="<?php echo esc_url( $simple ? $produit->add_to_cart_url() : $produit->get_permalink() ); ?>"
						   aria-label="<?php echo esc_attr( ( $simple ? 'Ajouter au panier : ' : 'Personnaliser : ' ) . $produit->get_name() ); ?>"><?php echo $simple ? 'Ajouter' : 'Personnaliser'; ?></a>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
}
/* Fin du morceau 1 US-13 */

// Fiche produit : avant les onglets et les avis, comme la maquette
add_action( 'woocommerce_after_single_product_summary', 'feuillette_suggestions_fiche', 5 );
function feuillette_suggestions_fiche() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$suite = has_term( 'viennoiserie', 'product_cat', $product->get_id() ) ? 'votre viennoiserie' : 'votre boisson';
	feuillette_cartes_suggestions( $product->get_cross_sell_ids(), $suite );
}

// Panier : nos cartes sous la liste des produits, à la place de l'affichage par défaut
add_action( 'init', function () {
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
} );
add_action( 'woocommerce_after_cart_table', 'feuillette_suggestions_panier' );
function feuillette_suggestions_panier() {
	feuillette_cartes_suggestions( WC()->cart->get_cross_sells(), 'votre commande' );
}
/* Fin du bloc US-13 */
