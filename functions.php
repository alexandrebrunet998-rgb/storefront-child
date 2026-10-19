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

        <nav class="footer-feuillette__colonne" aria-label="Espace client">
            <?php
            feuillette_liste_liens([
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
