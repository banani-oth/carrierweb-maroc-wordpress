<?php
/**
 * Bandeau de compatibilité constructeurs
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_brands = cw_editor_partners();
$cw_line   = cw_home_text( 'partners_line', 'partners.line' );
?>
<section
	id="compatibilite"
	class="relative py-12 bg-cw-black border-y border-white/5"
	aria-label="<?php echo esc_attr( $cw_line ); ?>"
>
	<div class="max-w-7xl mx-auto px-4 sm:px-6">
		<p class="text-center text-xs font-semibold uppercase tracking-[0.16em] text-cw-gray-500 mb-8 font-[family-name:var(--font-jakarta)]">
			<?php echo esc_html( $cw_line ); ?>
		</p>
		<div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 lg:gap-16">
			<?php foreach ( $cw_brands as $cw_brand ) : ?>
				<?php // dir="ltr" : les noms de marque restent en latin, même en arabe. ?>
				<div
					class="partner-logo text-2xl sm:text-3xl font-black text-cw-gray-600 tracking-wider hover:text-cw-gray-300 transition-all cursor-default select-none"
					dir="ltr"
				>
					<?php if ( ! empty( $cw_brand['image'] ) ) : ?>
                        <img src="<?php echo esc_url( $cw_brand['image'] ); ?>" alt="<?php echo esc_attr( $cw_brand['name'] ); ?>" loading="lazy" class="cw-partner-image">
                    <?php else : ?>
                        <?php echo esc_html( $cw_brand['name'] ); ?>
                    <?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
