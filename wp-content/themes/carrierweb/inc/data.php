<?php
/**
 * Lecture des contenus WordPress et reconstruction des champs ACF numérotés.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contenus traduits de la locale courante
 *
 * @return array{solutions?:array, sectors?:array, articles?:array}
 */
function cw_translated_content(): array {
	static $cache = array();

	$locale = cw_locale();

	if ( CW_DEFAULT_LOCALE === $locale ) {
		return array();
	}

	/*
	 * Polylang actif : les traductions sont désormais de VRAIS contenus
	 * WordPress, éditables par l'équipe (importés par tools/import-translations.php).
	 * On ignore donc les fichiers PHP, qui ne servent plus que de repli si le
	 * plugin est désactivé — et de source pour un ré-import.
	 */
	if ( function_exists( 'cw_polylang_active' ) && cw_polylang_active() ) {
		return array();
	}

	if ( isset( $cache[ $locale ] ) ) {
		return $cache[ $locale ];
	}

	$file = CW_DIR . '/inc/content/' . $locale . '.php';

	$cache[ $locale ] = is_readable( $file ) ? (array) require $file : array();

	return $cache[ $locale ];
}

/**
 * Une entrée traduite, par slug.
 *
 * @param string $type solutions | sectors | articles.
 * @param string $slug Slug recherché (identique dans les 3 langues).
 * @return array<string,mixed>|null
 */
function cw_translated_item( string $type, string $slug ): ?array {
	foreach ( cw_translated_content()[ $type ] ?? array() as $item ) {
		if ( ( $item['slug'] ?? '' ) === $slug ) {
			return $item;
		}
	}

	return null;
}

/**
 * Valeur meta nettoyée.
 *
 * @param int    $post_id Post.
 * @param string $key     Clé meta.
 * @param string $default Valeur si vide.
 */
function cw_meta( int $post_id, string $key, string $default = '' ): string {
	$value = trim( (string) get_post_meta( $post_id, $key, true ) );

	return '' !== $value ? $value : $default;
}

/**
 * Reconstruit une liste depuis des champs ACF numérotés
 *
 * @param int                  $post_id Post.
 * @param string               $prefix  Préfixe (ex. « stat »).
 * @param array<int,string>    $keys    Suffixes (ex. ['value','label','icon']).
 * @param int                  $max     Nombre de lignes possibles.
 * @param array<string,mixed>  $opts    pattern, defaults, required.
 * @return array<int, array<string,string>>
 */
function cw_collect_numbered( int $post_id, string $prefix, array $keys, int $max, array $opts = array() ): array {
	$pattern  = $opts['pattern'] ?? 'snake';
	$defaults = $opts['defaults'] ?? array();
	$required = $opts['required'] ?? array();
	$out      = array();

	for ( $i = 1; $i <= $max; $i++ ) {
		$raw = array();

		foreach ( $keys as $key ) {
			$meta_key = 'camel' === $pattern
				? $prefix . $i . ucfirst( $key )
				: $prefix . '_' . $i . '_' . $key;

			$raw[ $key ] = cw_meta( $post_id, $meta_key );
		}

		// Vacuité jugée sur ce qui a réellement été saisi, avant tout défaut.
		$has_content = false;

		foreach ( $raw as $value ) {
			if ( '' !== $value ) {
				$has_content = true;
				break;
			}
		}

		if ( ! $has_content ) {
			continue;
		}

		// Certaines lignes n'ont de sens que si des champs précis sont remplis
		// (une section d'article sans titre ne s'affiche pas).
		$missing_required = false;

		foreach ( $required as $key ) {
			if ( '' === ( $raw[ $key ] ?? '' ) ) {
				$missing_required = true;
				break;
			}
		}

		if ( $missing_required ) {
			continue;
		}

		$row = $raw;

		foreach ( $defaults as $key => $default ) {
			if ( '' === ( $row[ $key ] ?? '' ) ) {
				$row[ $key ] = $default;
			}
		}

		$out[] = $row;
	}

	return $out;
}

/**
 * Une solution complète
 *
 * @param WP_Post|int $post Post ou ID.
 * @return array<string,mixed> Forme de l'interface Solution (lib/types.ts).
 */
function cw_get_solution( $post ): array {
	$post = get_post( $post );

	if ( ! $post ) {
		return array();
	}

	$id = $post->ID;

	// ar/en : contenu traduit, mais URL et ordre restent portés par WordPress.
	$translated = cw_translated_item( 'solutions', $post->post_name );

	if ( null !== $translated ) {
		return array_merge(
			$translated,
			array(
				'id'        => $id,
				'permalink' => get_permalink( $post ),
			)
		);
	}

	return array(
		'slug'             => $post->post_name,
		'title'            => get_the_title( $post ),
		'permalink'        => get_permalink( $post ),
		'icon'             => cw_meta( $id, 'icon', 'settings-2' ),
		'tagline'          => cw_meta( $id, 'tagline' ),
		'shortDescription' => cw_meta( $id, 'shortDescription' ),
		// description retombe sur shortDescription, comme dans mapSolution().
		'description'      => cw_meta( $id, 'description', cw_meta( $id, 'shortDescription' ) ),
		'stats'            => cw_collect_numbered( $id, 'stat', array( 'value', 'label', 'icon' ), 4, array( 'defaults' => array( 'icon' => 'zap' ), 'required' => array( 'value' ) ) ),
		'features'         => cw_collect_numbered( $id, 'feature', array( 'title', 'desc', 'icon' ), 4, array( 'defaults' => array( 'icon' => 'check-circle-2' ), 'required' => array( 'title' ) ) ),
		'specs'            => cw_collect_numbered( $id, 'spec', array( 'label', 'value' ), 6, array( 'required' => array( 'label', 'value' ) ) ),
		'faq'              => cw_collect_numbered( $id, 'faq', array( 'question', 'answer' ), 3, array( 'required' => array( 'question', 'answer' ) ) ),
		'benefits'         => cw_collect_numbered( $id, 'benefit', array( 'title', 'desc' ), 2, array( 'required' => array( 'title' ) ) ),
		'testimonial'      => array(
			'quote'   => cw_meta( $id, 'testimonialQuote' ),
			'author'  => cw_meta( $id, 'testimonialAuthor' ),
			'company' => cw_meta( $id, 'testimonialCompany' ),
		),
		'seoRichContent'   => cw_meta( $id, 'seoRichContent' ),
	);
}

/**
 * Un secteur complet
 *
 * @param WP_Post|int $post Post ou ID.
 * @return array<string,mixed> Forme de l'interface Sector (lib/types.ts).
 */
function cw_get_sector( $post ): array {
	$post = get_post( $post );

	if ( ! $post ) {
		return array();
	}

	$id = $post->ID;

	$translated = cw_translated_item( 'sectors', $post->post_name );

	if ( null !== $translated ) {
		return array_merge(
			$translated,
			array(
				'id'        => $id,
				'permalink' => get_permalink( $post ),
			)
		);
	}

	return array(
		'slug'                 => $post->post_name,
		'title'                => get_the_title( $post ),
		'permalink'            => get_permalink( $post ),
		'icon'                 => cw_meta( $id, 'icon', 'settings-2' ),
		'tagline'              => cw_meta( $id, 'tagline' ),
		'listingDescription'   => cw_meta( $id, 'listingDescription' ),
		'description'          => cw_meta( $id, 'description', cw_meta( $id, 'listingDescription' ) ),
		'useCases'             => cw_collect_numbered( $id, 'usecase', array( 'title', 'desc' ), 4, array( 'required' => array( 'title' ) ) ),
		'recommendedSolutions' => cw_collect_numbered( $id, 'recsol', array( 'title', 'href' ), 3, array( 'required' => array( 'title', 'href' ) ) ),
		'seoRichContent'       => cw_meta( $id, 'seoRichContent' ),
	);
}

/**
 * Découpe un textarea en lignes non vides
 *
 * @return array<int,string>
 */
function cw_split_lines( string $text ): array {
	$lines = preg_split( '/\R/u', $text ) ?: array();

	return array_values(
		array_filter(
			array_map( 'trim', $lines ),
			static fn( $l ) => '' !== $l
		)
	);
}

/**
 * Découpe un textarea en paragraphes
 *
 * @return array<int,string>
 */
function cw_split_paragraphs( string $text ): array {
	$parts = preg_split( '/\R\s*\R/u', $text ) ?: array();

	return array_values(
		array_filter(
			array_map( 'trim', $parts ),
			static fn( $p ) => '' !== $p
		)
	);
}

/**
 * Un article complet
 *
 * @param WP_Post|int $post Post ou ID.
 * @return array<string,mixed>
 */
function cw_get_article( $post ): array {
	$post = get_post( $post );

	if ( ! $post ) {
		return array();
	}

	$id = $post->ID;

	$translated = cw_translated_item( 'articles', $post->post_name );

	if ( null !== $translated ) {
		return array_merge(
			$translated,
			array(
				'id'        => $id,
				'permalink' => get_permalink( $post ),
				// Le contenu utilise publishedAt ; les gabarits
				// attendent publishedDate (nom du champ ACF). On normalise ici
				// plutôt que de faire porter les deux noms aux gabarits.
				'publishedDate' => $translated['publishedAt'] ?? '',
			)
		);
	}

	$raw_sections = cw_collect_numbered(
		$id,
		'section',
		array( 'heading', 'paragraphs', 'bullets' ),
		6,
		array(
			'pattern'  => 'camel',
			'required' => array( 'heading', 'paragraphs' ),
		)
	);

	$sections = array();

	foreach ( $raw_sections as $section ) {
		$sections[] = array(
			'heading'    => $section['heading'],
			'paragraphs' => cw_split_paragraphs( $section['paragraphs'] ),
			'bullets'    => cw_split_lines( $section['bullets'] ),
		);
	}

	$minutes = (int) cw_meta( $id, 'readingMinutes', '0' );

	return array(
		'id'              => $id,
		'slug'            => $post->post_name,
		'title'           => get_the_title( $post ),
		'permalink'       => get_permalink( $post ),
		'category'        => cw_meta( $id, 'category' ),
		'excerpt'         => cw_meta( $id, 'excerpt' ),
		'metaDescription' => cw_meta( $id, 'metaDescription', cw_meta( $id, 'excerpt' ) ),
		// Repli sur la date de publication WordPress si le champ est vide.
		'publishedDate'   => cw_meta( $id, 'publishedDate', get_the_date( 'Y-m-d', $post ) ),
		'readingMinutes'  => $minutes > 0 ? $minutes : 5,
		'intro'           => cw_meta( $id, 'intro' ),
		'sections'        => $sections,
		'keyTakeaways'    => cw_split_lines( cw_meta( $id, 'keyTakeaways' ) ),
		'relatedSolutions' => cw_collect_numbered(
			$id,
			'related',
			array( 'title', 'href' ),
			3,
			array( 'pattern' => 'camel', 'required' => array( 'title', 'href' ) )
		),
	);
}

/**
 * Tous les articles, mappés, du plus récent au plus ancien.
 *
 * @return array<int, array<string,mixed>>
 */
function cw_get_articles(): array {
	$posts = cw_get_posts( 'article' );
	$out   = array_map( 'cw_get_article', $posts );

	// Tri par date de publication décroissante (champ ACF, pas post_date).
	usort(
		$out,
		static fn( $a, $b ) => strcmp( (string) $b['publishedDate'], (string) $a['publishedDate'] )
	);

	return $out;
}

/**
 * Les N autres articles, hors article courant.
 *
 * @return array<int, array<string,mixed>>
 */
function cw_get_other_articles( int $exclude, int $limit = 2 ): array {
	$out = array();

	foreach ( cw_get_articles() as $article ) {
		if ( $article['id'] === $exclude ) {
			continue;
		}

		$out[] = $article;

		if ( count( $out ) >= $limit ) {
			break;
		}
	}

	return $out;
}

/**
 * Tous les posts publiés d'un CPT, triés comme les listings du site.
 *
 * update_meta_cache() en amont : sans lui, 6 solutions × ~30 champs feraient
 * ~180 requêtes au lieu d'une seule.
 *
 * @param string $post_type CPT.
 * @return WP_Post[]
 */
function cw_get_posts( string $post_type ): array {
	static $cache = array();
	$cache_key = $post_type . ':' . cw_locale();

	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	if ( ! post_type_exists( $post_type ) ) {
		$cache[ $cache_key ] = array();
		return $cache[ $cache_key ];
	}

	$posts = get_posts(
		array(
			'post_type'   => $post_type,
			'post_status' => 'publish',
			'lang' => cw_locale(),
			'suppress_filters' => false,
			'numberposts' => -1,
			// menu_order est renseigné (1..6 / 1..4) et désormais éditable
			// dans l'admin — cf. mu-plugins/cw-cpts.php.
			'orderby'     => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);

	if ( $posts ) {
		update_meta_cache( 'post', wp_list_pluck( $posts, 'ID' ) );
	}

	$cache[ $cache_key ] = $posts;

	return $posts;
}

/**
 * Toutes les solutions, mappées.
 *
 * @return array<int, array<string,mixed>>
 */
function cw_get_solutions(): array {
	return array_map( 'cw_get_solution', cw_get_posts( 'solution' ) );
}

/**
 * Tous les secteurs, mappés.
 *
 * @return array<int, array<string,mixed>>
 */
function cw_get_sectors(): array {
	return array_map( 'cw_get_sector', cw_get_posts( 'secteur' ) );
}

/**
 * Les N autres entrées d'un CPT, hors post courant.
 *
 * @param string $post_type CPT.
 * @param int    $exclude   ID à exclure.
 * @param int    $limit     Nombre d'entrées.
 * @return array<int, array<string,mixed>>
 */
function cw_get_related( string $post_type, int $exclude, int $limit = 3 ): array {
	$mapper = 'secteur' === $post_type ? 'cw_get_sector' : 'cw_get_solution';
	$out    = array();

	foreach ( cw_get_posts( $post_type ) as $post ) {
		if ( $post->ID === $exclude ) {
			continue;
		}

		$out[] = $mapper( $post );

		if ( count( $out ) >= $limit ) {
			break;
		}
	}

	return $out;
}

/**
 * Témoignages publiés
 *
 * @return array<int, array{quote:string, author:string, role:string, company:string}>
 */
function cw_get_testimonials(): array {
	static $cache = array();
	$locale = cw_locale();

	if ( isset( $cache[ $locale ] ) ) {
		return $cache[ $locale ];
	}

	if ( ! post_type_exists( 'temoignage' ) ) {
		$cache[ $locale ] = array();
		return $cache[ $locale ];
	}

	// Polylang filtre par langue courante ; sans lui, seul le français existe.
	$posts = get_posts(
		array(
			'post_type'   => 'temoignage',
			'post_status' => 'publish',
			'lang' => $locale,
			'suppress_filters' => false,
			'numberposts' => -1,
			'orderby'     => 'menu_order date',
			'order'       => 'ASC',
		)
	);

	if ( empty( $posts ) ) {
		$cache[ $locale ] = array();
		return $cache[ $locale ];
	}

	// Une seule requête meta pour tous les posts au lieu d'une par champ.
	update_meta_cache( 'post', wp_list_pluck( $posts, 'ID' ) );

	$out = array();

	foreach ( $posts as $post ) {
		$quote = trim( (string) get_post_meta( $post->ID, 'quote', true ) );

		// Un témoignage sans citation n'a rien à afficher (cf. compact()).
		if ( '' === $quote ) {
			continue;
		}

		$out[] = array(
			'quote'   => $quote,
			'author'  => trim( (string) get_post_meta( $post->ID, 'author', true ) ),
			'role'    => trim( (string) get_post_meta( $post->ID, 'role', true ) ),
			'company' => trim( (string) get_post_meta( $post->ID, 'company', true ) ),
		);
	}

	$cache[ $locale ] = $out;

	return $cache[ $locale ];
}
