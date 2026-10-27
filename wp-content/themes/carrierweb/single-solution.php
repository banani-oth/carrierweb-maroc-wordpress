<?php
/**
 * Page solution — /solutions/{slug}
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_sol     = cw_get_solution( get_the_ID() );
$cw_related = cw_get_related( 'solution', (int) get_the_ID(), 3 );
$cw_phone   = cw_setting( 'contactPhone', '', '+212 (0)5 22 36 19 88' );
$cw_tel     = 'tel:' . preg_replace( '/[^\d+]/', '', $cw_phone );
?>

<main id="main-content" class="bg-cw-black">

	<section class="relative overflow-hidden pt-36 pb-16 lg:pt-44 lg:pb-24">
		<div class="absolute inset-0 bg-radial-glow pointer-events-none"></div>
		<div class="absolute top-0 right-0 w-[520px] h-[520px] bg-cw-red/[0.06] rounded-full blur-[130px] pointer-events-none" aria-hidden="true"></div>

		<div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<nav aria-label="<?php echo esc_attr( (string) cw_t( 'common.breadcrumbAria' ) ); ?>" class="flex items-center gap-1.5 text-xs text-cw-gray-500 mb-10">
				<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.home' ); ?></a>
				<?php cw_icon( 'chevron-right', 12 ); ?>
				<a href="<?php echo esc_url( cw_url( '/solutions' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.solutionsLabel' ); ?></a>
				<?php cw_icon( 'chevron-right', 12 ); ?>
				<span class="text-cw-gray-300" aria-current="page"><?php echo esc_html( $cw_sol['title'] ); ?></span>
			</nav>

			<div class="grid lg:grid-cols-12 gap-10 lg:gap-16 items-center">
				<div class="lg:col-span-7">
					<span class="section-label"><?php cw_e( 'solutionDetail.badge' ); ?></span>
					<h1 class="page-title"><?php echo esc_html( $cw_sol['title'] ); ?></h1>
					<p class="page-desc max-w-xl mt-6"><?php echo esc_html( $cw_sol['tagline'] ); ?></p>

					<div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-9">
						<a href="<?php echo esc_url( cw_editor_link( 'demo', cw_url( '/contact' ) ) ); ?>" class="cta-primary justify-center text-sm px-7 py-4">
							<?php cw_e( 'solutionDetail.ctaDemo' ); ?>
							<?php cw_icon( 'arrow-right', 16 ); ?>
						</a>
						<a href="<?php echo esc_url( $cw_tel ); ?>" class="cta-secondary justify-center text-sm px-7 py-4" dir="ltr">
							<span class="text-cw-red-light inline-flex"><?php cw_icon( 'phone-call', 15 ); ?></span>
							<?php cw_e( 'common.talkToExpert' ); ?>
						</a>
					</div>
				</div>

				<div class="lg:col-span-5 hidden lg:flex justify-center">
					<div class="relative">
						<div class="absolute inset-0 bg-cw-red/20 blur-[60px] rounded-full" aria-hidden="true"></div>
						<div class="relative w-44 h-44 xl:w-52 xl:h-52 rounded-[2rem] bg-gradient-to-br from-cw-surface-2 to-cw-surface border border-cw-red/20 flex items-center justify-center text-cw-red-light shadow-2xl">
							<?php cw_icon( $cw_sol['icon'], 88 ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $cw_sol['stats'] ) ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2">
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
				<?php foreach ( $cw_sol['stats'] as $cw_stat ) : ?>
					<div class="bg-cw-surface border border-white/[0.06] rounded-2xl p-6 hover:border-cw-red/30 transition-colors group">
						<div class="flex items-center gap-2 text-cw-red-light mb-3">
							<?php cw_icon( $cw_stat['icon'], 18 ); ?>
						</div>
						<div class="text-[2.5rem] leading-none font-extrabold text-cw-fg tracking-tight font-[family-name:var(--font-jakarta)]">
							<?php echo esc_html( $cw_stat['value'] ); ?>
						</div>
						<div class="text-[11px] text-cw-gray-400 uppercase tracking-[0.12em] mt-2">
							<?php echo esc_html( $cw_stat['label'] ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! empty( $cw_sol['features'] ) ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24 lg:mt-32">
			<span class="section-label"><?php cw_e( 'solutionDetail.featuresLabel' ); ?></span>
			<h2 class="text-[clamp(1.75rem,4vw,3rem)] font-extrabold text-cw-fg tracking-[-0.03em] font-[family-name:var(--font-jakarta)] mb-12 max-w-2xl">
				<?php cw_e( 'solutionDetail.featuresTitle' ); ?>
			</h2>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
				<?php foreach ( $cw_sol['features'] as $cw_i => $cw_feature ) : ?>
					<div class="service-card">
						<div class="flex items-start justify-between mb-6">
							<div class="w-12 h-12 rounded-xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light">
								<?php cw_icon( $cw_feature['icon'], 20 ); ?>
							</div>
							<span aria-hidden="true" class="text-4xl font-extrabold leading-none text-cw-fg/[0.05] font-[family-name:var(--font-jakarta)] select-none">
								<?php echo esc_html( str_pad( (string) ( $cw_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>
							</span>
						</div>
						<h3 class="text-cw-fg font-bold text-base mb-2 font-[family-name:var(--font-jakarta)]">
							<?php echo esc_html( $cw_feature['title'] ); ?>
						</h3>
						<p class="text-cw-gray-400 text-xs leading-relaxed"><?php echo esc_html( $cw_feature['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! empty( $cw_sol['specs'] ) || '' !== $cw_sol['testimonial']['quote'] ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24 lg:mt-32 grid grid-cols-1 lg:grid-cols-2 gap-6">

			<?php if ( ! empty( $cw_sol['specs'] ) ) : ?>
				<div class="bg-cw-surface border border-white/[0.06] rounded-2xl overflow-hidden">
					<div class="px-7 py-5 border-b border-white/[0.06]">
						<span class="section-label !mb-0"><?php cw_e( 'solutionDetail.specsLabel' ); ?></span>
					</div>
					<dl class="divide-y divide-white/[0.06]">
						<?php foreach ( $cw_sol['specs'] as $cw_spec ) : ?>
							<div class="flex items-center justify-between px-7 py-4 hover:bg-white/[0.02] transition-colors">
								<dt class="text-sm text-cw-gray-400"><?php echo esc_html( $cw_spec['label'] ); ?></dt>
								<dd class="text-sm text-cw-fg font-semibold text-right"><?php echo esc_html( $cw_spec['value'] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $cw_sol['testimonial']['quote'] ) : ?>
				<div class="relative bg-gradient-to-br from-cw-surface-2 to-cw-surface border border-white/[0.06] rounded-2xl p-7 sm:p-9 flex flex-col justify-between overflow-hidden">
					<span class="absolute -top-4 -right-4 text-cw-red-light/[0.05] pointer-events-none"><?php cw_icon( 'quote', 130 ); ?></span>
					<div class="relative">
						<span class="section-label"><?php cw_e( 'solutionDetail.testimonialLabel' ); ?></span>
						<blockquote class="text-cw-fg text-lg sm:text-xl leading-relaxed font-medium">
							«&nbsp;<?php echo esc_html( $cw_sol['testimonial']['quote'] ); ?>&nbsp;»
						</blockquote>
					</div>
					<div class="relative flex items-center gap-4 pt-8 mt-8 border-t border-white/[0.06]">
						<div class="w-12 h-12 rounded-full bg-cw-red/15 border border-cw-red/30 flex items-center justify-center text-cw-red-light font-bold text-base font-[family-name:var(--font-jakarta)]">
							<?php // Initiale de l'auteur — mb_substr, sinon un é serait coupé en deux octets. ?>
							<?php echo esc_html( mb_substr( $cw_sol['testimonial']['author'], 0, 1 ) ); ?>
						</div>
						<div>
							<div class="text-cw-fg font-semibold text-sm font-[family-name:var(--font-jakarta)]">
								<?php echo esc_html( $cw_sol['testimonial']['author'] ); ?>
							</div>
							<div class="text-cw-gray-500 text-xs"><?php echo esc_html( $cw_sol['testimonial']['company'] ); ?></div>
						</div>
					</div>
				</div>
			<?php endif; ?>

		</section>
	<?php endif; ?>

	<?php if ( ! empty( $cw_sol['faq'] ) ) : ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24 lg:mt-32">
			<span class="section-label"><?php cw_e( 'solutionDetail.faqLabel' ); ?></span>
			<h2 class="text-[clamp(1.75rem,4vw,3rem)] font-extrabold text-cw-fg tracking-[-0.03em] font-[family-name:var(--font-jakarta)] mb-12 max-w-2xl">
				<?php cw_e( 'solutionDetail.faqTitle' ); ?>
			</h2>
			<?php
			// L'accordéon attend des clés q/a ; les champs ACF sont question/answer.
			$cw_faq_items = array_map(
				static function ( $item ) {
					return array(
						'q' => $item['question'],
						'a' => $item['answer'],
					);
				},
				$cw_sol['faq']
			);

			get_template_part( 'template-parts/blocks/faq-accordion', null, array( 'items' => $cw_faq_items ) );
			?>
		</section>
	<?php endif; ?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24 lg:mt-32 mb-24 grid grid-cols-1 lg:grid-cols-12 gap-6">
		<?php if ( '' !== $cw_sol['seoRichContent'] ) : ?>
			<div class="lg:col-span-8 bg-cw-surface border border-white/[0.06] rounded-2xl p-7 sm:p-9">
				<span class="section-label"><?php cw_e( 'solutionDetail.seoLabel' ); ?></span>
				<p class="text-cw-gray-300 text-sm leading-relaxed"><?php echo esc_html( $cw_sol['seoRichContent'] ); ?></p>
			</div>
		<?php endif; ?>

		<div class="lg:col-span-4 bg-gradient-to-br from-cw-red to-cw-red-dark rounded-2xl p-8 flex flex-col justify-center text-center shadow-[var(--shadow-glow-red)]">
			<h2 class="text-2xl font-extrabold text-white font-[family-name:var(--font-jakarta)] tracking-tight">
				<?php cw_e( 'solutionDetail.ctaBoxTitle' ); ?>
			</h2>
			<p class="text-white/80 text-sm leading-relaxed mt-3 mb-6"><?php cw_e( 'solutionDetail.ctaBoxDesc' ); ?></p>
			<a
				href="<?php echo esc_url( cw_editor_link( 'demo', cw_url( '/contact' ) ) ); ?>"
				class="inline-flex items-center justify-center gap-2 bg-white text-cw-red font-bold text-sm rounded-xl px-6 py-3.5 hover:bg-cw-gray-100 transition-colors"
			>
				<?php cw_e( 'solutionDetail.ctaDemo' ); ?>
				<?php cw_icon( 'arrow-right', 16 ); ?>
			</a>
		</div>
	</section>

	<?php
	get_template_part(
		'template-parts/blocks/related-grid',
		null,
		array(
			'items'    => $cw_related,
			'label'    => (string) cw_t( 'solutionDetail.relatedLabel' ),
			'title'    => (string) cw_t( 'solutionDetail.relatedTitle' ),
			'desc_key' => 'shortDescription',
		)
	);
	?>

</main>

<?php
get_footer();
