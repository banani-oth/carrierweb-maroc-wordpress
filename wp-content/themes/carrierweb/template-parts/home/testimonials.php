<?php
/**
 * Carrousel de témoignages
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_items = cw_get_testimonials();



if ( empty( $cw_items ) ) {
	return;
}
?>
<section id="testimonials" class="relative py-24 lg:py-32 bg-cw-black overflow-hidden">
	<div class="relative max-w-4xl mx-auto px-4 sm:px-6">

		<div class="text-center mb-12">
			<div class="section-label"><?php cw_e( 'testimonials.label' ); ?></div>
			<h2 class="section-title text-3xl md:text-4xl lg:text-5xl font-[family-name:var(--font-jakarta)]">
				<?php cw_e( 'testimonials.titlePre' ); ?><span class="gradient-text"><?php cw_e( 'testimonials.titleHl' ); ?></span>
			</h2>
			<p class="text-cw-gray-500 text-xs mt-4"><?php cw_e( 'testimonials.note' ); ?></p>
		</div>

		<div class="relative bg-cw-surface border border-white/[0.04] rounded-3xl p-8 sm:p-12 shadow-2xl overflow-hidden" data-cw-carousel>
			<div class="absolute top-6 right-8 text-cw-red-light/[0.06] pointer-events-none">
				<?php cw_icon( 'quote', 120 ); ?>
			</div>

			<?php // aria-live : annonce le changement de témoignage aux lecteurs d'écran. ?>
			<div class="min-h-[160px] flex flex-col justify-between relative z-10" aria-live="polite">
				<?php foreach ( $cw_items as $cw_i => $cw_item ) : ?>
					<div class="cw-slide<?php echo 0 === $cw_i ? ' is-active' : ''; ?>" data-cw-slide>
						<p class="text-cw-gray-300 text-base sm:text-lg font-medium leading-relaxed italic mb-8">
							«&nbsp;<?php echo esc_html( (string) ( $cw_item['quote'] ?? '' ) ); ?>&nbsp;»
						</p>

						<div>
							<div class="text-cw-fg font-bold text-base font-[family-name:var(--font-jakarta)]">
								<?php echo esc_html( (string) ( $cw_item['author'] ?? '' ) ); ?>
							</div>
							<div class="text-cw-red-light text-xs font-medium mt-0.5">
								<?php echo esc_html( (string) ( $cw_item['role'] ?? '' ) ); ?>
								<?php if ( ! empty( $cw_item['company'] ) ) : ?>
									— <span class="text-cw-gray-500"><?php echo esc_html( (string) $cw_item['company'] ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>

				<?php // Les commandes n'ont de sens qu'avec le JS : masquées par défaut. ?>
				<div class="flex gap-2 justify-end mt-4 hidden" data-cw-carousel-controls>
					<button
						type="button"
						class="w-10 h-10 rounded-xl bg-cw-black border border-white/[0.04] hover:border-cw-red hover:text-cw-red-light flex items-center justify-center text-cw-fg transition-all"
						aria-label="<?php echo esc_attr( (string) cw_t( 'testimonials.prevAria' ) ); ?>"
						data-cw-carousel-prev
					>
						<?php cw_icon( 'chevron-left', 18 ); ?>
					</button>
					<button
						type="button"
						class="w-10 h-10 rounded-xl bg-cw-black border border-white/[0.04] hover:border-cw-red hover:text-cw-red-light flex items-center justify-center text-cw-fg transition-all"
						aria-label="<?php echo esc_attr( (string) cw_t( 'testimonials.nextAria' ) ); ?>"
						data-cw-carousel-next
					>
						<?php cw_icon( 'chevron-right', 18 ); ?>
					</button>
				</div>
			</div>
		</div>

		<div class="flex justify-center gap-2 mt-6 hidden" data-cw-carousel-dots>
			<?php foreach ( $cw_items as $cw_i => $cw_item ) : ?>
				<button
					type="button"
					class="h-1.5 rounded-full transition-all duration-300 <?php echo 0 === $cw_i ? 'bg-cw-red w-6' : 'bg-cw-slate-700 w-2'; ?>"
					aria-label="<?php echo esc_attr( cw_t( 'testimonials.goToAria' ) . ' ' . ( $cw_i + 1 ) ); ?>"
					data-cw-carousel-dot
				></button>
			<?php endforeach; ?>
		</div>

	</div>
</section>
