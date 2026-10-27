<?php
/**
 * Grille « à découvrir aussi »
 *
 * @package CarrierWeb
 * @var array  $args['items'] Entrées mappées (cw_get_solution/cw_get_sector).
 * @var string $args['label'] Sur-titre.
 * @var string $args['title'] Titre de section.
 * @var string $args['desc_key'] Clé de la description à afficher sur la carte.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_items    = $args['items'] ?? array();
$cw_label    = $args['label'] ?? '';
$cw_title    = $args['title'] ?? '';
$cw_desc_key = $args['desc_key'] ?? 'shortDescription';

if ( empty( $cw_items ) ) {
	return;
}
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
	<div class="text-center mb-10">
		<?php if ( $cw_label ) : ?>
			<div class="section-label"><?php echo esc_html( $cw_label ); ?></div>
		<?php endif; ?>
		<h2 class="section-title text-2xl md:text-3xl font-[family-name:var(--font-jakarta)]">
			<?php echo esc_html( $cw_title ); ?>
		</h2>
	</div>

	<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
		<?php foreach ( $cw_items as $cw_item ) : ?>
			<a
				href="<?php echo esc_url( $cw_item['permalink'] ); ?>"
				class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 hover:border-cw-red/30 transition-all group flex flex-col"
			>
				<div class="w-11 h-11 rounded-xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light mb-4 group-hover:bg-cw-red group-hover:text-white transition-all">
					<?php cw_icon( $cw_item['icon'], 24 ); ?>
				</div>
				<h3 class="text-base font-bold text-cw-fg mb-2 group-hover:text-cw-red-light transition-colors font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( $cw_item['title'] ); ?>
				</h3>
				<p class="text-cw-gray-400 text-xs leading-relaxed">
					<?php echo esc_html( (string) ( $cw_item[ $cw_desc_key ] ?? '' ) ); ?>
				</p>
			</a>
		<?php endforeach; ?>
	</div>
</section>
