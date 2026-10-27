<?php
/**
 * Page « Pourquoi CarrierWeb » — /pourquoi-carrierweb
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_specs = cw_page_list( 'page_pourquoi', 'spec', array( 'title', 'desc' ), 4, 'whyPage.specs' );

$cw_side = cw_page_list( 'page_pourquoi', 'side', array( 'title', 'desc' ), 3, 'whyPage.sideBlocks' );

// Les « points ✓ » sont des champs simples numérotés (check_1..3), pas des
// lignes multi-champs : cw_page_list ne s'applique pas.
$cw_checks     = array();
$cw_dict_check = (array) cw_t( 'whyPage.checks', array() );

for ( $cw_i = 1; $cw_i <= 3; $cw_i++ ) {
	$cw_value = cw_page_text( 'page_pourquoi', 'check_' . $cw_i, '', (string) ( $cw_dict_check[ $cw_i - 1 ] ?? '' ) );

	if ( '' !== $cw_value ) {
		$cw_checks[] = $cw_value;
	}
}

if ( function_exists( 'cw_editor_has_override' ) && cw_editor_has_override( 'whyPage.checks' ) ) { $cw_checks = (array) cw_t( 'whyPage.checks', array() ); }

$cw_spec_icons = array( 'cpu', 'wifi', 'shield', 'server' );
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-32 pb-12">

	<section class="relative py-16 bg-gradient-to-b from-cw-red/10 to-cw-black overflow-hidden border-b border-white/[0.04]">
		<div class="absolute inset-0 bg-gradient-to-t from-cw-black via-transparent to-transparent"></div>
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

			<nav class="flex items-center gap-1.5 text-xs text-cw-gray-500 mb-6" aria-label="<?php echo esc_attr( (string) cw_t( 'common.breadcrumbAria' ) ); ?>">
				<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.home' ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
				<span class="text-cw-gray-300" aria-current="page"><?php cw_e( 'whyPage.breadcrumb' ); ?></span>
			</nav>

			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
				<div class="flex items-start gap-4">
					<div class="w-14 h-14 rounded-2xl bg-cw-red/15 border border-cw-red/30 flex items-center justify-center text-cw-red-light flex-shrink-0 shadow-lg shadow-cw-red/5">
						<?php cw_icon( 'cpu', 32 ); ?>
					</div>
					<div>
						<span class="section-label mb-3"><?php echo esc_html( cw_page_text( 'page_pourquoi', 'label', 'whyPage.label' ) ); ?></span>
						<h1 class="page-title"><?php echo esc_html( cw_page_text( 'page_pourquoi', 'h1', 'whyPage.h1' ) ); ?></h1>
					</div>
				</div>
				<p class="page-desc max-w-xl sm:text-right">
					<?php echo esc_html( cw_page_text( 'page_pourquoi', 'tagline', 'whyPage.tagline' ) ); ?>
				</p>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $cw_specs ) ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
			<div class="text-center mb-10">
				<h2 class="text-xl sm:text-2xl font-bold text-cw-fg font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( cw_page_text( 'page_pourquoi', 'specs_title', 'whyPage.specsTitle' ) ); ?>
				</h2>
				<p class="text-cw-gray-500 text-xs mt-1">
					<?php echo esc_html( cw_page_text( 'page_pourquoi', 'specs_desc', 'whyPage.specsDesc' ) ); ?>
				</p>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				<?php foreach ( $cw_specs as $cw_i => $cw_spec ) : ?>
					<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 hover:border-cw-red/30 transition-all group space-y-3">
						<div class="w-9 h-9 rounded-lg bg-cw-red/10 flex items-center justify-center text-cw-red-light group-hover:bg-cw-red group-hover:text-white transition-colors">
							<?php cw_icon( $cw_spec_icons[ $cw_i ] ?? 'cpu', 18 ); ?>
						</div>
						<h3 class="text-cw-fg font-bold text-sm font-[family-name:var(--font-jakarta)]"><?php echo esc_html( $cw_spec['title'] ); ?></h3>
						<p class="text-cw-gray-400 text-xs leading-relaxed"><?php echo esc_html( $cw_spec['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 grid grid-cols-1 lg:grid-cols-12 gap-10">
		<div class="lg:col-span-8 space-y-8">
			<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 sm:p-8 space-y-6 shadow-md">
				<h2 class="text-lg font-bold text-cw-fg border-l-3 border-cw-red pl-3 font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( cw_page_text( 'page_pourquoi', 'perf_title', 'whyPage.perfTitle' ) ); ?>
				</h2>
				<p class="text-cw-gray-300 text-sm leading-relaxed">
					<?php echo esc_html( cw_page_text( 'page_pourquoi', 'perf_p1', 'whyPage.perfP1' ) ); ?>
				</p>
				<p class="text-cw-gray-300 text-sm leading-relaxed">
					<?php echo esc_html( cw_page_text( 'page_pourquoi', 'perf_p2', 'whyPage.perfP2' ) ); ?>
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
					<?php echo esc_html( cw_page_text( 'page_pourquoi', 'side_title', 'whyPage.sideTitle' ) ); ?>
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

	<div class="mt-16">
		<?php get_template_part( 'template-parts/home/cta' ); ?>
	</div>

</main>

<?php
get_footer();
