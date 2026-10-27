<?php
/**
 * Gabarit de repli — obligatoire dans tout thème WordPress.
 *
 * Il ne sert qu'aux vues qui n'ont pas encore de gabarit dédié (les phases 2 à
 * 5 en ajoutent un par vue). Il affiche l'en-tête et le pied de page réels,
 * plus un résumé du contenu, afin qu'une URL non encore portée reste navigable
 * au lieu de renvoyer une page blanche.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="flex-1 pt-[110px] pb-24">
	<div class="max-w-7xl mx-auto px-4 sm:px-6">

		<?php if ( have_posts() ) : ?>

			<h1 class="page-title mb-8">
				<?php
				if ( is_post_type_archive() ) {
					post_type_archive_title();
				} elseif ( is_singular() ) {
					the_title();
				} else {
					echo esc_html( get_the_archive_title() );
				}
				?>
			</h1>

			<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="cw-card p-6">
						<h2 class="text-lg font-semibold text-cw-fg mb-2">
							<a href="<?php the_permalink(); ?>" class="hover:text-cw-red-light transition-colors">
								<?php the_title(); ?>
							</a>
						</h2>
						<p class="text-sm text-cw-gray-400">
							<?php echo esc_html( get_post_type() ); ?>
						</p>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'class'     => 'mt-12 flex justify-center gap-2 text-sm',
					'mid_size'  => 2,
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
				)
			);
			?>

		<?php else : ?>

			<h1 class="page-title mb-4"><?php esc_html_e( 'Aucun contenu', 'carrierweb' ); ?></h1>
			<p class="page-desc">
				<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="text-cw-red-light hover:text-cw-fg transition-colors">
					<?php cw_e( 'common.home' ); ?>
				</a>
			</p>

		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
