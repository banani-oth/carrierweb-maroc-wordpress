<?php
/**
 * Gabarit partagé des pages juridiques.
 *
 * @package CarrierWeb
 *
 * @var string $args['key']   Clé dans inc/legal-data.php.
 * @var string $args['title'] Titre affiché.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_key   = $args['key'] ?? '';
$cw_title = $args['title'] ?? '';

$cw_page = get_post();
$cw_custom_content = $cw_page && (
	'' !== trim( (string) $cw_page->post_content )
	|| '1' === (string) get_post_meta( $cw_page->ID, '_cw_legal_content_saved', true )
);

if ( $cw_custom_content ) {
	$cw_title = get_the_title( $cw_page );
}

$cw_french_default = ! $cw_custom_content || (
	$cw_page && 'fr' === (string) get_post_meta( $cw_page->ID, '_cw_legal_seed_language', true )
	&& hash_equals( (string) get_post_meta( $cw_page->ID, '_cw_legal_seed_hash', true ), hash( 'sha256', (string) $cw_page->post_content ) )
);

$cw_legal = require CW_DIR . '/inc/legal-data.php';
$cw_data  = $cw_legal[ $cw_key ] ?? array();

$cw_sections = $cw_data['sections'] ?? array();
$cw_updated  = $cw_custom_content ? '' : ( $cw_data['lastUpdated'] ?? '' );
?>
<main id="main-content" class="bg-cw-black min-h-screen pt-[110px] pb-20">

	<section class="relative py-12 bg-gradient-to-b from-cw-red/10 to-cw-black overflow-hidden border-b border-white/[0.04]">
		<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
			<nav class="flex items-center gap-1.5 text-xs text-cw-gray-500 mb-6" aria-label="<?php echo esc_attr( (string) cw_t( 'common.breadcrumbAria' ) ); ?>">
				<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.home' ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
				<span class="text-cw-gray-300" aria-current="page"><?php echo esc_html( $cw_title ); ?></span>
			</nav>

			<h1 class="page-title"><?php echo esc_html( $cw_title ); ?></h1>

			<?php if ( '' !== $cw_updated ) : ?>
				<p class="text-cw-gray-500 text-xs mt-4">
					<?php echo esc_html( sprintf( /* translators: %s: date */ __( 'Dernière mise à jour : %s', 'carrierweb' ), $cw_updated ) ); ?>
				</p>
			<?php endif; ?>

			<?php
			// Le texte de départ est français ; une page enregistrée fournit son propre contenu.
			?>
			<?php if ( $cw_french_default && 'fr' !== cw_locale() ) : ?>
				<p class="mt-4 bg-cw-red/10 border border-cw-red/20 text-cw-gray-300 text-xs rounded-xl px-4 py-3">
					<?php cw_e( 'common.legalFrenchOnly' ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 space-y-10">
		<?php if ( $cw_custom_content ) : ?>
			<div class="cw-legal-content text-cw-gray-400 text-sm leading-relaxed">
				<?php echo apply_filters( 'the_content', $cw_page->post_content ); // WordPress renders saved blocks, links and paragraphs. ?>
			</div>
		<?php else : ?>
		<?php foreach ( $cw_sections as $cw_section ) : ?>
			<section>
				<h2 class="text-lg font-bold text-cw-fg border-l-3 border-cw-red pl-3 font-[family-name:var(--font-jakarta)] mb-3">
					<?php echo esc_html( $cw_section['title'] ); ?>
				</h2>
				<p class="text-cw-gray-400 text-sm leading-relaxed"><?php echo esc_html( $cw_section['body'] ); ?></p>
			</section>
		<?php endforeach; ?>
		<?php endif; ?>
	</article>

</main>
