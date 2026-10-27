<?php
/**
 * Grille des 4 secteurs
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_sectors = cw_nav_sectors();
?>
<section
	id="secteurs"
	class="relative py-24 lg:py-32 bg-cw-black-deep"
	aria-label="<?php echo esc_attr( cw_home_text( 'sectors_label', 'sectorsHome.label' ) ); ?>"
>
	<div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-cw-red/20 to-transparent"></div>

	<div class="relative max-w-7xl mx-auto px-4 sm:px-6">

		<div class="text-center mb-16">
			<div class="section-label"><?php cw_home_e( 'sectors_label', 'sectorsHome.label' ); ?></div>
			<h2 class="section-title text-3xl md:text-4xl lg:text-5xl mb-4 font-[family-name:var(--font-jakarta)]">
				<?php echo esc_html( cw_home_text( 'sectors_title_pre', 'sectorsHome.titlePre' ) ); ?><span class="gradient-text"><?php echo esc_html( cw_home_text( 'sectors_title_hl', 'sectorsHome.titleHl' ) ); ?></span>
			</h2>
			<p class="section-desc mx-auto text-center"><?php cw_home_e( 'sectors_desc', 'sectorsHome.desc' ); ?></p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
			<?php foreach ( $cw_sectors as $cw_i => $cw_sector ) : ?>
				<a
					href="<?php echo esc_url( cw_nav_url( $cw_sector['href'] ) ); ?>"
					class="sector-card group flex flex-col justify-between"
					data-reveal="up"
					data-reveal-delay="<?php echo esc_attr( (string) ( $cw_i * 100 ) ); ?>"
				>
					<div>
						<div class="w-16 h-16 rounded-2xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light mx-auto mb-6 group-hover:bg-cw-red group-hover:text-white group-hover:scale-110 transition-all duration-300">
							<?php cw_icon( $cw_sector['icon'], 36 ); ?>
						</div>

						<h3 class="text-cw-fg font-bold text-base mb-3 font-[family-name:var(--font-jakarta)] group-hover:text-cw-red-light transition-colors">
							<?php echo esc_html( $cw_sector['label'] ); ?>
						</h3>

						<p class="text-cw-gray-400 text-xs leading-relaxed mb-6">
							<?php echo esc_html( $cw_sector['desc'] ); ?>
						</p>
					</div>

					<div class="flex items-center justify-center gap-1.5 text-cw-gray-500 text-xs font-semibold group-hover:text-cw-red-light transition-colors">
						<span><?php cw_e( 'sectorsHome.discover' ); ?></span>
						<span class="group-hover:translate-x-1 transition-transform inline-flex"><?php cw_icon( 'arrow-right', 12 ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
