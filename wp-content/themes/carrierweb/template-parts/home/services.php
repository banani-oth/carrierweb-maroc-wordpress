<?php
/**
 * Grille des 6 solutions
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_solutions = cw_nav_solutions();
?>
<section
	id="services-grid"
	class="relative z-20 py-24 lg:py-32 bg-cw-black"
	aria-label="<?php echo esc_attr( cw_home_text( 'services_label', 'services.label' ) ); ?>"
>
	<div class="max-w-7xl mx-auto px-4 sm:px-6">

		<div class="text-center mb-16">
			<div class="section-label"><?php cw_home_e( 'services_label', 'services.label' ); ?></div>
			<h2 class="section-title text-3xl md:text-4xl lg:text-5xl mb-4 font-[family-name:var(--font-jakarta)]">
				<?php echo esc_html( cw_home_text( 'services_title_pre', 'services.titlePre' ) ); ?><span class="gradient-text"><?php echo esc_html( cw_home_text( 'services_title_hl', 'services.titleHl' ) ); ?></span>
			</h2>
			<p class="section-desc mx-auto text-center"><?php cw_home_e( 'services_desc', 'services.desc' ); ?></p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php foreach ( $cw_solutions as $cw_i => $cw_sol ) : ?>
				<a
					href="<?php echo esc_url( cw_nav_url( $cw_sol['href'] ) ); ?>"
					class="service-card flex flex-col justify-between"
					data-reveal="up"
					data-reveal-delay="<?php echo esc_attr( (string) ( $cw_i * 100 ) ); ?>"
				>
					<div>
						<div class="flex items-start justify-between mb-6">
							<div class="w-14 h-14 rounded-xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light">
								<?php cw_icon( $cw_sol['icon'], 28 ); ?>
							</div>
							<?php // Numéro décoratif « 01 », « 02 »… ?>
							<span aria-hidden="true" class="text-5xl font-extrabold leading-none text-cw-fg/[0.05] font-[family-name:var(--font-jakarta)] select-none">
								<?php echo esc_html( str_pad( (string) ( $cw_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>
							</span>
						</div>
						<h3 class="text-cw-fg font-bold text-lg mb-2.5 font-[family-name:var(--font-jakarta)]">
							<?php echo esc_html( $cw_sol['label'] ); ?>
						</h3>
						<p class="text-cw-gray-400 text-sm leading-relaxed mb-6">
							<?php echo esc_html( $cw_sol['desc'] ); ?>
						</p>
					</div>

					<div class="flex items-center gap-1.5 text-xs font-semibold text-cw-red-light group">
						<span><?php cw_e( 'services.discover' ); ?></span>
						<span class="group-hover:translate-x-1 transition-transform inline-flex"><?php cw_icon( 'arrow-right', 14 ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
