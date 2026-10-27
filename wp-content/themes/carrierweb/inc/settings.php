<?php
/**
 * Réglages communs et textes du hero.
 *
 * Les valeurs du contenu reglage remplacent les valeurs du dictionnaire.
 * Une traduction absente conserve le texte de sa propre langue.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Champs du singleton `reglage`.
 *
 * Lecture via get_post_meta() plutôt que get_field() : ACF stocke ces valeurs
 * en scalaires simples, donc le détour par ACF n'apporte rien au rendu — et le
 * front continue de fonctionner même si le plugin est désactivé. ACF ne reste
 * nécessaire que pour l'interface d'édition.
 *
 * @return array<string, string> Clés vides absentes ; jamais de valeur nulle.
 */
function cw_site_settings(): array {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	/*
	 * Depuis l'ajout de Polylang, WordPress peut porter les trois langues :
	 * cw_get_singleton_id() renvoie le post de la LANGUE COURANTE. Si l'équipe
	 * n'a pas encore traduit les réglages, il n'y a pas de post et l'on
	 * retombe naturellement sur le dictionnaire — aucune page ne se vide.
	 */
	$id = function_exists( 'cw_get_singleton_id' )
		? cw_get_singleton_id( 'reglage' )
		: 0;

	// A missing translation must use that language's dictionary, never French copy.
	if ( function_exists( 'cw_polylang_active' ) && cw_polylang_active() ) {
		$record_locale = $id && function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $id, 'slug' ) : false;
		if ( ! $id || ( $record_locale && $record_locale !== cw_locale() ) ) {
			$cache = array();
			return $cache;
		}
	}

	if ( ! $id ) {
		$posts = get_posts(
			array(
				'post_type'        => 'reglage',
				'name'             => 'reglages-site',
				'numberposts'      => 1,
				'post_status'      => 'publish',
				'suppress_filters' => false,
			)
		);

		$id = ! empty( $posts ) ? $posts[0]->ID : 0;
	}

	if ( ! $id ) {
		$cache = array();
		return $cache;
	}

	$out = array();

	$keys = array(
		'footerBlurb',
		'contactPhone',
		'contactEmail',
		'contactAddress',
		'linkedinUrl',
		'heroTitleLine1',
		'heroTitleHighlight',
		'heroTitleLine3',
		'heroSubtitle',
	);

	foreach ( $keys as $key ) {
		$value = trim( (string) get_post_meta( $id, $key, true ) );

		// Un champ laissé vide dans l'admin ne doit pas écraser le dictionnaire.
		if ( '' !== $value ) {
			$out[ $key ] = $value;
		}
	}

	$cache = $out;

	return $cache;
}

/**
 * Un réglage, avec repli sur le dictionnaire.
 *
 * @param string $key       Clé du réglage (ex. « contactPhone »).
 * @param string $dict_path Chemin dans le dictionnaire (ex. « footer.blurb »).
 * @param string $fallback  Valeur ultime.
 */
function cw_setting( string $key, string $dict_path = '', string $fallback = '' ): string {
	if ( 'linkedinUrl' === $key && function_exists( 'cw_editor_link' ) ) {
		$legacy = cw_site_settings();
		return cw_editor_link( 'linkedin', $legacy[$key] ?? $fallback );
	}
	if ( function_exists( 'cw_editor_has_override' ) && cw_editor_has_override( 'site.' . $key ) ) {
		return (string) cw_t( 'site.' . $key, '' );
	}
	if ( '' !== $dict_path && function_exists( 'cw_editor_has_override' ) && cw_editor_has_override( $dict_path ) ) {
		return (string) cw_t( $dict_path, '' );
	}
	$settings = cw_site_settings();

	if ( isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	if ( '' !== $dict_path ) {
		$value = cw_t( $dict_path, '' );

		if ( is_string( $value ) && '' !== $value ) {
			return $value;
		}
	}

	return $fallback;
}
