<?php
/**
 * Textes et listes des pages fixes.
 *
 * Les champs WordPress remplacent les valeurs du dictionnaire lorsqu’ils sont renseignés.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Méta du singleton d'un CPT de page fixe.
 *
 * Lecture directe de la table meta (pas get_field()) : ces champs sont des
 * scalaires, donc ACF n'apporte rien au rendu et le front survit à sa
 * désactivation. update_meta_cache() garantit une seule requête par page.
 *
 * @param string $cpt Nom du CPT (ex. « page_accueil »).
 * @return array<string, string>
 */
function cw_page_meta( string $cpt ): array {
	static $cache = array();

	if ( isset( $cache[ $cpt ] ) ) {
		return $cache[ $cpt ];
	}

	if ( ! post_type_exists( $cpt ) ) {
		$cache[ $cpt ] = array();
		return $cache[ $cpt ];
	}

	/*
	 * Post de la LANGUE COURANTE. Si l'équipe n'a pas encore traduit cette
	 * page fixe, il n'y en a pas — et cw_page_text() retombe sur le
	 * dictionnaire. La page reste donc toujours complète.
	 */
	$id = function_exists( 'cw_get_singleton_id' ) ? cw_get_singleton_id( $cpt ) : 0;

	if ( ! $id ) {
		$cache[ $cpt ] = array();
		return $cache[ $cpt ];
	}

	$raw = get_post_meta( $id );
	$out = array();
	$french_meta = array();
	if ( CW_DEFAULT_LOCALE !== cw_locale() && function_exists( 'pll_get_post' ) ) {
		$french_id = pll_get_post( $id, CW_DEFAULT_LOCALE );
		if ( ! $french_id ) {
			$candidates = get_posts( array( 'post_type' => $cpt, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'lang' => CW_DEFAULT_LOCALE, 'suppress_filters' => false ) );
			foreach ( $candidates as $candidate ) {
				if ( pll_get_post_language( (int) $candidate, 'slug' ) === CW_DEFAULT_LOCALE ) { $french_id = (int) $candidate; break; }
			}
		}
		if ( $french_id && (int) $french_id !== (int) $id ) {
			$french_meta = get_post_meta( $french_id );
		}
	}

	foreach ( $raw as $key => $values ) {
		// Les clés préfixées « _ » sont les références de champ internes d'ACF.
		if ( '_' === $key[0] ) {
			continue;
		}

		$value = is_array( $values ) ? ( $values[0] ?? '' ) : $values;
		$original_value = (string) $value;
		$value = trim( $original_value );

		// Early translation imports duplicated French fields. Keep those records
		// intact, but let the existing translated dictionary fill untranslated copy.
		if ( isset( $french_meta[ $key ][0] ) && $value === trim( (string) $french_meta[ $key ][0] ) ) {
			continue;
		}
		if ( '' !== $value ) {
			$out[ $key ] = $original_value;
		}
	}

	$cache[ $cpt ] = $out;

	return $cache[ $cpt ];
}

/**
 * Un texte : valeur WordPress si renseignée, sinon dictionnaire.
 *
 * @param string $cpt       CPT source (ex. « page_accueil »).
 * @param string $wp_key    Nom du champ ACF (ex. « services_label »).
 * @param string $dict_path Chemin dans le dictionnaire (ex. « services.label »).
 * @param string $fallback  Valeur ultime.
 */
function cw_page_text( string $cpt, string $wp_key, string $dict_path, string $fallback = '' ): string {
	if ( '' !== $dict_path && function_exists( 'cw_editor_has_override' ) && cw_editor_has_override( $dict_path ) ) {
		return (string) cw_t( $dict_path, '' );
	}
	$meta = cw_page_meta( $cpt );

	if ( isset( $meta[ $wp_key ] ) ) {
		return $meta[ $wp_key ];
	}

	$value = cw_t( $dict_path, '' );

	return is_string( $value ) && '' !== $value ? $value : $fallback;
}

/**
 * Une liste : les entrées WordPress numérotées surchargent celles du
 * dictionnaire, entrée par entrée ET champ par champ.
 *
 * Exemple — trois atouts de la section « Pourquoi » :
 *   cw_page_list( 'page_accueil', 'why_feature', array( 'title', 'desc' ), 3, 'why.features',
 *                 array( 'title' => 'title', 'desc' => 'description' ) )
 * lit why_feature_1_title / why_feature_1_desc… et retombe sur
 * why.features[0].title / .description quand le champ est vide.
 *
 * @param string                $cpt       CPT source.
 * @param string                $prefix    Préfixe des champs (ex. « why_feature »).
 * @param array<int,string>     $wp_keys   Suffixes des champs (ex. ['title','desc']).
 * @param int                   $count     Nombre d'entrées.
 * @param string                $dict_path Chemin de la liste dans le dictionnaire.
 * @param array<string,string>  $map       Suffixe WP => clé du dictionnaire.
 * @return array<int, array<string,string>>
 */
function cw_page_list( string $cpt, string $prefix, array $wp_keys, int $count, string $dict_path, array $map = array() ): array {
	if ( function_exists( 'cw_editor_has_override' ) && cw_editor_has_override( $dict_path ) ) {
		$out = array();
		foreach ( (array) cw_t( $dict_path, array() ) as $entry ) {
			$row = array();
			foreach ( $wp_keys as $key ) { $row[ $key ] = (string) ( $entry[ $map[ $key ] ?? $key ] ?? '' ); }
			$out[] = $row;
		}
		return $out;
	}
	$meta = cw_page_meta( $cpt );
	$dict = cw_t( $dict_path, array() );
	$dict = is_array( $dict ) ? $dict : array();
	$out  = array();

	for ( $i = 1; $i <= $count; $i++ ) {
		$row     = array();
		$dict_row = $dict[ $i - 1 ] ?? array();

		foreach ( $wp_keys as $key ) {
			$wp_value   = $meta[ $prefix . '_' . $i . '_' . $key ] ?? '';
			$dict_key   = $map[ $key ] ?? $key;
			$dict_value = is_array( $dict_row ) ? ( $dict_row[ $dict_key ] ?? '' ) : '';

			$row[ $key ] = '' !== $wp_value ? $wp_value : (string) $dict_value;
		}

		// On ne garde la ligne que si elle a du contenu (équivalent de compact()).
		$has_content = false;

		foreach ( $row as $value ) {
			if ( '' !== $value ) {
				$has_content = true;
				break;
			}
		}

		if ( $has_content ) {
			$out[] = $row;
		}
	}

	return $out;
}

/** Raccourci : un texte de la page d'accueil. */
function cw_home_text( string $wp_key, string $dict_path, string $fallback = '' ): string {
	return cw_page_text( 'page_accueil', $wp_key, $dict_path, $fallback );
}

/** Raccourci échappé, pour affichage direct dans un gabarit. */
function cw_home_e( string $wp_key, string $dict_path, string $fallback = '' ): void {
	echo esc_html( cw_home_text( $wp_key, $dict_path, $fallback ) );
}

/** Raccourci : une liste de la page d'accueil. */
function cw_home_list( string $prefix, array $wp_keys, int $count, string $dict_path, array $map = array() ): array {
	return cw_page_list( 'page_accueil', $prefix, $wp_keys, $count, $dict_path, $map );
}

/**
 * Titre de section « début + partie rouge ».
 *
 * Motif répété dans presque toutes les sections : un début en blanc, un
 * fragment en rouge, parfois une fin. Centralisé ici pour éviter de répéter
 * le balisage — et l'échappement — dans onze gabarits.
 *
 * @param string $pre  Début du titre.
 * @param string $hl   Fragment mis en avant (rouge).
 * @param string $post Fin éventuelle.
 * @param string $class Classes du <h2>.
 */
function cw_section_title( string $pre, string $hl, string $post = '', string $class = 'section-title' ): void {
	printf(
		'<h2 class="%s">%s<span class="text-cw-red-light">%s</span>%s</h2>',
		esc_attr( $class ),
		esc_html( $pre ),
		esc_html( $hl ),
		esc_html( $post )
	);
}
