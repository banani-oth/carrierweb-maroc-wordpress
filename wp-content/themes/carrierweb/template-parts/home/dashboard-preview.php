<?php
/**
 * Aperçu du tableau de bord
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_features      = (array) cw_t( 'dashboard.features', array() );
$cw_kpi_labels    = (array) cw_t( 'dashboard.kpiLabels', array() );
$cw_feature_icons = array( 'eye', 'bell', 'file-text' );

$cw_values = (array) cw_t( 'dashboard.kpiValues', array( '48/50', '2', 'OK' ) );
$cw_kpis = array(
	array( 'label' => $cw_kpi_labels[0] ?? '', 'val' => $cw_values[0] ?? '', 'accent' => 'text-cw-green' ),
	array( 'label' => $cw_kpi_labels[1] ?? '', 'val' => $cw_values[1] ?? '', 'accent' => 'text-cw-amber' ),
	array( 'label' => $cw_kpi_labels[2] ?? '', 'val' => $cw_values[2] ?? '', 'accent' => 'text-cw-green' ),
);

// Points clignotants le long du tracé — positions décoratives.
$cw_dots = array(
	array( '78%', '6%' ),
	array( '58%', '38%' ),
	array( '60%', '62%' ),
	array( '16%', '92%' ),
);
?>
<section
	id="tableau-de-bord"
	class="relative py-24 lg:py-32 bg-cw-black-deep overflow-hidden"
	aria-label="<?php echo esc_attr( (string) cw_t( 'dashboard.label' ) ); ?>"
>
	<div class="absolute top-1/2 right-0 w-[500px] h-[500px] bg-cw-red/[0.02] rounded-full blur-[150px] -translate-y-1/2 pointer-events-none"></div>

	<div class="relative max-w-7xl mx-auto px-4 sm:px-6">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

			<div class="lg:col-span-5" data-reveal="up">
				<div class="section-label"><?php cw_e( 'dashboard.label' ); ?></div>
				<h2 class="section-title text-3xl md:text-4xl lg:text-5xl mb-4 font-[family-name:var(--font-jakarta)]">
					<?php cw_e( 'dashboard.titlePre' ); ?><span class="gradient-text"><?php cw_e( 'dashboard.titleHl' ); ?></span>
				</h2>
				<p class="text-cw-gray-400 text-sm leading-relaxed mb-8"><?php cw_e( 'dashboard.desc' ); ?></p>

				<div class="space-y-6">
					<?php foreach ( $cw_features as $cw_i => $cw_feature ) : ?>
						<div class="flex items-start gap-4" data-reveal="up" data-reveal-delay="<?php echo esc_attr( (string) ( 200 + $cw_i * 100 ) ); ?>">
							<div class="w-8 h-8 rounded-lg bg-cw-red/10 border border-cw-red/20 flex items-center justify-center flex-shrink-0 text-cw-red-light">
								<?php cw_icon( $cw_feature_icons[ $cw_i ] ?? 'eye', 16 ); ?>
							</div>
							<div>
								<h3 class="text-cw-fg font-bold text-sm mb-1 font-[family-name:var(--font-jakarta)]">
									<?php echo esc_html( (string) ( $cw_feature['text'] ?? '' ) ); ?>
								</h3>
								<p class="text-cw-gray-400 text-xs leading-relaxed">
									<?php echo esc_html( (string) ( $cw_feature['description'] ?? '' ) ); ?>
								</p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="lg:col-span-7 relative" data-reveal="up" data-reveal-delay="300">
				<div class="absolute inset-0 bg-cw-red/10 rounded-2xl blur-[40px] scale-95"></div>

				<div class="relative rounded-2xl overflow-hidden border border-white/[0.08] shadow-2xl bg-cw-surface p-3">
					<?php // Chrome de navigateur factice — reste en LTR même en arabe. ?>
					<div class="flex items-center gap-1.5 border-b border-white/[0.04] pb-3 mb-3 px-1" dir="ltr">
						<span class="w-2.5 h-2.5 rounded-full bg-cw-red/40"></span>
						<span class="w-2.5 h-2.5 rounded-full bg-cw-amber/40"></span>
						<span class="w-2.5 h-2.5 rounded-full bg-cw-green/40"></span>
						<span class="text-[10px] text-cw-gray-600 ml-2 font-medium"><?php cw_e( 'dashboard.previewUrl', 'https://portal.carrierweb.ma' ); ?></span>
					</div>

					<div class="bg-cw-black/80 rounded-xl overflow-hidden h-[360px] relative p-3 flex flex-col gap-3">
						<div class="grid grid-cols-3 gap-2.5">
							<?php foreach ( $cw_kpis as $cw_kpi ) : ?>
								<div class="bg-cw-surface-2 border border-white/[0.05] rounded-lg px-3 py-2.5">
									<div class="text-lg font-black font-[family-name:var(--font-jakarta)] <?php echo esc_attr( $cw_kpi['accent'] ); ?>">
										<?php echo esc_html( $cw_kpi['val'] ); ?>
									</div>
									<div class="text-[9px] text-cw-gray-500 uppercase tracking-wide">
										<?php echo esc_html( $cw_kpi['label'] ); ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="relative flex-1 bg-cw-surface rounded-lg border border-white/[0.05] overflow-hidden">
							<div class="absolute inset-0 bg-grid-pattern opacity-60"></div>
							<svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 200" preserveAspectRatio="none" aria-hidden="true">
								<path d="M 20 170 Q 120 120 200 130 T 380 40" fill="none" stroke="rgba(191, 32, 38, 0.5)" stroke-width="2" stroke-dasharray="6 5" />
							</svg>

							<?php foreach ( $cw_dots as $cw_dot ) : ?>
								<span class="pulse-dot absolute" style="top: <?php echo esc_attr( $cw_dot[0] ); ?>; left: <?php echo esc_attr( $cw_dot[1] ); ?>"></span>
							<?php endforeach; ?>

							<div class="absolute top-[42%] left-[38%] -translate-x-1/2 bg-cw-black/90 border border-cw-red/30 rounded-md px-2 py-1 text-[9px] text-cw-fg font-medium shadow-lg">
								<?php cw_e( 'dashboard.vehicleTag' ); ?>
								<span class="block text-cw-gray-500"><?php cw_e( 'dashboard.vehicleRoute' ); ?></span>
							</div>

							<div class="absolute bottom-2 right-2.5 text-[8px] text-cw-gray-600 uppercase tracking-wider">
								<?php cw_e( 'dashboard.demoData' ); ?>
							</div>
						</div>
					</div>
				</div>

				<div class="absolute -bottom-4 -left-4 bg-cw-red text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg font-[family-name:var(--font-jakarta)]">
					<?php cw_e( 'dashboard.badge' ); ?>
				</div>
			</div>

		</div>
	</div>
</section>
