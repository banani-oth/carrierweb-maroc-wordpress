<?php
/**
 * Listing des ressources — /ressources
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_articles = cw_get_articles();
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-[110px] pb-20">

	<?php
	get_template_part(
		'template-parts/blocks/breadcrumb-hero',
		null,
		array(
			'breadcrumb' => cw_page_text( 'page_ressources', 'breadcrumb', 'resources.breadcrumb' ),
			'label'      => cw_page_text( 'page_ressources', 'label', 'resources.label' ),
			'title_pre'  => cw_page_text( 'page_ressources', 'h1_pre', 'resources.h1Pre' ),
			'title_hl'   => cw_page_text( 'page_ressources', 'h1_hl', 'resources.h1Hl' ),
			'desc'       => cw_page_text( 'page_ressources', 'desc', 'resources.desc' ),
		)
	);
	?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-8">

		<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
			<?php foreach ( $cw_articles as $cw_art ) : ?>
				<?php get_template_part( 'template-parts/cards/article-card', null, array( 'article' => $cw_art ) ); ?>
			<?php endforeach; ?>
		</div>

		<?php
		/*
		 * Newsletter : un simple lien mailto, comme côté Next. Pas de formulaire
		 * ni de service tiers — c'est un choix produit, pas une limite technique.
		 */
		$cw_news_email = cw_setting( 'contactEmail', '', 'info@carrierweb.ma' );
		?>
		<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 sm:p-8 text-center max-w-2xl mx-auto space-y-5 mt-16">
			<div class="w-12 h-12 rounded-xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light mx-auto">
				<?php cw_icon( 'book-open', 24 ); ?>
			</div>
			<div class="space-y-2">
				<h2 class="text-lg font-bold text-cw-fg font-[family-name:var(--font-jakarta)]">
					<?php echo esc_html( cw_page_text( 'page_ressources', 'newsletter_title', 'resources.newsletterTitle' ) ); ?>
				</h2>
				<p class="text-cw-gray-400 text-xs max-w-md mx-auto leading-relaxed">
					<?php echo esc_html( cw_page_text( 'page_ressources', 'newsletter_desc', 'resources.newsletterDesc' ) ); ?>
				</p>
			</div>
			<div class="pt-2">
				<a
					href="<?php echo esc_url( cw_editor_link( 'newsletter', 'mailto:' . $cw_news_email . '?subject=' . rawurlencode( 'Newsletter CarrierWeb Maroc' ) ) ); ?>"
					class="cta-primary text-xs py-2.5 px-6 justify-center inline-flex"
				>
					<?php echo esc_html( cw_page_text( 'page_ressources', 'newsletter_cta', 'resources.newsletterCta' ) ); ?>
				</a>
			</div>
		</div>

	</section>
</main>

<?php
get_footer();
