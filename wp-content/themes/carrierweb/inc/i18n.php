<?php
/**
 * Langues, dictionnaires, formats et URL du site.
 *
 * Le français utilise la racine ; l’arabe et l’anglais utilisent /ar et /en.
 * Polylang résout les URL des contenus traduits.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CW_LOCALES        = array( 'fr', 'ar', 'en' );
const CW_DEFAULT_LOCALE = 'fr';

/** Portail client externe — le site ne gère aucune authentification. */
const CW_CLIENT_PORTAL_URL = 'https://portalus1.carrierweb.com/website/web.redirect.php';

/**
 * Locale courante.
 *
 * @return string 'fr' | 'ar' | 'en'
 */
function cw_locale(): string {
	/**
	 * Filtre la locale courante.
	 *
	 * @param string $locale Locale détectée.
	 */
	$locale = apply_filters( 'cw_locale', CW_DEFAULT_LOCALE );

	return in_array( $locale, CW_LOCALES, true ) ? $locale : CW_DEFAULT_LOCALE;
}

/**
 * Sens d'écriture
 */
function cw_dir(): string {
	return 'ar' === cw_locale() ? 'rtl' : 'ltr';
}

/**
 * Préfixe d'URL de la locale
 */
function cw_locale_prefix( ?string $locale = null ): string {
	$locale = $locale ?? cw_locale();

	return CW_DEFAULT_LOCALE === $locale ? '' : '/' . $locale;
}

/**
 * Localise un chemin interne
 *
 * @param string      $path   Chemin nu, commençant par '/'.
 * @param string|null $locale Locale cible (courante par défaut).
 */
function cw_path( string $path, ?string $locale = null ): string {
	$prefix = cw_locale_prefix( $locale );

	if ( '/' === $path ) {
		return '' === $prefix ? '/' : $prefix;
	}

	return $prefix . $path;
}

/**
 * URL absolue localisée. C'est ce qu'appellent les gabarits pour tout href
 * interne — jamais home_url() en dur.
 *
 * @param string      $path   Chemin nu.
 * @param string|null $locale Locale cible.
 */
function cw_url( string $path = '/', ?string $locale = null ): string {
    $locale = $locale ?? cw_locale();
    if ( function_exists( 'cw_fixed_page_url' ) ) {
        $translated = cw_fixed_page_url( $path, $locale );
        if ( '' !== $translated ) {
            return $translated;
        }
    }
    if ( function_exists( 'cw_detail_page_url' ) ) {
        $translated = cw_detail_page_url( $path, $locale );
        if ( '' !== $translated ) {
            return $translated;
        }
    }
    return home_url( cw_path( $path, $locale ) );
}

/**
 * Retire le préfixe de locale
 */
function cw_strip_locale( string $path ): string {
	foreach ( CW_LOCALES as $l ) {
		if ( CW_DEFAULT_LOCALE === $l ) {
			continue;
		}

		if ( '/' . $l === $path ) {
			return '/';
		}

		if ( str_starts_with( $path, '/' . $l . '/' ) ) {
			return substr( $path, strlen( $l ) + 1 );
		}
	}

	return $path;
}

/**
 * Chemin courant sans préfixe de locale.
 */
function cw_current_path(): string {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
	$path = wp_parse_url( $uri, PHP_URL_PATH );

	if ( ! is_string( $path ) || '' === $path ) {
		$path = '/';
	}

	// Sous-répertoire éventuel de l'install WordPress.
	$base = wp_parse_url( home_url(), PHP_URL_PATH );

	if ( is_string( $base ) && '/' !== $base && str_starts_with( $path, $base ) ) {
		$path = substr( $path, strlen( $base ) );
	}

	$path = '/' . ltrim( cw_strip_locale( '/' . ltrim( $path, '/' ) ), '/' );

	// Normalise le slash final ('/solutions/' et '/solutions' sont la même page).
	return '/' !== $path ? untrailingslashit( $path ) : '/';
}

/**
 * L'entrée de navigation $href correspond-elle à la page courante ?
 */
function cw_is_active( string $href ): bool {
	$current = cw_current_path();

	if ( '/' === $href ) {
		return '/' === $current;
	}

	return str_starts_with( $current, $href );
}

/**
 * Dictionnaire complet de la locale courante.
 *
 * @return array<string, mixed>
 */
function cw_dict(): array {
	static $cache = array();

	$locale = cw_locale();

	if ( isset( $cache[ $locale ] ) ) {
		return apply_filters( 'cw_dictionary', $cache[ $locale ], $locale );
	}

	$file = CW_DIR . '/inc/dictionaries/' . $locale . '.php';

	// Repli sur le français : une clé manquante ne doit jamais vider une page.
	if ( ! is_readable( $file ) ) {
		$file = CW_DIR . '/inc/dictionaries/' . CW_DEFAULT_LOCALE . '.php';
	}

	$cache[ $locale ] = is_readable( $file ) ? (array) require $file : array();

	return apply_filters( 'cw_dictionary', $cache[ $locale ], $locale );
}

/**
 * Traduction par chemin pointé
 *
 * @param string $path     Chemin pointé, ex. 'header.nav.solutions'.
 * @param mixed  $fallback Valeur si la clé est absente.
 * @return mixed Chaîne, tableau (listes comme header.solutionItems), ou $fallback.
 */
function cw_t( string $path, $fallback = '' ) {
	$node = cw_dict();

	foreach ( explode( '.', $path ) as $segment ) {
		if ( ! is_array( $node ) || ! array_key_exists( $segment, $node ) ) {
			return $fallback;
		}

		$node = $node[ $segment ];
	}

	return $node;
}

/**
 * cw_t() échappé pour affichage direct dans un gabarit.
 */
function cw_e( string $path, string $fallback = '' ): void {
	$value = cw_t( $path, $fallback );

	echo esc_html( is_scalar( $value ) ? (string) $value : $fallback );
}

/**
 * Code BCP 47 pour <html lang>
 */
function cw_html_lang(): string {
	return cw_locale();
}

/**
 * Locale Open Graph
 */
function cw_og_locale(): string {
	$map = array(
		'fr' => 'fr_MA',
		'ar' => 'ar_MA',
		'en' => 'en_US',
	);

	return $map[ cw_locale() ] ?? 'fr_MA';
}

/**
 * Locale de formatage dates/nombres
 */
function cw_intl_locale(): string {
	$map = array(
		'fr' => 'fr_MA',
		'ar' => 'ar_MA',
		'en' => 'en_GB',
	);

	return $map[ cw_locale() ] ?? 'fr_MA';
}

/**
 * Date longue localisée
 *
 * @param string $date   Date ISO (AAAA-MM-JJ) ou tout format reconnu par strtotime.
 * @param string $format Motif ICU. Par défaut : « 10 juin 2026 ».
 */
function cw_format_date( string $date, string $format = 'd MMMM y' ): string {
	$timestamp = strtotime( $date );

	if ( ! $timestamp ) {
		return '';
	}

	if ( class_exists( 'IntlDateFormatter' ) ) {
		$formatter = new IntlDateFormatter(
			cw_intl_locale(),
			IntlDateFormatter::LONG,
			IntlDateFormatter::NONE,
			wp_timezone(),
			IntlDateFormatter::GREGORIAN,
			$format
		);

		$formatted = $formatter->format( $timestamp );

		if ( false !== $formatted ) {
			return $formatted;
		}
	}

	// Repli si l'extension intl est absente : noms de mois portés par le thème.
	$months = cw_t( 'common.months', array() );

	if ( is_array( $months ) && 12 === count( $months ) ) {
		$month = (int) wp_date( 'n', $timestamp );

		return wp_date( 'j', $timestamp ) . ' ' . $months[ $month - 1 ] . ' ' . wp_date( 'Y', $timestamp );
	}

	return wp_date( 'j F Y', $timestamp );
}

/**
 * Libellés courts du sélecteur de langue
 */
function cw_locale_labels(): array {
	return array(
		'fr' => 'FR',
		'ar' => 'AR',
		'en' => 'EN',
	);
}

/**
 * Noms complets des langues
 */
function cw_locale_names(): array {
	return array(
		'fr' => 'Français',
		'ar' => 'العربية',
		'en' => 'English',
	);
}

/**
 * Canonical + hreflang d'une page
 *
 * @param string|null $path Chemin nu ; chemin courant par défaut.
 * @return array{canonical:string, languages:array<string,string>}
 */
function cw_alternates( ?string $path = null ): array {
	$path = $path ?? cw_current_path();

	$languages = array();

	foreach ( CW_LOCALES as $l ) {
		$languages[ $l ] = cw_url( $path, $l );
	}

	$languages['x-default'] = cw_url( $path, CW_DEFAULT_LOCALE );

	return array(
		'canonical' => cw_url( $path ),
		'languages' => $languages,
	);
}
