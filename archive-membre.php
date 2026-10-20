<?php
/**
 * Template : liste des membres de l'équipe (US-11.3)
 * Adresse : /equipe/
 */
get_header();

$membres = new WP_Query( array(
	'post_type'      => 'membre',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main equipe" role="main">

		<header class="equipe-bandeau">
			<h1 class="equipe-bandeau__titre">Notre équipe</h1>
			<p class="equipe-bandeau__intro">Ceux qui préparent vos boissons et vos gourmandises, chaque jour au Café Feuillette Limoges.</p>
		</header>

		<?php feuillette_equipe_afficher_fil(); ?>

		<?php if ( $membres->have_posts() ) : ?>
			<ul class="equipe-grille">
				<?php while ( $membres->have_posts() ) : $membres->the_post(); ?>
					<li class="equipe-carte">
						<a class="equipe-carte__photo" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php the_post_thumbnail( 'large', array( 'alt' => '' ) ); ?>
						</a>
						<p class="equipe-carte__poste"><?php echo esc_html( get_field( 'poste' ) ); ?></p>
						<h2 class="equipe-carte__nom"><?php echo feuillette_nom_membre(); ?></h2>
						<?php if ( get_field( 'accroche' ) ) : ?>
							<p class="equipe-carte__accroche"><?php echo esc_html( get_field( 'accroche' ) ); ?></p>
						<?php endif; ?>
						<a class="equipe-bouton equipe-bouton--contour" href="<?php the_permalink(); ?>">Voir le profil<span class="screen-reader-text"> de <?php the_title(); ?></span></a>
					</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		<?php else : ?>
			<p>Aucun membre pour le moment.</p>
		<?php endif; ?>

		<section class="equipe-cta">
			<h2 class="equipe-cta__titre"><strong>Passez nous voir</strong> ou commandez en ligne</h2>
			<p>Retrait gratuit au Café Feuillette Limoges.</p>
			<a class="equipe-bouton equipe-bouton--plein" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Commander</a>
		</section>

	</main>
</div>
<?php
get_footer();
