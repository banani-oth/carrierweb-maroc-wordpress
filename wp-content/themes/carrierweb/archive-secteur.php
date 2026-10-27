<?php
/**
 * Listing des secteurs — /secteurs
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_sectors = cw_get_sectors();
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-[110px] pb-20">

	<?php
	get_template_part(
		'template-parts/blocks/breadcrumb-hero',
		null,
		array(
			'breadcrumb' => (string) cw_t( 'listing.sectors.breadcrumb' ),
			'label'      => (string) cw_t( 'listing.sectors.label' ),
			'title_pre'  => (string) cw_t( 'listing.sectors.h1Pre' ),
			'title_hl'   => (string) cw_t( 'listing.sectors.h1Hl' ),
			'desc'       => (string) cw_t( 'listing.sectors.desc' ),
		)
	);
	?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
			<?php foreach ( $cw_sectors as $cw_sec ) : ?>
				<?php get_template_part( 'template-parts/cards/sector-card', null, array( 'sector' => $cw_sec ) ); ?>
			<?php endforeach; ?>
		</div>
	</section>

	<?php // Renvoi croisé vers l'entrée « par technologie ». ?>
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
		<div class="bg-gradient-to-br from-cw-red/10 to-cw-surface border border-cw-red/20 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
			<div>
				<h2 class="text-lg font-bold text-cw-fg font-[family-name:var(--font-jakarta)] mb-1">
					<?php cw_e( 'listing.sectors.crossTitle' ); ?>
				</h2>
				<p class="text-cw-gray-400 text-xs leading-relaxed max-w-xl">
					<?php cw_e( 'listing.sectors.crossDesc' ); ?>
				</p>
			</div>
			<a href="<?php echo esc_url( cw_editor_link( 'solutions', cw_url( '/solutions' ) ) ); ?>" class="cta-outline text-xs px-5 py-3 flex-shrink-0">
				<?php cw_e( 'listing.sectors.crossCta' ); ?>
				<?php cw_icon( 'arrow-right', 14 ); ?>
			</a>
		</div>
	</section>

</main>

<?php
get_footer();
