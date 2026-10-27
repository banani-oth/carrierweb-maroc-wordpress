<?php
/**
 * Section « Pourquoi CarrierWeb »
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_features = cw_home_list(
	'why_feature',
	array( 'title', 'desc' ),
	3,
	'why.features',
	array( 'desc' => 'description' )
);

$cw_metrics = cw_home_list(
	'why_metric',
	array( 'val', 'label', 'desc' ),
	3,
	'why.metrics'
);

$cw_feature_icons = array( 'clock', 'trending-down', 'bar-chart-3' );
?>
<section
	id="pourquoi-carrierweb"
	class="relative py-24 lg:py-32 bg-cw-black overflow-hidden"
	aria-label="<?php echo esc_attr( cw_home_text( 'why_label', 'why.label' ) ); ?>"
>
	<div class="relative max-w-7xl mx-auto px-4 sm:px-6">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

			<div class="lg:col-span-7 space-y-6">
				<div class="section-label"><?php cw_home_e( 'why_label', 'why.label' ); ?></div>
				<h2 class="section-title text-3xl md:text-4xl lg:text-5xl font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( cw_home_text( 'why_title_pre', 'why.titlePre' ) ); ?><span class="gradient-text"><?php echo esc_html( cw_home_text( 'why_title_hl', 'why.titleHl' ) ); ?></span>
				</h2>
				<p class="text-cw-gray-400 text-base leading-relaxed">
					<?php cw_home_e( 'why_paragraph', 'why.paragraph' ); ?>
				</p>

				<div class="space-y-6 mt-8">
					<?php foreach ( $cw_features as $cw_i => $cw_feature ) : ?>
						<div class="flex gap-4">
							<div class="w-12 h-12 rounded-xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light flex-shrink-0">
								<?php cw_icon( $cw_feature_icons[ $cw_i ] ?? 'zap', 28 ); ?>
							</div>
							<div>
								<h3 class="text-cw-fg font-semibold text-base font-[family-name:var(--font-jakarta)] mb-1">
									<?php echo esc_html( $cw_feature['title'] ); ?>
								</h3>
								<p class="text-cw-gray-400 text-sm leading-relaxed">
									<?php echo esc_html( $cw_feature['desc'] ); ?>
								</p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<a
					href="<?php echo esc_url( cw_url( '/pourquoi-carrierweb' ) ); ?>"
					class="inline-flex items-center gap-1.5 text-sm font-semibold text-cw-red-light hover:text-cw-fg transition-colors mt-2 group"
				>
					<?php cw_e( 'why.link' ); ?>
					<span class="group-hover:translate-x-1 transition-transform inline-flex"><?php cw_icon( 'arrow-right', 15 ); ?></span>
				</a>
			</div>

			<div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-5" data-reveal="right">
				<?php foreach ( $cw_metrics as $cw_metric ) : ?>
					<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 hover:border-cw-red/30 transition-all group">
						<div class="text-3xl font-black text-cw-red-light font-[family-name:var(--font-jakarta)] mb-1 group-hover:scale-105 transition-transform duration-300 origin-left">
							<?php echo esc_html( $cw_metric['val'] ); ?>
						</div>
						<div class="text-sm font-bold text-cw-fg mb-1.5 font-[family-name:var(--font-jakarta)]">
							<?php echo esc_html( $cw_metric['label'] ); ?>
						</div>
						<div class="text-xs text-cw-gray-500 leading-relaxed">
							<?php echo esc_html( $cw_metric['desc'] ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
