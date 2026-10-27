<?php
/**
 * Carte secteur du listing /secteurs.
 *
 * @package CarrierWeb
 *
 * @var array $args['sector'] Forme renvoyée par cw_get_sector().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_sec = $args['sector'] ?? array();

if ( empty( $cw_sec ) ) {
	return;
}
?>
<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 flex flex-col justify-between hover:border-cw-red/30 transition-all group">
	<div>
		<div class="w-12 h-12 rounded-xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light mb-5 group-hover:bg-cw-red group-hover:text-white transition-all">
			<?php cw_icon( $cw_sec['icon'], 28 ); ?>
		</div>
		<h3 class="text-xl font-bold text-cw-fg mb-3 group-hover:text-cw-red-light transition-colors font-[family-name:var(--font-jakarta)]">
			<?php echo esc_html( $cw_sec['title'] ); ?>
		</h3>
		<p class="text-cw-gray-400 text-xs leading-relaxed mb-6">
			<?php echo esc_html( $cw_sec['listingDescription'] ); ?>
		</p>
	</div>
	<a
		href="<?php echo esc_url( $cw_sec['permalink'] ); ?>"
		class="inline-flex items-center gap-1.5 text-xs font-semibold text-cw-red-light hover:gap-2.5 transition-all w-fit"
	>
		<span><?php cw_e( 'listing.sectors.seeMore' ); ?></span>
		<?php cw_icon( 'arrow-right', 14 ); ?>
	</a>
</div>
