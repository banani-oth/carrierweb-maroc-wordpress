<?php
/**
 * Page article — /ressources/{slug}
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_art    = cw_get_article( get_the_ID() );
$cw_others = cw_get_other_articles( (int) get_the_ID(), 2 );

$cw_human = cw_format_date( $cw_art['publishedDate'] );
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-[110px] pb-20">

	<section class="relative py-12 bg-gradient-to-b from-cw-red/10 to-cw-black overflow-hidden border-b border-white/[0.04]">
		<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
			<nav aria-label="<?php echo esc_attr( (string) cw_t( 'common.breadcrumbAria' ) ); ?>" class="flex items-center gap-1.5 text-xs text-cw-gray-500 mb-6 flex-wrap">
				<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.home' ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
				<a href="<?php echo esc_url( cw_url( '/ressources' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'resources.breadcrumb' ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
				<span class="text-cw-gray-300 line-clamp-1" aria-current="page"><?php echo esc_html( $cw_art['title'] ); ?></span>
			</nav>

			<div class="space-y-4">
				<div class="flex items-center gap-3 text-xs flex-wrap">
					<?php if ( '' !== $cw_art['category'] ) : ?>
						<span class="bg-cw-red/10 text-cw-red-light border border-cw-red/20 px-2.5 py-1 rounded-md font-semibold text-[10px]">
							<?php echo esc_html( $cw_art['category'] ); ?>
						</span>
					<?php endif; ?>
					<span class="text-cw-gray-500 inline-flex items-center gap-1.5">
						<?php cw_icon( 'clock', 12 ); ?>
						<?php echo esc_html( $cw_art['readingMinutes'] . ' ' . cw_t( 'articlePage.minRead' ) ); ?>
					</span>
					<time datetime="<?php echo esc_attr( $cw_art['publishedDate'] ); ?>" class="text-cw-gray-500">
						<?php echo esc_html( $cw_human ); ?>
					</time>
				</div>
				<h1 class="page-title"><?php echo esc_html( $cw_art['title'] ); ?></h1>
			</div>
		</div>
	</section>

	<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">

		<?php if ( '' !== $cw_art['intro'] ) : ?>
			<p class="text-cw-gray-300 text-base sm:text-lg leading-relaxed font-medium border-l-2 border-cw-red pl-5">
				<?php echo esc_html( $cw_art['intro'] ); ?>
			</p>
		<?php endif; ?>

		<div class="mt-12 space-y-12">
			<?php foreach ( $cw_art['sections'] as $cw_section ) : ?>
				<section>
					<h2 class="text-xl sm:text-2xl font-bold text-cw-fg tracking-tight font-[family-name:var(--font-jakarta)] mb-4">
						<?php echo esc_html( $cw_section['heading'] ); ?>
					</h2>

					<div class="space-y-4">
						<?php foreach ( $cw_section['paragraphs'] as $cw_p ) : ?>
							<p class="text-cw-gray-400 text-sm sm:text-[15px] leading-relaxed"><?php echo esc_html( $cw_p ); ?></p>
						<?php endforeach; ?>
					</div>

					<?php if ( ! empty( $cw_section['bullets'] ) ) : ?>
						<ul class="mt-5 space-y-3">
							<?php foreach ( $cw_section['bullets'] as $cw_b ) : ?>
								<li class="flex items-start gap-3 text-cw-gray-400 text-sm leading-relaxed">
									<span class="text-cw-red-light mt-0.5 flex-shrink-0 inline-flex"><?php cw_icon( 'check-circle-2', 16 ); ?></span>
									<span><?php echo esc_html( $cw_b ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</section>
			<?php endforeach; ?>
		</div>

		<?php if ( ! empty( $cw_art['keyTakeaways'] ) ) : ?>
			<div class="mt-14 bg-cw-surface border border-white/[0.06] rounded-2xl p-7 sm:p-9">
				<span class="section-label"><?php cw_e( 'articlePage.takeaways' ); ?></span>
				<ul class="space-y-3 mt-2">
					<?php foreach ( $cw_art['keyTakeaways'] as $cw_k ) : ?>
						<li class="flex items-start gap-3 text-cw-gray-300 text-sm leading-relaxed">
							<span class="text-cw-red-light mt-0.5 flex-shrink-0 inline-flex"><?php cw_icon( 'check-circle-2', 16 ); ?></span>
							<span><?php echo esc_html( $cw_k ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
			<?php if ( ! empty( $cw_art['relatedSolutions'] ) ) : ?>
				<div class="lg:col-span-7 bg-cw-surface border border-white/[0.06] rounded-2xl p-7">
					<span class="section-label"><?php cw_e( 'articlePage.relatedTitle' ); ?></span>
					<ul class="space-y-3 mt-2">
						<?php foreach ( $cw_art['relatedSolutions'] as $cw_rel ) : ?>
							<li>
								<a
									href="<?php echo esc_url( cw_url( $cw_rel['href'] ) ); ?>"
									class="inline-flex items-center gap-2 text-sm text-cw-gray-300 hover:text-cw-red-light transition-colors font-medium"
								>
									<?php cw_icon( 'arrow-right', 14 ); ?>
									<?php echo esc_html( $cw_rel['title'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<div class="lg:col-span-5 bg-gradient-to-br from-cw-red to-cw-red-dark rounded-2xl p-7 flex flex-col justify-center text-center shadow-[var(--shadow-glow-red)]">
				<h2 class="text-xl font-extrabold text-white font-[family-name:var(--font-jakarta)] tracking-tight">
					<?php cw_e( 'articlePage.ctaTitle' ); ?>
				</h2>
				<p class="text-white/80 text-xs leading-relaxed mt-2 mb-5"><?php cw_e( 'articlePage.ctaDesc' ); ?></p>
				<a
					href="<?php echo esc_url( cw_editor_link( 'demo', cw_url( '/contact' ) ) ); ?>"
					class="inline-flex items-center justify-center gap-2 bg-white text-cw-red font-bold text-sm rounded-xl px-6 py-3 hover:bg-cw-gray-100 transition-colors"
				>
					<?php cw_e( 'articlePage.ctaButton' ); ?>
					<?php cw_icon( 'arrow-right', 16 ); ?>
				</a>
			</div>
		</div>

		<?php if ( ! empty( $cw_others ) ) : ?>
			<div class="mt-16 pt-10 border-t border-white/[0.06]">
				<h2 class="text-lg font-bold text-cw-fg font-[family-name:var(--font-jakarta)] mb-6">
					<?php cw_e( 'articlePage.continueReading' ); ?>
				</h2>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
					<?php foreach ( $cw_others as $cw_other ) : ?>
						<a
							href="<?php echo esc_url( $cw_other['permalink'] ); ?>"
							class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 hover:border-cw-red/30 transition-all group block"
						>
							<?php if ( '' !== $cw_other['category'] ) : ?>
								<span class="bg-cw-red/10 text-cw-red-light border border-cw-red/20 px-2.5 py-1 rounded-md font-semibold text-[10px]">
									<?php echo esc_html( $cw_other['category'] ); ?>
								</span>
							<?php endif; ?>
							<h3 class="text-sm font-bold text-cw-fg group-hover:text-cw-red-light transition-colors leading-snug font-[family-name:var(--font-jakarta)] mt-3">
								<?php echo esc_html( $cw_other['title'] ); ?>
							</h3>
							<p class="text-cw-gray-500 text-[11px] leading-relaxed mt-2 line-clamp-2">
								<?php echo esc_html( $cw_other['excerpt'] ); ?>
							</p>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</article>
</main>

<?php
get_footer();
