<?php
/**
 * Bandeau de réassurance
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_items = (array) cw_t( 'trustBar', array() );
$cw_icons = array( 'shield-check', 'wifi', 'phone-call', 'clock' );

if ( empty( $cw_items ) ) {
	return;
}
?>
<section
	class="relative z-30 py-6 bg-cw-black-deep/80 backdrop-blur-md border-y border-white/[0.04]"
	aria-label="<?php echo esc_attr( (string) ( $cw_items[0]['label'] ?? '' ) ); ?>"
>
	<div class="max-w-7xl mx-auto px-4 sm:px-6">
		<div class="grid grid-cols-2 md:grid-cols-4 gap-6 items-center">
			<?php foreach ( $cw_items as $cw_i => $cw_item ) : ?>
				<div class="flex items-center gap-3">
					<div class="w-9 h-9 rounded-lg bg-cw-red/10 flex items-center justify-center flex-shrink-0">
						<span class="text-cw-red-light inline-flex"><?php cw_icon( $cw_icons[ $cw_i ] ?? 'shield-check', 18 ); ?></span>
					</div>
					<div>
						<div class="text-xs sm:text-sm font-semibold text-cw-fg tracking-tight font-[family-name:var(--font-jakarta)]">
							<?php echo esc_html( (string) ( $cw_item['label'] ?? '' ) ); ?>
						</div>
						<div class="text-[10px] sm:text-xs text-cw-gray-500">
							<?php echo esc_html( (string) ( $cw_item['desc'] ?? '' ) ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
