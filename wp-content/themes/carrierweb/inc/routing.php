<?php
/**
 * Routage de langue utilisé lorsque Polylang est désactivé.
 *
 * Le français reste à la racine ; les variantes utilisent /ar et /en.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Locale détectée dans l'URL. Renseignée par cw_detect_locale_from_uri().
 *
 * @var string|null
 */
$GLOBALS['cw_request_locale'] = null;

/**
 * Détecte et retire le préfixe de langue de la requête courante.
 *
 * Appelée au chargement du thème (setup_theme), donc bien avant
 * WP::parse_request() qui lit $_SERVER['REQUEST_URI'].
 */
function cw_detect_locale_from_uri(): void {
	/*
	 * Polylang, s'il est actif, gère lui-même le routage des langues.
	 *
	 * On teste ici la PRÉSENCE DU PLUGIN, pas le filtre cw_use_custom_routing :
	 * cette fonction est appelée directement au chargement du module, donc
	 * AVANT inc/polylang.php qui enregistre ce filtre. S'y fier laissait le
	 * routage maison retirer le préfixe /en/ avant que Polylang ne le voie ;
	 * Polylang croyait alors la page française, résolvait le contenu anglais
	 * (slugs identiques d'une langue à l'autre) et redirigeait vers /en/ —
	 * boucle de redirection infinie.
	 *
	 * Les plugins sont chargés avant le thème : la constante est donc fiable ici.
	 */
	if ( defined( 'POLYLANG_VERSION' ) ) {
		return;
	}

	if ( ! apply_filters( 'cw_use_custom_routing', true ) ) {
		return;
	}

	// L'admin, l'AJAX, le REST et le cron ne sont jamais préfixés.
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

	// Sépare le chemin de la query string pour ne toucher qu'au chemin.
	$parts = explode( '?', $uri, 2 );
	$path  = $parts[0];
	$query = isset( $parts[1] ) ? '?' . $parts[1] : '';

	// Tient compte d'une install WordPress en sous-répertoire.
	$base = wp_parse_url( home_url(), PHP_URL_PATH );
	$base = is_string( $base ) ? untrailingslashit( $base ) : '';

	$relative = $path;

	if ( '' !== $base && str_starts_with( $path, $base ) ) {
		$relative = substr( $path, strlen( $base ) );
	}

	$relative = '/' . ltrim( $relative, '/' );

	foreach ( CW_LOCALES as $locale ) {
		if ( CW_DEFAULT_LOCALE === $locale ) {
			continue;
		}

		$prefix = '/' . $locale;

		if ( $relative !== $prefix && ! str_starts_with( $relative, $prefix . '/' ) ) {
			continue;
		}

		$GLOBALS['cw_request_locale'] = $locale;

		// Chemin nu, tel que WordPress doit le résoudre.
		$stripped = substr( $relative, strlen( $prefix ) );
		$stripped = '' === $stripped ? '/' : $stripped;

		$_SERVER['REQUEST_URI'] = $base . $stripped . $query;

		return;
	}
}

/*
 * Appel DIRECT, pas via add_action().
 *
 * L'action `setup_theme` est déclenchée par wp-settings.php AVANT le
 * chargement du functions.php du thème : s'y accrocher ne servirait à rien,
 * le callback ne serait jamais exécuté. On exécute donc la détection au
 * chargement du module — ce qui reste très en amont de WP::parse_request(),
 * seul moment où $_SERVER['REQUEST_URI'] est réellement lu.
 */
cw_detect_locale_from_uri();

/**
 * Branche la locale détectée sur la façade i18n.
 *
 * Seul point de couplage entre le routage et cw_locale() : si l'on passe un
 * jour à Polylang, c'est ce filtre-ci que l'on remplace, rien d'autre.
 */
add_filter(
	'cw_locale',
	function ( $locale ) {
		return $GLOBALS['cw_request_locale'] ?? $locale;
	}
);

/**
 * /fr/... → 301 vers l'URL sans préfixe
 */
add_action(
	'template_redirect',
	function () {
		// Polylang applique déjà cette redirection.
		if ( ! apply_filters( 'cw_use_custom_routing', true ) ) {
			return;
		}

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$uri  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$path = explode( '?', $uri, 2 )[0];

		$base = wp_parse_url( home_url(), PHP_URL_PATH );
		$base = is_string( $base ) ? untrailingslashit( $base ) : '';

		$relative = '' !== $base && str_starts_with( $path, $base ) ? substr( $path, strlen( $base ) ) : $path;
		$relative = '/' . ltrim( $relative, '/' );

		if ( '/fr' !== $relative && ! str_starts_with( $relative, '/fr/' ) ) {
			return;
		}

		$stripped = substr( $relative, 3 );
		$stripped = '' === $stripped ? '/' : $stripped;

		wp_safe_redirect( home_url( $stripped ), 301 );
		exit;
	},
	1
);

/**
 * Réinjecte le préfixe de langue dans les permaliens générés par WordPress.
 *
 * get_permalink() ne connaît pas nos préfixes : sur /ar/, un lien vers une
 * solution pointerait vers la version française. On corrige à la source, pour
 * que les gabarits n'aient jamais à y penser.
 *
 * @param string $url URL générée par WordPress.
 */
function cw_localize_permalink( string $url ): string {
	// Polylang produit déjà des permaliens localisés.
	if ( ! apply_filters( 'cw_use_custom_routing', true ) ) {
		return $url;
	}

	$locale = cw_locale();

	if ( CW_DEFAULT_LOCALE === $locale ) {
		return $url;
	}

	$home = untrailingslashit( home_url() );

	// N'agit que sur les URL internes.
	if ( ! str_starts_with( $url, $home ) ) {
		return $url;
	}

	$path = substr( $url, strlen( $home ) );

	// Déjà préfixée : ne rien faire (évite /ar/ar/…).
	if ( str_starts_with( $path, '/' . $locale . '/' ) || '/' . $locale === $path ) {
		return $url;
	}

	return $home . '/' . $locale . $path;
}

foreach ( array( 'post_link', 'page_link', 'post_type_link', 'post_type_archive_link' ) as $cw_permalink_filter ) {
	add_filter( $cw_permalink_filter, 'cw_localize_permalink', 20 );
}

unset( $cw_permalink_filter );
