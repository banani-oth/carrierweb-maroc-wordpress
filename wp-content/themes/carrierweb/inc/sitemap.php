<?php
/**
 * Sitemap XML et robots.txt
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Le sitemap du core ne gère pas hreflang : il ferait doublon et induirait
// les moteurs en erreur.
add_filter( 'wp_sitemaps_enabled', '__return_false' );

/** Enregistre /sitemap.xml. */
add_action(
	'init',
	function () {
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?cw_sitemap=1', 'top' );
	}
);

add_filter(
	'query_vars',
	function ( $vars ) {
		$vars[] = 'cw_sitemap';

		return $vars;
	}
);

/**
 * Toutes les URL du site, chaque entrée portant ses variantes de langue.
 *
 * @return array<int, array{loc:string, alternates:array<string,string>, changefreq:string, priority:string, lastmod:string}>
 */
function cw_sitemap_entries(): array {
	$entries = array();


	$static = array(
		'/'                    => array( '1.0', 'weekly' ),
		'/solutions'           => array( '0.9', 'monthly' ),
		'/secteurs'            => array( '0.9', 'monthly' ),
		'/pourquoi-carrierweb' => array( '0.8', 'monthly' ),
		'/references'          => array( '0.8', 'monthly' ),
		'/ressources'          => array( '0.8', 'weekly' ),
		'/contact'             => array( '0.7', 'yearly' ),
		'/espace-client'       => array( '0.6', 'yearly' ),
	);

	foreach ( $static as $path => $meta ) {
		$alternates = array();

		foreach ( CW_LOCALES as $locale ) {
			$alternates[ $locale ] = cw_url( $path, $locale );
		}

		$entries[] = array(
			'loc'        => $alternates[ CW_DEFAULT_LOCALE ],
			'alternates' => $alternates,
			'priority'   => $meta[0],
			'changefreq' => $meta[1],
			'lastmod'    => '',
		);
	}

	/*
	 * Contenus : on part des posts FRANÇAIS et on récupère l'URL réelle de
	 * chaque traduction via Polylang — les slugs traduits diffèrent
	 * (suivi-gps-ar), on ne peut donc pas les déduire du chemin français.
	 */
	$types = array(
		'solution' => '0.8',
		'secteur'  => '0.8',
		'article'  => '0.7',
	);

	foreach ( $types as $type => $priority ) {
		if ( ! post_type_exists( $type ) ) {
			continue;
		}

		$posts = get_posts(
			array(
				'post_type'   => $type,
				'post_status' => 'publish',
				'numberposts' => -1,
				'lang'        => CW_DEFAULT_LOCALE,
			)
		);

		foreach ( $posts as $post ) {
			$alternates = array( CW_DEFAULT_LOCALE => get_permalink( $post ) );

			foreach ( CW_LOCALES as $locale ) {
				if ( CW_DEFAULT_LOCALE === $locale ) {
					continue;
				}

				if ( function_exists( 'pll_get_post' ) ) {
					$translated = pll_get_post( $post->ID, $locale );

					if ( $translated ) {
						$alternates[ $locale ] = get_permalink( $translated );
					}
				}
			}

			$entries[] = array(
				'loc'        => $alternates[ CW_DEFAULT_LOCALE ],
				'alternates' => $alternates,
				'priority'   => $priority,
				'changefreq' => 'monthly',
				'lastmod'    => get_post_modified_time( 'c', true, $post ),
			);
		}
	}

	return $entries;
}

/**
 * Empêche l'ajout du slash final sur /sitemap.xml.
 *
 * redirect_canonical() traite l'URL comme une page et redirige vers
 * /sitemap.xml/ — or robots.txt annonce /sitemap.xml, et les moteurs
 * attendent cette URL exacte. On désactive la redirection pour cette requête.
 */
add_filter(
	'redirect_canonical',
	function ( $redirect ) {
		return get_query_var( 'cw_sitemap' ) ? false : $redirect;
	}
);

/** Rend le sitemap. */
add_action(
	'template_redirect',
	function () {
		if ( ! get_query_var( 'cw_sitemap' ) ) {
			return;
		}

		header( 'Content-Type: application/xml; charset=UTF-8' );
		header( 'X-Robots-Tag: noindex' );

		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

		foreach ( cw_sitemap_entries() as $entry ) {
			echo "\t<url>\n";
			echo "\t\t<loc>" . esc_url( $entry['loc'] ) . "</loc>\n";

			// Chaque URL déclare TOUTES ses variantes, y compris elle-même :
			// c'est ce qu'attend la spécification hreflang.
			foreach ( $entry['alternates'] as $locale => $url ) {
				printf(
					"\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\"/>\n",
					esc_attr( $locale ),
					esc_url( $url )
				);
			}

			if ( ! empty( $entry['alternates'][ CW_DEFAULT_LOCALE ] ) ) {
				printf(
					"\t\t<xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"%s\"/>\n",
					esc_url( $entry['alternates'][ CW_DEFAULT_LOCALE ] )
				);
			}

			if ( ! empty( $entry['lastmod'] ) ) {
				echo "\t\t<lastmod>" . esc_html( $entry['lastmod'] ) . "</lastmod>\n";
			}

			echo "\t\t<changefreq>" . esc_html( $entry['changefreq'] ) . "</changefreq>\n";
			echo "\t\t<priority>" . esc_html( $entry['priority'] ) . "</priority>\n";
			echo "\t</url>\n";
		}

		echo '</urlset>';
		exit;
	}
);

/**
 * robots.txt
 */
add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( ! $public || cw_site_noindex() ) {
			// Site marqué non public : on laisse WordPress tout interdire.
			return "User-agent: *\nDisallow: /\n";
		}

		$lines = array(
			'User-agent: *',
			'Allow: /',
			'',
			// Pages sans valeur de référencement (cf. noindex dans inc/seo.php).
			'Disallow: /connexion',
			'Disallow: /inscription',
			'Disallow: /ar/connexion',
			'Disallow: /ar/inscription',
			'Disallow: /en/connexion',
			'Disallow: /en/inscription',
			'Disallow: /wp-admin/',
			'Allow: /wp-admin/admin-ajax.php',
			'',
			'Sitemap: ' . home_url( '/sitemap.xml' ),
		);

		return implode( "\n", $lines ) . "\n";
	},
	10,
	2
);

/** Flush unique quand la règle du sitemap est ajoutée. */
add_action(
	'init',
	function () {
		if ( 'sitemap-1' === get_option( 'cw_sitemap_rewrite' ) ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( 'cw_sitemap_rewrite', 'sitemap-1', false );
	},
	99
);
