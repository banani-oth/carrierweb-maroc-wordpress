<?php
/**
 * Vidéo de démonstration
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section
	id="video-demo"
	class="relative py-24 lg:py-32 bg-cw-black overflow-hidden"
	aria-label="<?php echo esc_attr( (string) cw_t( 'videoShowcase.label' ) ); ?>"
	data-cw-video-section
>
	<div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-cw-red/[0.03] rounded-full blur-[160px] -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>

	<div class="relative max-w-6xl mx-auto px-4 sm:px-6">
		<div class="text-center max-w-2xl mx-auto mb-12" data-reveal="up">
			<div class="section-label justify-center"><?php cw_e( 'videoShowcase.label' ); ?></div>
			<h2 class="section-title text-3xl md:text-4xl lg:text-5xl mb-4 font-[family-name:var(--font-jakarta)]">
				<?php cw_e( 'videoShowcase.titlePre' ); ?><span class="gradient-text"><?php cw_e( 'videoShowcase.titleHl' ); ?></span><?php cw_e( 'videoShowcase.titlePost' ); ?>
			</h2>
			<p class="text-cw-gray-400 text-sm sm:text-base leading-relaxed">
				<?php cw_e( 'videoShowcase.desc' ); ?>
			</p>
		</div>

		<div class="relative" data-reveal="up" data-reveal-delay="200">
			<div class="absolute inset-0 bg-cw-red/10 rounded-2xl blur-[50px] scale-95 pointer-events-none"></div>
			<div class="relative rounded-2xl overflow-hidden border border-white/[0.08] shadow-2xl bg-cw-surface">
				<?php
				/*
				 * Pas d'attribut autoplay : c'est le JS qui lance la lecture à
				 * l'entrée dans le viewport. Sans JS, la vidéo reste avec ses
				 * contrôles natifs — donc toujours consultable.
				 */
				?>
				<video
					class="w-full h-auto block"
					src="<?php echo esc_url( cw_media_url( 'demo_video', CW_URI . '/assets/video/carrierweb-demo.mp4' ) ); ?>"
					muted
					loop
					playsinline
					preload="metadata"
					controls
					aria-label="<?php echo esc_attr( (string) cw_t( 'videoShowcase.videoAlt' ) ); ?>"
					data-cw-video
				></video>
			</div>
		</div>
	</div>
</section>
