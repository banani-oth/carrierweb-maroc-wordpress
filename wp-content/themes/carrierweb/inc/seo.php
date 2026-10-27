<?php
/**
 * Titres, description, indexation, liens canoniques et métadonnées de partage.
 *
 * Polylang fournit les variantes de langue ; ce module ajoute x-default.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Use this installation's URL for schema IDs and canonical metadata. */
define( 'CW_SITE_URL', untrailingslashit( home_url( '/' ) ) );

/** Honour search visibility and keep local/staging copies out of search. */
function cw_site_noindex(): bool {
    return ! (bool) get_option( 'blog_public', 1 )
        || ( function_exists( 'wp_get_environment_type' ) && 'production' !== wp_get_environment_type() );
}

/**
 * Métadonnées de la page courante.
 *
 * Un seul endroit décide du titre, de la description, du chemin canonique et
 * de l'indexation — les balises <title>, <meta>, OG et JSON-LD en découlent.
 *
 * @return array{title:string, description:string, path:string, noindex:bool, type:string}
 */
function cw_seo_context(): array {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$ctx = array(
		'title'       => (string) cw_t( 'meta.rootTitle' ),
		'description' => (string) cw_t( 'meta.rootDescription' ),
		'path'        => '/',
		'noindex'     => false,
		'type'        => 'website',
	);

	if ( is_front_page() ) {
		// Valeurs par défaut ci-dessus.
		$cache = $ctx;
		return $ctx;
	}

	if ( is_post_type_archive( 'solution' ) ) {
		$ctx['title']       = (string) cw_t( 'meta.solutions.title' );
		$ctx['description'] = (string) cw_t( 'meta.solutions.description' );
		$ctx['path']        = '/solutions';
	} elseif ( is_post_type_archive( 'secteur' ) ) {
		$ctx['title']       = (string) cw_t( 'meta.sectors.title' );
		$ctx['description'] = (string) cw_t( 'meta.sectors.description' );
		$ctx['path']        = '/secteurs';
	} elseif ( is_post_type_archive( 'article' ) ) {
		$ctx['title']       = (string) cw_t( 'meta.resources.title' );
		$ctx['description'] = (string) cw_t( 'meta.resources.description' );
		$ctx['path']        = '/ressources';
	} elseif ( is_singular( 'solution' ) ) {
		$solution           = cw_get_solution( get_the_ID() );
		$ctx['title']       = $solution['title'] ?? '';
		$ctx['description'] = $solution['tagline'] ?? '';
		$ctx['path']        = wp_make_link_relative( get_permalink() );
		$ctx['type']        = 'article';
	} elseif ( is_singular( 'secteur' ) ) {
		$sector             = cw_get_sector( get_the_ID() );
		$ctx['title']       = $sector['title'] ?? '';
		$ctx['description'] = $sector['tagline'] ?? '';
		$ctx['path']        = wp_make_link_relative( get_permalink() );
		$ctx['type']        = 'article';
	} elseif ( is_singular( 'article' ) ) {
		$article            = cw_get_article( get_the_ID() );
		$ctx['title']       = $article['title'] ?? '';
		$ctx['description'] = $article['metaDescription'] ?? ( $article['excerpt'] ?? '' );
		$ctx['path']        = wp_make_link_relative( get_permalink() );
		$ctx['type']        = 'article';
	} elseif ( is_page() ) {
		$slug = get_post_meta( get_the_ID(), '_cw_fixed_slug', true ) ?: get_post_field( 'post_name', get_the_ID() );

		/*
		 * Les pages d'authentification et les mentions légales restent en
		 * noindex : les premières n'ont aucune valeur de référencement, les
		 * secondes tant que les identifiants légaux définitifs manquent.
		 */
		$map = array(
			'pourquoi-carrierweb' => array( 'meta.why', false ),
			'references'          => array( 'meta.references', false ),
			'contact'             => array( 'meta.contact', false ),
			'espace-client'       => array( 'meta.clientArea', false ),
			'connexion'           => array( 'meta.login', true ),
			'inscription'         => array( 'meta.register', true ),
			'mentions-legales'    => array( null, true ),
			'confidentialite'     => array( null, true ),
		);

		$entry = $map[ $slug ] ?? null;

		if ( $entry ) {
			if ( $entry[0] ) {
				$ctx['title']       = (string) cw_t( $entry[0] . '.title' );
				if ( 'inscription' === $slug ) {
					$access_titles = array( 'fr' => 'Demander un accès client', 'en' => 'Request client access', 'ar' => 'طلب الوصول إلى فضاء العملاء' );
					$ctx['title'] = $access_titles[ cw_locale() ];
				}
				$ctx['description'] = (string) cw_t( $entry[0] . '.description' );
			} else {
				$ctx['title'] = get_the_title();
			}

			$ctx['noindex'] = $entry[1];
		} else {
			$ctx['title'] = get_the_title();
		}

		$ctx['path'] = wp_make_link_relative( get_permalink() );
	} elseif ( is_404() ) {
		$ctx['noindex'] = true;
		$ctx['title']   = __( 'Page introuvable', 'carrierweb' );
	} elseif ( is_search() ) {
		$ctx['noindex'] = true;
	}

	$cache = $ctx;

	return $ctx;
}

/**
 * Chemin sans préfixe de langue, pour construire les alternates.
 *
 * Avec Polylang les slugs traduits diffèrent (suivi-gps-ar) : on ne peut pas
 * déduire l'URL d'une autre langue par préfixage. On demande donc à Polylang
 * l'URL réelle de chaque traduction.
 *
 * @return array<string,string> locale => URL absolue.
 */
function cw_seo_alternates(): array {
	$out = array();

	if ( function_exists( 'pll_the_languages' ) ) {
		$raw = pll_the_languages(
			array(
				'raw'                    => 1,
				'hide_if_no_translation' => 0,
			)
		);

		foreach ( (array) $raw as $entry ) {
			if ( ! empty( $entry['slug'] ) && ! empty( $entry['url'] ) ) {
				$out[ $entry['slug'] ] = $entry['url'];
			}
		}
	}

	if ( empty( $out ) ) {
		$path = cw_strip_locale( cw_seo_context()['path'] ?: '/' );

		foreach ( CW_LOCALES as $locale ) {
			$out[ $locale ] = cw_url( $path, $locale );
		}
	}

	return $out;
}


add_filter(
	'document_title_parts',
	function ( $parts ) {
		$ctx = cw_seo_context();

		$parts['title'] = $ctx['title'];

		// Le titre de la page est fourni par son contexte.
		unset( $parts['site'], $parts['tagline'], $parts['page'] );

		return $parts;
	}
);

/** Sépare les segments du titre (inutilisé, mais évite le « - » par défaut). */
add_filter( 'document_title_separator', fn() => '—' );

/**
 * Bloc <head> : description, robots, canonical, x-default, OG et Twitter.
 *
 * Priorité 3 : après le préchargement des polices (priorité 2), avant le reste.
 */
add_action(
	'wp_head',
	function () {
		$ctx        = cw_seo_context();
		$alternates = cw_seo_alternates();
		$canonical  = $alternates[ cw_locale() ] ?? cw_url( $ctx['path'] );

		// ── Description ──
		if ( '' !== $ctx['description'] ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $ctx['description'] ) );
		}

		// ── Indexation ──
		if ( $ctx['noindex'] || cw_site_noindex() ) {
			echo '<meta name="robots" content="noindex, follow">' . "\n";
		} else {
			echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
		}

		// ── Canonical ──
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );

		/*
		 * x-default : Polylang émet fr/ar/en mais PAS x-default. Il désigne la
		 * version à servir quand aucune langue ne correspond — le français ici.
		 */
		if ( ! empty( $alternates[ CW_DEFAULT_LOCALE ] ) ) {
			printf(
				'<link rel="alternate" href="%s" hreflang="x-default">' . "\n",
				esc_url( $alternates[ CW_DEFAULT_LOCALE ] )
			);
		}

		// ── Open Graph ──
		$og_title = is_front_page() ? (string) cw_t( 'meta.ogTitle', $ctx['title'] ) : $ctx['title'];
		$og_desc  = is_front_page() ? (string) cw_t( 'meta.ogDescription', $ctx['description'] ) : $ctx['description'];

		$og = array(
			'og:type'        => $ctx['type'],
			'og:site_name'   => 'CarrierWeb Maroc',
			'og:title'       => $og_title,
			'og:description' => $og_desc,
			'og:url'         => $canonical,
			'og:image'       => cw_media_url( 'social_image', CW_URI . '/assets/img/og-image.png' ),
			'og:locale'      => cw_og_locale(),
		);

		foreach ( $og as $property => $content ) {
			if ( '' !== $content ) {
				printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
			}
		}

		// Les autres langues disponibles, pour les partages sociaux.
		foreach ( CW_LOCALES as $locale ) {
			if ( $locale === cw_locale() || empty( $alternates[ $locale ] ) ) {
				continue;
			}

			$map = array(
				'fr' => 'fr_MA',
				'ar' => 'ar_MA',
				'en' => 'en_US',
			);

			printf( '<meta property="og:locale:alternate" content="%s">' . "\n", esc_attr( $map[ $locale ] ) );
		}

		// ── Twitter ──
		$twitter = array(
			'twitter:card'        => 'summary_large_image',
			'twitter:title'       => $og_title,
			'twitter:description' => $og_desc,
			'twitter:image'       => cw_media_url( 'social_image', CW_URI . '/assets/img/og-image.png' ),
		);

		foreach ( $twitter as $name => $content ) {
			if ( '' !== $content ) {
				printf( '<meta name="%s" content="%s">' . "\n", esc_attr( $name ), esc_attr( $content ) );
			}
		}

		// Vérification Google Search Console, si fournie via wp-config.
		if ( defined( 'CW_GOOGLE_SITE_VERIFICATION' ) && CW_GOOGLE_SITE_VERIFICATION ) {
			printf(
				'<meta name="google-site-verification" content="%s">' . "\n",
				esc_attr( CW_GOOGLE_SITE_VERIFICATION )
			);
		}
	},
	3
);

/**
 * Dégraissage du <head>.
 *
 * Le site n'a ni blog, ni commentaires, ni client XML-RPC : ces balises ne
 * servent à rien et exposent inutilement la version de WordPress.
 */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );

		// Le core émet sa propre balise robots ; la nôtre est plus précise.
		remove_action( 'wp_head', 'wp_robots', 1 );

		/*
		 * Le core pose aussi son propre rel=canonical sur les contenus uniques.
		 * Sans ça la page en portait DEUX — les moteurs n'en retiennent alors
		 * aucune de façon fiable. La nôtre est la bonne : elle tient compte de
		 * la langue et du domaine canonique de production.
		 */
		remove_action( 'wp_head', 'rel_canonical' );
	}
);
