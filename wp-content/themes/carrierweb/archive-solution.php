<?php
/**
 * Listing des solutions — /solutions
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_solutions = cw_get_solutions();
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-[110px] pb-20">

	<?php
	get_template_part(
		'template-parts/blocks/breadcrumb-hero',
		null,
		array(
			'breadcrumb' => (string) cw_t( 'listing.solutions.breadcrumb' ),
			'label'      => (string) cw_t( 'listing.solutions.label' ),
			'title_pre'  => (string) cw_t( 'listing.solutions.h1Pre' ),
			'title_hl'   => (string) cw_t( 'listing.solutions.h1Hl' ),
			'desc'       => (string) cw_t( 'listing.solutions.desc' ),
		)
	);
	?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php foreach ( $cw_solutions as $cw_sol ) : ?>
				<?php get_template_part( 'template-parts/cards/solution-card', null, array( 'solution' => $cw_sol ) ); ?>
			<?php endforeach; ?>
		</div>
	</section>

	<?php // Renvoi croisé vers l'entrée « par métier ». ?>
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
		<div class="bg-gradient-to-br from-cw-red/10 to-cw-surface border border-cw-red/20 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
			<div>
				<h2 class="text-lg font-bold text-cw-fg font-[family-name:var(--font-jakarta)] mb-1">
					<?php cw_e( 'listing.solutions.crossTitle' ); ?>
				</h2>
				<p class="text-cw-gray-400 text-xs leading-relaxed max-w-xl">
					<?php cw_e( 'listing.solutions.crossDesc' ); ?>
				</p>
			</div>
			<a href="<?php echo esc_url( cw_url( '/secteurs' ) ); ?>" class="cta-outline text-xs px-5 py-3 flex-shrink-0">
				<?php cw_e( 'listing.solutions.crossCta' ); ?>
				<?php cw_icon( 'arrow-right', 14 ); ?>
			</a>
		</div>
	</section>

</main>

<?php
get_footer();
