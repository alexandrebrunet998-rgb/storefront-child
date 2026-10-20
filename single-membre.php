<?php
/**
 * Template : profil d'un membre de l'équipe (US-11.3)
 * Adresse : /equipe/prenom-nom/
 */
get_header();

while ( have_posts() ) :
	the_post();
	$membre_id     = get_the_ID();
	$depuis        = get_field( 'au_cafe_depuis' );
	$boisson       = get_field( 'boisson_preferee' );
	$coup_de_coeur = get_field( 'coup_de_coeur' );
	?>
<div id="primary" class="content-area">
	<main id="main" class="site-main equipe equipe--profil" role="main">

		<?php feuillette_equipe_afficher_fil(); ?>

		<article class="membre">
			<div class="membre__photo">
				<?php the_post_thumbnail( 'large', array( 'alt' => 'Portrait de ' . get_the_title() ) ); ?>
			</div>

			<div class="membre__texte">
				<p class="membre__poste"><?php echo esc_html( get_field( 'poste' ) ); ?></p>
				<h1 class="membre__nom"><?php echo feuillette_nom_membre(); ?></h1>
				<div class="membre__presentation"><?php the_content(); ?></div>

				<dl class="membre__infos">
					<?php if ( $depuis ) : ?>
						<div><dt>Au café depuis</dt><dd><?php echo esc_html( $depuis ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $boisson ) : ?>
						<div><dt>Boisson préférée</dt><dd><?php echo esc_html( $boisson ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $coup_de_coeur ) : ?>
						<div><dt>Coup de cœur de la carte</dt><dd><a href="<?php echo esc_url( get_permalink( $coup_de_coeur ) ); ?>"><?php echo esc_html( get_the_title( $coup_de_coeur ) ); ?></a></dd></div>
					<?php endif; ?>
					<div><dt>Café</dt><dd>Limoges</dd></div>
				</dl>

				<div class="membre__actions">
					<a class="equipe-bouton equipe-bouton--contour" href="<?php echo esc_url( get_post_type_archive_link( 'membre' ) ); ?>">← Toute l’équipe</a>
					<a class="equipe-bouton equipe-bouton--plein" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Commander</a>
				</div>
			</div>
		</article>
<!-- Fin du morceau 1 -->

		<?php
		$collegues = new WP_Query( array(
			'post_type'      => 'membre',
			'posts_per_page' => -1,
			'post__not_in'   => array( $membre_id ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );
		if ( $collegues->have_posts() ) :
			?>
			<section class="collegues" aria-labelledby="collegues-titre">
				<h2 id="collegues-titre" class="collegues__titre">Ses collègues</h2>
				<ul class="collegues__liste">
					<?php while ( $collegues->have_posts() ) : $collegues->the_post(); ?>
						<li>
							<a class="collegue" href="<?php the_permalink(); ?>">
								<span class="collegue__photo"><?php the_post_thumbnail( 'thumbnail', array( 'alt' => '' ) ); ?></span>
								<span class="collegue__texte">
									<span class="collegue__nom"><?php the_title(); ?></span>
									<span class="collegue__poste"><?php echo esc_html( get_field( 'poste' ) ); ?></span>
								</span>
							</a>
						</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
			</section>
		<?php endif; ?>

	</main>
</div>
	<?php
endwhile;
get_footer();
