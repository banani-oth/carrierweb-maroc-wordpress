<?php
/**
 * Hero
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_title_a  = cw_setting( 'heroTitleLine1', 'hero.titleA' );
$cw_title_hl = cw_setting( 'heroTitleHighlight', 'hero.titleBhl' );
$cw_title_c  = cw_setting( 'heroTitleLine3', 'hero.titleC' );
$cw_subtitle = cw_setting( 'heroSubtitle', 'hero.subtitle' );

$cw_proofs      = (array) cw_t( 'hero.proofs', array() );
$cw_proof_icons = array( 'fuel', 'map-pin', 'headset' );
?>
<section
	id="hero"
	class="relative min-h-[92vh] flex items-center overflow-hidden pt-[72px]"
	aria-label="<?php echo esc_attr( (string) cw_t( 'hero.sectionAria' ) ); ?>"
>
	<div class="absolute inset-0 z-0">
		<?php
		/*
		 * Équivalent de <Image fill priority> : l'image du hero est le LCP de
		 * l'accueil, donc jamais en lazy-load et déclarée fetchpriority="high".
		 */
		?>
		<img
			src="<?php echo esc_url( cw_media_url( 'hero', CW_URI . '/assets/img/page-home-2.webp' ) ); ?>"
			alt="<?php echo esc_attr( cw_media_alt( 'hero', (string) cw_t( 'hero.imgAlt' ) ) ); ?>"
			fetchpriority="high"
			decoding="async"
			class="absolute inset-0 w-full h-full object-cover object-[center_65%]"
		>
		<div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-transparent"></div>
		<div class="absolute inset-0 bg-gradient-to-t from-cw-black via-transparent to-transparent"></div>
	</div>

	<div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 w-full py-24 lg:py-32">
		<div class="max-w-3xl">
			<p class="section-label animate-fadeInDown"><?php cw_e( 'hero.label' ); ?></p>

			<h1 class="page-title page-title--hero mb-7 animate-fadeInUp">
				<?php echo esc_html( $cw_title_a ); ?>
				<br>
				<?php cw_e( 'hero.titleBpre' ); ?><span class="text-cw-red-light"><?php echo esc_html( $cw_title_hl ); ?></span><span class="text-cw-red">.</span>
				<br>
				<?php echo esc_html( $cw_title_c ); ?><span class="text-cw-red">.</span>
			</h1>

			<p class="text-base sm:text-lg md:text-xl text-white/80 mb-8 leading-relaxed max-w-xl animate-fadeInUp" style="animation-delay: 120ms">
				<?php echo esc_html( $cw_subtitle ); ?>
			</p>

			<div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-10 animate-fadeInUp" style="animation-delay: 220ms">
				<a href="<?php echo esc_url( cw_editor_link( 'hero_primary', cw_url( '/contact' ) ) ); ?>" class="cta-primary justify-center text-sm sm:text-base px-7 sm:px-8 py-4">
					<?php cw_e( 'hero.ctaPrimary' ); ?>
					<?php cw_icon( 'arrow-right', 17 ); ?>
				</a>
				<a
					href="<?php echo esc_url( cw_editor_link( 'hero_secondary', cw_url( '/solutions' ) ) ); ?>"
					class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 backdrop-blur-md text-white font-semibold text-sm sm:text-base px-7 sm:px-8 py-4 hover:bg-white/20 transition-colors font-[family-name:var(--font-jakarta)]"
				>
					<?php cw_e( 'hero.ctaSecondary' ); ?>
				</a>
			</div>

			<ul class="flex flex-wrap gap-x-6 gap-y-3 animate-fadeInUp" style="animation-delay: 320ms">
				<?php foreach ( $cw_proofs as $cw_i => $cw_proof ) : ?>
					<li class="flex items-center gap-2 text-xs sm:text-sm text-white/85">
						<span class="w-6 h-6 rounded-md bg-cw-red/15 border border-cw-red/25 flex items-center justify-center text-cw-red-light flex-shrink-0">
							<?php cw_icon( $cw_proof_icons[ $cw_i ] ?? 'zap', 15 ); ?>
						</span>
						<?php echo esc_html( (string) $cw_proof ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-cw-black to-transparent z-10"></div>
</section>
