/* US-18.3 : bouton du filtre de prix
   - aucun filtre actif : « Filtrer » (comportement normal de WooCommerce)
   - un filtre actif : « Réinitialiser », qui ramène au catalogue sans filtre
   - si le visiteur bouge le curseur : redevient « Filtrer » */
jQuery(function ($) {
	var $bouton = $('.widget_price_filter .price_slider_amount .button');
	if (!$bouton.length) {
		return;
	}

	var params = new URLSearchParams(window.location.search);
	var filtreActif = false;
	params.forEach(function (valeur, cle) {
		if (cle === 'min_price' || cle === 'max_price' || cle.indexOf('filter_') === 0) {
			filtreActif = true;
		}
	});
	if (!filtreActif) {
		return;
	}

	// Adresse sans filtre (on garde la recherche si le visiteur en a fait une)
	var urlSansFiltre = window.location.pathname;
	if (params.get('s')) {
		urlSansFiltre += '?s=' + encodeURIComponent(params.get('s')) + '&post_type=product';
	}

	$bouton.text('Réinitialiser').on('click.feuillette', function (e) {
		e.preventDefault();
		window.location.href = urlSansFiltre;
	});

	// Le visiteur choisit un nouveau prix : le bouton redevient « Filtrer »
	$(document.body).on('price_slider_change', function () {
		$bouton.text('Filtrer').off('click.feuillette');
	});
});
