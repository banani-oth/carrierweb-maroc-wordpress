<?php
/**
 * Sélecteur de langue FR / AR / EN
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_current = cw_locale();
$cw_bare    = cw_current_path();
$cw_labels  = cw_locale_labels();
$cw_first   = true;

/*
 * URL de chaque langue pour la page COURANTE.
 *
 * Avec Polylang, les traductions ont leur propre slug (suivi-gps-ar, -en) :
 * on ne peut donc plus déduire l'URL en préfixant le chemin. On demande à
 * Polylang les liens réels — il connaît la traduction de chaque contenu.
 * Sans Polylang, on retombe sur le préfixage de chemin.
 */
$cw_lang_urls = array();

if ( function_exists( 'pll_the_languages' ) ) {
	$cw_raw = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_no_translation' => 0,
			'display_names_as'       => 'slug',
		)
	);

	foreach ( (array) $cw_raw as $cw_entry ) {
		if ( ! empty( $cw_entry['slug'] ) && ! empty( $cw_entry['url'] ) ) {
			$cw_lang_urls[ $cw_entry['slug'] ] = $cw_entry['url'];
		}
	}
}
?>
<div
	class="flex items-center gap-1 text-xs"
	role="group"
	aria-label="<?php echo esc_attr( (string) cw_t( 'header.langSwitchAria' ) ); ?>"
>
	<?php foreach ( CW_LOCALES as $cw_l ) : ?>
		<span class="flex items-center">
			<?php if ( ! $cw_first ) : ?>
				<span class="text-cw-gray-700 mx-1" aria-hidden="true">|</span>
			<?php endif; ?>

			<?php if ( $cw_l === $cw_current ) : ?>
				<span class="text-cw-fg font-semibold" aria-current="true"><?php echo esc_html( $cw_labels[ $cw_l ] ); ?></span>
			<?php else : ?>
				<a
					href="<?php echo esc_url( $cw_lang_urls[ $cw_l ] ?? cw_url( $cw_bare, $cw_l ) ); ?>"
					hreflang="<?php echo esc_attr( $cw_l ); ?>"
					class="text-cw-gray-500 hover:text-cw-fg transition-colors"
				><?php echo esc_html( $cw_labels[ $cw_l ] ); ?></a>
			<?php endif; ?>
		</span>
		<?php $cw_first = false; ?>
	<?php endforeach; ?>
</div>
