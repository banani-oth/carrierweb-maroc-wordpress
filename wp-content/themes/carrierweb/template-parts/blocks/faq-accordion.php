<?php
/**
 * Accordéon FAQ réutilisable
 *
 * @package CarrierWeb
 * @var array<int, array{q:string, a:string}> $args['items']
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_faq_items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();

if ( empty( $cw_faq_items ) ) {
	return;
}

// Identifiant unique : une page peut contenir plusieurs accordéons.
static $cw_faq_instance = 0;
++$cw_faq_instance;
?>
<div class="space-y-4" data-cw-faq>
	<?php foreach ( $cw_faq_items as $cw_i => $cw_faq ) : ?>
		<?php
		$cw_panel_id  = 'cw-faq-' . $cw_faq_instance . '-panel-' . $cw_i;
		$cw_button_id = 'cw-faq-' . $cw_faq_instance . '-button-' . $cw_i;
		?>
		<div class="faq-item border border-white/[0.04] bg-cw-surface transition-all">
			<h3 class="m-0">
				<button
					type="button"
					id="<?php echo esc_attr( $cw_button_id ); ?>"
					class="w-full flex items-center justify-between px-6 py-5 text-left transition-colors"
					aria-expanded="true"
					aria-controls="<?php echo esc_attr( $cw_panel_id ); ?>"
					data-cw-faq-toggle
				>
					<span class="text-sm sm:text-base font-semibold text-cw-fg pr-4 font-[family-name:var(--font-jakarta)] flex items-center gap-3">
						<span class="text-cw-red-light flex-shrink-0 inline-flex"><?php cw_icon( 'help-circle', 18 ); ?></span>
						<?php echo esc_html( (string) ( $cw_faq['q'] ?? '' ) ); ?>
					</span>
					<span class="text-cw-red-light flex-shrink-0 transition-transform duration-300 inline-flex" data-cw-faq-chevron>
						<?php cw_icon( 'chevron-down', 18 ); ?>
					</span>
				</button>
			</h3>

			<div
				id="<?php echo esc_attr( $cw_panel_id ); ?>"
				role="region"
				aria-labelledby="<?php echo esc_attr( $cw_button_id ); ?>"
				class="border-t border-white/[0.04]"
				data-cw-faq-panel
			>
				<p class="text-cw-gray-400 text-xs sm:text-sm leading-relaxed py-5 px-6">
					<?php echo esc_html( (string) ( $cw_faq['a'] ?? '' ) ); ?>
				</p>
			</div>
		</div>
	<?php endforeach; ?>
</div>
