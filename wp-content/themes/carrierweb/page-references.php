<?php
/**
 * Page « Références » — /references
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_metrics = cw_page_list( 'page_references', 'metric', array( 'val', 'label', 'desc' ), 3, 'referencesPage.metrics' );
$cw_side    = cw_page_list( 'page_references', 'side', array( 'title', 'desc' ), 4, 'referencesPage.sideBlocks' );

$cw_checks     = array();
$cw_dict_check = (array) cw_t( 'referencesPage.checks', array() );

for ( $cw_i = 1; $cw_i <= 4; $cw_i++ ) {
	$cw_value = cw_page_text( 'page_references', 'check_' . $cw_i, '', (string) ( $cw_dict_check[ $cw_i - 1 ] ?? '' ) );

	if ( '' !== $cw_value ) {
		$cw_checks[] = $cw_value;
	}
}

if ( function_exists( 'cw_editor_has_override' ) && cw_editor_has_override( 'referencesPage.checks' ) ) { $cw_checks = (array) cw_t( 'referencesPage.checks', array() ); }

$cw_testimonials = cw_get_testimonials();


?>

<main id="main-content" class="bg-cw-black min-h-screen pt-32 pb-12">

	<section class="relative py-16 bg-gradient-to-b from-cw-red/10 to-cw-black overflow-hidden border-b border-white/[0.04]">
		<div class="absolute inset-0 bg-gradient-to-t from-cw-black via-transparent to-transparent"></div>
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

			<nav class="flex items-center gap-1.5 text-xs text-cw-gray-500 mb-6" aria-label="<?php echo esc_attr( (string) cw_t( 'common.breadcrumbAria' ) ); ?>">
				<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.home' ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
				<span class="text-cw-gray-300" aria-current="page"><?php cw_e( 'referencesPage.breadcrumb' ); ?></span>
			</nav>

			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
				<div class="flex items-start gap-4">
					<div class="w-14 h-14 rounded-2xl bg-cw-red/15 border border-cw-red/30 flex items-center justify-center text-cw-red-light flex-shrink-0 shadow-lg shadow-cw-red/5">
						<?php cw_icon( 'map-pin', 32 ); ?>
					</div>
					<div>
						<span class="section-label mb-3"><?php echo esc_html( cw_page_text( 'page_references', 'label', 'referencesPage.label' ) ); ?></span>
						<h1 class="page-title"><?php echo esc_html( cw_page_text( 'page_references', 'h1', 'referencesPage.h1' ) ); ?></h1>
					</div>
				</div>
				<p class="page-desc max-w-xl sm:text-right">
					<?php echo esc_html( cw_page_text( 'page_references', 'tagline', 'referencesPage.tagline' ) ); ?>
				</p>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $cw_metrics ) ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<?php foreach ( $cw_metrics as $cw_m ) : ?>
					<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 text-center hover:border-cw-red/30 transition-all space-y-1">
						<div class="text-3xl font-extrabold text-cw-red-light font-[family-name:var(--font-jakarta)]"><?php echo esc_html( $cw_m['val'] ); ?></div>
						<h2 class="text-cw-fg font-bold text-sm font-[family-name:var(--font-jakarta)]"><?php echo esc_html( $cw_m['label'] ); ?></h2>
						<p class="text-cw-gray-500 text-xs leading-relaxed"><?php echo esc_html( $cw_m['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 grid grid-cols-1 lg:grid-cols-12 gap-10">
		<div class="lg:col-span-8 space-y-8">
			<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 sm:p-8 space-y-6 shadow-md">
				<h2 class="text-lg font-bold text-cw-fg border-l-3 border-cw-red pl-3 font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( cw_page_text( 'page_references', 'presence_title', 'referencesPage.presenceTitle' ) ); ?>
				</h2>
				<p class="text-cw-gray-300 text-sm leading-relaxed">
					<?php echo esc_html( cw_page_text( 'page_references', 'presence_p1', 'referencesPage.presenceP1' ) ); ?>
				</p>
				<p class="text-cw-gray-300 text-sm leading-relaxed">
					<?php echo esc_html( cw_page_text( 'page_references', 'presence_p2', 'referencesPage.presenceP2' ) ); ?>
				</p>

				<?php if ( ! empty( $cw_checks ) ) : ?>
					<ul class="space-y-3 pt-4 border-t border-white/[0.04]">
						<?php foreach ( $cw_checks as $cw_check ) : ?>
							<li class="flex items-center gap-2 text-xs text-cw-gray-300">
								<span class="text-cw-red-light inline-flex"><?php cw_icon( 'check', 18 ); ?></span>
								<span><?php echo esc_html( $cw_check ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>

		<div class="lg:col-span-4 space-y-6">
			<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 sm:p-8 space-y-6 shadow-md">
				<h2 class="text-sm font-bold text-cw-fg border-b border-white/[0.04] pb-3 uppercase tracking-wider font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( cw_page_text( 'page_references', 'side_title', 'referencesPage.sideTitle' ) ); ?>
				</h2>
				<div class="space-y-4">
					<?php foreach ( $cw_side as $cw_block ) : ?>
						<div class="space-y-1">
							<h3 class="text-cw-red-light font-semibold text-xs"><?php echo esc_html( $cw_block['title'] ); ?></h3>
							<p class="text-cw-gray-500 text-[11px] leading-relaxed"><?php echo esc_html( $cw_block['desc'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $cw_testimonials ) ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
			<div class="text-center mb-10">
				<h2 class="text-xl sm:text-2xl font-bold text-cw-fg font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( cw_page_text( 'page_references', 'refs_title', 'referencesPage.refsTitle' ) ); ?>
				</h2>
				<p class="text-cw-gray-500 text-xs mt-1">
					<?php echo esc_html( cw_page_text( 'page_references', 'refs_desc', 'referencesPage.refsDesc' ) ); ?>
				</p>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<?php foreach ( $cw_testimonials as $cw_item ) : ?>
					<figure class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 hover:border-cw-red/30 transition-all flex flex-col gap-4">
						<span class="text-cw-red-light flex-shrink-0 inline-flex"><?php cw_icon( 'quote', 24 ); ?></span>
						<blockquote class="text-cw-gray-300 text-xs leading-relaxed flex-1">
							<?php echo esc_html( (string) ( $cw_item['quote'] ?? '' ) ); ?>
						</blockquote>
						<figcaption class="pt-4 border-t border-white/[0.04]">
							<div class="text-cw-fg font-semibold text-xs"><?php echo esc_html( (string) ( $cw_item['author'] ?? '' ) ); ?></div>
							<div class="text-cw-gray-500 text-[11px]">
								<?php echo esc_html( trim( ( $cw_item['role'] ?? '' ) . ' — ' . ( $cw_item['company'] ?? '' ), ' —' ) ); ?>
							</div>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
			<p class="text-cw-gray-600 text-[11px] text-center mt-6"><?php cw_e( 'testimonials.note' ); ?></p>
		</section>
	<?php endif; ?>

	<div class="mt-16">
		<?php get_template_part( 'template-parts/home/cta' ); ?>
	</div>

</main>

<?php
get_footer();
