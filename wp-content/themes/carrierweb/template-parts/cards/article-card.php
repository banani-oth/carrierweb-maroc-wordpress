<?php
/**
 * Carte article — listing /ressources et grille « Poursuivre la lecture ».
 *
 * @package CarrierWeb
 *
 * @var array $args['article'] Forme renvoyée par cw_get_article().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_art = $args['article'] ?? array();

if ( empty( $cw_art ) ) {
	return;
}

// Date localisée via Intl — voir cw_format_date() dans inc/i18n.php.
$cw_human = cw_format_date( $cw_art['publishedDate'] );
?>
<a
	href="<?php echo esc_url( $cw_art['permalink'] ); ?>"
	class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 flex flex-col justify-between hover:border-cw-red/30 transition-all group"
>
	<div class="space-y-4">
		<div class="flex justify-between items-center text-xs">
			<?php if ( '' !== $cw_art['category'] ) : ?>
				<span class="bg-cw-red/10 text-cw-red-light border border-cw-red/20 px-2.5 py-1 rounded-md font-semibold text-[10px]">
					<?php echo esc_html( $cw_art['category'] ); ?>
				</span>
			<?php endif; ?>
			<span class="text-cw-gray-500 text-[10px] inline-flex items-center gap-1">
				<?php cw_icon( 'clock', 10 ); ?>
				<?php echo esc_html( $cw_art['readingMinutes'] . ' ' . cw_t( 'resources.minRead' ) ); ?>
			</span>
		</div>

		<h3 class="text-base font-bold text-cw-fg group-hover:text-cw-red-light transition-colors leading-snug font-[family-name:var(--font-jakarta)]">
			<?php echo esc_html( $cw_art['title'] ); ?>
		</h3>

		<p class="text-cw-gray-400 text-[11px] leading-relaxed">
			<?php echo esc_html( $cw_art['excerpt'] ); ?>
		</p>
	</div>

	<div class="pt-6 mt-6 border-t border-white/[0.04] flex items-center justify-between">
		<time datetime="<?php echo esc_attr( $cw_art['publishedDate'] ); ?>" class="text-[10px] text-cw-gray-500">
			<?php echo esc_html( $cw_human ); ?>
		</time>
		<span class="text-cw-red-light text-[11px] font-semibold inline-flex items-center gap-1 group-hover:gap-2 transition-all">
			<?php cw_e( 'resources.read' ); ?>
			<?php cw_icon( 'chevron-right', 12 ); ?>
		</span>
	</div>
</a>
