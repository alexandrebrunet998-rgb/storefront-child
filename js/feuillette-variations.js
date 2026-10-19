/**
 * Café Feuillette — Options des produits variables en boutons (US-3.2)
 *
 * Les menus déroulants de WooCommerce restent dans la page (cachés en CSS).
 * Les boutons ne font que les piloter : WooCommerce continue de gérer
 * les combinaisons possibles, le prix et le stock de chaque variante.
 */
jQuery(function ($) {
    var $formulaire = $('form.variations_form');

    if (!$formulaire.length) {
        return;
    }

    // Le grand prix en haut de la fiche (« à partir de ... »)
    var $prix = $('.summary p.price').first();
    var prixInitial = $prix.html();

    // Renvoie le menu déroulant caché qui correspond à une rangée de boutons
    function menuDe($rangee) {
        return $formulaire.find('select[name="' + $rangee.data('attribut') + '"]');
    }

    // Clic sur un bouton : on choisit (ou on retire) la valeur dans le menu caché
    $formulaire.on('click', '.feuillette-options__bouton', function () {
        var $bouton = $(this);
        var $menu = menuDe($bouton.closest('.feuillette-options'));
        var valeur = $bouton.hasClass('is-active') ? '' : String($bouton.data('valeur'));

        $menu.val(valeur).trigger('change');
    });

    // Met les boutons à jour : option choisie en noir, options impossibles désactivées
    function synchroniser() {
        $formulaire.find('.feuillette-options').each(function () {
            var $rangee = $(this);
            var $menu = menuDe($rangee);
            var valeurChoisie = $menu.val();

            $rangee.find('.feuillette-options__bouton').each(function () {
                var $bouton = $(this);
                var valeur = String($bouton.data('valeur'));
                var $option = $menu.find('option').filter(function () {
                    return this.value === valeur;
                });
                var choisie = valeurChoisie === valeur;

                $bouton.toggleClass('is-active', choisie);
                $bouton.attr('aria-pressed', choisie ? 'true' : 'false');
                $bouton.prop('disabled', !$option.length || $option.prop('disabled'));
            });
        });
    }

    $formulaire.on('woocommerce_update_variation_values reset_data', synchroniser);
    $formulaire.on('change', 'select', synchroniser);

    // Quand une combinaison complète est choisie : le grand prix affiche son prix
    $formulaire.on('found_variation', function (evenement, variante) {
        if (variante.price_html) {
            $prix.html(variante.price_html);
        }
    });

    // Quand le choix est effacé : retour à « à partir de ... »
    $formulaire.on('reset_data', function () {
        $prix.html(prixInitial);
    });

    synchroniser();
});