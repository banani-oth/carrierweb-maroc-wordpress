<?php
/**
 * Page secteur — /secteurs/{slug}
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_sec     = cw_get_sector( get_the_ID() );
$cw_related = cw_get_related( 'secteur', (int) get_the_ID(), 3 );
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-32 pb-12">

	<section class="relative py-16 bg-gradient-to-b from-cw-red/10 to-cw-black overflow-hidden border-b border-white/5">
		<div class="absolute inset-0 bg-gradient-to-t from-cw-black via-transparent to-transparent"></div>
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

			<nav class="flex items-center gap-1.5 text-xs text-cw-gray-500 mb-6" aria-label="<?php echo esc_attr( (string) cw_t( 'common.breadcrumbAria' ) ); ?>">
				<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.home' ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
				<a href="<?php echo esc_url( cw_url( '/secteurs' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.sectorsLabel' ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
				<span class="text-cw-gray-300" aria-current="page"><?php echo esc_html( $cw_sec['title'] ); ?></span>
			</nav>

			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
				<div class="flex items-start gap-4">
					<div class="w-14 h-14 rounded-2xl bg-cw-red/15 border border-cw-red/30 flex items-center justify-center text-cw-red-light flex-shrink-0 shadow-lg shadow-cw-red/5">
						<?php cw_icon( $cw_sec['icon'], 32 ); ?>
					</div>
					<div>
						<span class="section-label mb-3"><?php cw_e( 'sectorDetail.badge' ); ?></span>
						<h1 class="page-title"><?php echo esc_html( $cw_sec['title'] ); ?></h1>
					</div>
				</div>
				<p class="page-desc max-w-xl sm:text-right"><?php echo esc_html( $cw_sec['tagline'] ); ?></p>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $cw_sec['useCases'] ) ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
			<div class="text-center mb-10">
				<h2 class="text-xl sm:text-2xl font-black text-cw-fg"><?php cw_e( 'sectorDetail.useCasesTitle' ); ?></h2>
				<p class="text-cw-gray-400 text-xs mt-1"><?php cw_e( 'sectorDetail.useCasesDesc' ); ?></p>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				<?php foreach ( $cw_sec['useCases'] as $cw_uc ) : ?>
					<div class="bg-cw-surface border border-white/5 rounded-2xl p-6 hover:border-cw-red/30 transition-all group space-y-3">
						<div class="w-9 h-9 rounded-lg bg-cw-red/10 flex items-center justify-center text-cw-red-light group-hover:bg-cw-red group-hover:text-white transition-colors">
							<?php cw_icon( 'check-circle-2', 18 ); ?>
						</div>
						<h3 class="text-cw-fg font-bold text-sm"><?php echo esc_html( $cw_uc['title'] ); ?></h3>
						<p class="text-cw-gray-400 text-xs leading-relaxed"><?php echo esc_html( $cw_uc['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 grid grid-cols-1 lg:grid-cols-12 gap-10">

		<div class="lg:col-span-8 space-y-8">
			<div class="bg-cw-surface border border-white/5 rounded-2xl p-6 sm:p-8 space-y-6 shadow-md">
				<h2 class="text-lg font-bold text-cw-fg border-l-3 border-cw-red pl-3"><?php cw_e( 'sectorDetail.challengesTitle' ); ?></h2>
				<?php // whitespace-pre-line : la description contient des retours à la ligne saisis dans l'admin. ?>
				<p class="text-cw-gray-300 text-sm leading-relaxed whitespace-pre-line"><?php echo esc_html( $cw_sec['description'] ); ?></p>
			</div>

			<?php if ( '' !== $cw_sec['seoRichContent'] ) : ?>
				<div class="bg-cw-surface/50 border border-white/5 rounded-2xl p-6 sm:p-8 space-y-4">
					<h2 class="text-lg font-bold text-cw-fg border-l-3 border-cw-red pl-3"><?php cw_e( 'sectorDetail.expertiseTitle' ); ?></h2>
					<p class="text-cw-gray-400 text-xs leading-relaxed"><?php echo esc_html( $cw_sec['seoRichContent'] ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<div class="lg:col-span-4 space-y-8">
			<?php if ( ! empty( $cw_sec['recommendedSolutions'] ) ) : ?>
				<div class="bg-cw-surface border border-white/5 rounded-2xl p-6 space-y-5">
					<h2 class="text-sm font-bold text-cw-fg border-b border-white/5 pb-3 uppercase tracking-wider">
						<?php cw_e( 'sectorDetail.recommendedTitle' ); ?>
					</h2>
					<div class="space-y-3">
						<?php foreach ( $cw_sec['recommendedSolutions'] as $cw_rec ) : ?>
							<a
								href="<?php echo esc_url( cw_url( $cw_rec['href'] ) ); ?>"
								class="flex items-center justify-between p-3 rounded-lg bg-cw-black hover:bg-cw-red/10 border border-white/5 hover:border-cw-red/20 text-xs text-cw-fg font-medium transition-all group"
							>
								<span><?php echo esc_html( $cw_rec['title'] ); ?></span>
								<span class="text-cw-gray-500 group-hover:text-cw-red-light group-hover:translate-x-1 transition-all inline-flex"><?php cw_icon( 'arrow-right', 14 ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="bg-gradient-to-br from-cw-red/15 to-cw-surface border border-cw-red/20 rounded-2xl p-6 text-center space-y-4 shadow-lg shadow-cw-red/5">
				<h2 class="text-base font-bold text-cw-fg"><?php cw_e( 'sectorDetail.expertBoxTitle' ); ?></h2>
				<p class="text-cw-gray-300 text-xs leading-relaxed"><?php cw_e( 'sectorDetail.expertBoxDesc' ); ?></p>
				<a href="<?php echo esc_url( cw_editor_link( 'demo', cw_url( '/contact' ) ) ); ?>" class="cta-primary text-xs w-full justify-center py-3">
					<span><?php cw_e( 'sectorDetail.expertBoxCta' ); ?></span>
					<?php cw_icon( 'arrow-right', 14 ); ?>
				</a>
			</div>
		</div>
	</section>

	<?php
	get_template_part(
		'template-parts/blocks/related-grid',
		null,
		array(
			'items'    => $cw_related,
			'label'    => (string) cw_t( 'sectorDetail.relatedLabel' ),
			'title'    => (string) cw_t( 'sectorDetail.relatedTitle' ),
			'desc_key' => 'listingDescription',
		)
	);
	?>

	<div class="mt-8">
		<?php get_template_part( 'template-parts/home/cta' ); ?>
	</div>

</main>

<?php
get_footer();
