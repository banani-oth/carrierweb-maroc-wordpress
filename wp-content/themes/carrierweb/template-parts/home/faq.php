<?php
/**
 * FAQ de l'accueil
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_faqs = cw_home_list( 'faq', array( 'q', 'a' ), 4, 'homeFaq.items' );
?>
<section id="faq" class="relative py-24 lg:py-32 bg-cw-black overflow-hidden">
	<div class="relative max-w-4xl mx-auto px-4 sm:px-6">

		<div class="text-center mb-16">
			<div class="section-label"><?php cw_home_e( 'faq_label', 'homeFaq.label' ); ?></div>
			<h2 class="section-title text-3xl md:text-4xl lg:text-5xl mb-4 font-[family-name:var(--font-jakarta)]">
				<?php echo esc_html( cw_home_text( 'faq_title_pre', 'homeFaq.titlePre' ) ); ?><span class="gradient-text"><?php echo esc_html( cw_home_text( 'faq_title_hl', 'homeFaq.titleHl' ) ); ?></span>
			</h2>
			<p class="section-desc mx-auto text-center"><?php cw_home_e( 'faq_desc', 'homeFaq.desc' ); ?></p>
		</div>

		<?php get_template_part( 'template-parts/blocks/faq-accordion', null, array( 'items' => $cw_faqs ) ); ?>

	</div>
</section>
