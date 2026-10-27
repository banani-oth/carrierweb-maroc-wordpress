<?php
/**
 * Bandeau final
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_phone    = cw_setting( 'contactPhone', '', '+212 (0)5 22 36 19 88' );
$cw_tel_href = 'tel:' . preg_replace( '/[^\d+]/', '', $cw_phone );
?>
<section
	id="cta"
	class="relative py-24 lg:py-32 overflow-hidden bg-cw-black-deep"
	aria-label="<?php echo esc_attr( cw_home_text( 'cta_label', 'cta.label' ) ); ?>"
>
	<div class="absolute inset-0 bg-gradient-to-br from-cw-red/[0.04] via-cw-black to-cw-red/[0.04]"></div>
	<div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-cw-red/20 to-transparent"></div>

	<div class="relative max-w-4xl mx-auto px-4 sm:px-6 text-center" data-reveal="up">
		<div class="section-label mx-auto w-fit mb-6"><?php cw_home_e( 'cta_label', 'cta.label' ); ?></div>

		<h2 class="section-title text-3xl md:text-4xl lg:text-5xl mb-6 font-[family-name:var(--font-jakarta)]">
			<?php echo esc_html( cw_home_text( 'cta_title_pre', 'cta.titlePre' ) ); ?><span class="gradient-text"><?php echo esc_html( cw_home_text( 'cta_title_hl', 'cta.titleHl' ) ); ?></span>
		</h2>

		<p class="section-desc mx-auto text-center mb-10 text-cw-gray-400">
			<?php cw_home_e( 'cta_desc', 'cta.desc' ); ?>
		</p>

		<div class="flex flex-wrap gap-4 justify-center">
			<a href="<?php echo esc_url( cw_editor_link( 'cta', cw_url( '/contact' ) ) ); ?>" class="cta-primary text-base px-8 py-4">
				<span class="flex items-center gap-2">
					<?php cw_home_e( 'cta_button', 'cta.button' ); ?>
					<?php cw_icon( 'arrow-right', 18 ); ?>
				</span>
			</a>
			<a href="<?php echo esc_url( $cw_tel_href ); ?>" class="cta-secondary text-base px-8 py-4" dir="ltr">
				<span class="text-cw-red-light inline-flex"><?php cw_icon( 'phone', 16 ); ?></span>
				<span><?php echo esc_html( $cw_phone ); ?></span>
			</a>
		</div>
	</div>
</section>
