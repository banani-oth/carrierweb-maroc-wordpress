<?php
/**
 * Intégration Polylang : langues, contenu lié et URL traduites.
 *
 * Les migrations préservent les traductions et les valeurs déjà publiées.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Polylang est-il actif et initialisé ? */
function cw_polylang_active(): bool {
	return function_exists( 'pll_current_language' ) && function_exists( 'PLL' );
}

/**
 * Langues attendues.
 *
 * `flag` sert uniquement à l'affichage dans l'admin. `term_group` fixe l'ordre
 * du sélecteur de langue.
 */
function cw_polylang_languages(): array {
	return array(
		array(
			'name'       => 'Français',
			'slug'       => 'fr',
			'locale'     => 'fr_FR',
			'rtl'        => 0,
			'term_group' => 1,
			'flag'       => 'fr',
		),
		array(
			'name'       => 'العربية',
			'slug'       => 'ar',
			'locale'     => 'ar',
			'rtl'        => 1,
			'term_group' => 2,
			'flag'       => 'ma',
		),
		array(
			'name'       => 'English',
			'slug'       => 'en',
			'locale'     => 'en_GB',
			'rtl'        => 0,
			'term_group' => 3,
			'flag'       => 'gb',
		),
	);
}

/**
 * Crée les 3 langues si elles n'existent pas.
 *
 * Idempotent : chaque langue n'est ajoutée que si absente. S'exécute sur
 * `init` en priorité tardive, une fois Polylang complètement chargé — PLL()
 * n'est pas disponible plus tôt (ni en CLI, d'où ce passage par le web).
 */
add_action(
	'init',
	function () {
		if ( ! cw_polylang_active() ) {
			return;
		}

		$model   = PLL()->model;
		$created = false;

		foreach ( cw_polylang_languages() as $lang ) {
			// Existence testée sur le terme de taxonomie : stable d'une version
			// de Polylang à l'autre, contrairement aux méthodes du modèle.
			if ( get_term_by( 'slug', $lang['slug'], 'language' ) ) {
				continue;
			}

			/*
			 * L'API a bougé : Polylang ≥ 3.7 expose $model->languages->add(),
			 * les versions antérieures $model->add_language(). En 3.8,
			 * add_language() n'existe QUE sur le modèle admin — donc absent en
			 * front, où ce code s'exécute. D'où le double appel.
			 */
			if ( isset( $model->languages ) && method_exists( $model->languages, 'add' ) ) {
				$added = $model->languages->add( $lang );
			} elseif ( method_exists( $model, 'add_language' ) ) {
				$added = $model->add_language( $lang );
			} else {
				continue;
			}

			if ( ! is_wp_error( $added ) ) {
				$created = true;
			}
		}

		if ( $created ) {
			if ( isset( $model->languages ) && method_exists( $model->languages, 'clean_cache' ) ) {
				$model->languages->clean_cache();
			} elseif ( method_exists( $model, 'clean_languages_cache' ) ) {
				$model->clean_languages_cache();
			}

			flush_rewrite_rules( false );
		}

		delete_option( 'cw_polylang_needs_languages' );
	},
	99
);

/**
 * Locale courante depuis Polylang.
 *
 * Priorité 20 : passe APRÈS le filtre de inc/routing.php, dont la valeur est
 * ainsi remplacée quand Polylang est actif.
 */
add_filter(
	'cw_locale',
	function ( $locale ) {
		if ( ! cw_polylang_active() ) {
			return $locale;
		}

		$current = pll_current_language( 'slug' );

		// pll_current_language() renvoie false dans l'admin hors contexte de langue.
		if ( ! $current && function_exists( 'pll_default_language' ) ) {
			$current = pll_default_language( 'slug' );
		}

		return $current ? $current : $locale;
	},
	20
);

/**
 * Polylang gère déjà les URL : on neutralise notre réécriture maison.
 *
 * Les deux mécanismes se marcheraient dessus — Polylang retire le préfixe de
 * son côté, notre filtre le retirerait une seconde fois.
 */
add_filter(
	'cw_use_custom_routing',
	function ( $use ) {
		return cw_polylang_active() ? false : $use;
	}
);

/**
 * Restreint les requêtes de contenu unique à la langue courante.
 *
 * @param WP_Query $query Requête.
 */
add_filter(
	'request',
	function ( $query_vars ) {
		if ( is_admin() || ! cw_polylang_active() ) {
			return $query_vars;
		}

		/*
		 * Le filtre `request` s'applique AVANT la construction de la requête
		 * principale — assez tôt pour que la contrainte de langue participe à
		 * la résolution du contenu. `pre_get_posts` arrivait trop tard :
		 * Polylang avait déjà arrêté la langue de la requête.
		 *
		 * On ne cible que la résolution d'un contenu unique par slug ; les
		 * archives sont déjà correctement filtrées par Polylang.
		 */
		$targets = array( 'solution', 'secteur', 'article', 'temoignage', 'reglage', 'name', 'pagename' );
		$touches_single = false;

		foreach ( $targets as $key ) {
			if ( ! empty( $query_vars[ $key ] ) ) {
				$touches_single = true;
				break;
			}
		}

		if ( $touches_single && empty( $query_vars['lang'] ) ) {
			$query_vars['lang'] = cw_locale();
		}

		return $query_vars;
	},
	5
);

/**
 * Traduit un ID de post vers la langue courante.
 *
 * Les gabarits appellent get_the_ID() ; en /ar/ Polylang sert déjà le post
 * arabe, donc rien à faire dans le cas courant. Ce raccourci sert aux
 * requêtes explicites (réglages du site, pages fixes), qui ne passent pas par
 * la boucle.
 *
 * @param int         $post_id ID source.
 * @param string|null $locale  Langue cible (courante par défaut).
 */
function cw_translate_post_id( int $post_id, ?string $locale = null ): int {
	if ( ! cw_polylang_active() || ! function_exists( 'pll_get_post' ) ) {
		return $post_id;
	}

	$translated = pll_get_post( $post_id, $locale ?? cw_locale() );

	return $translated ? (int) $translated : $post_id;
}

/**
 * Premier post publié d'un CPT dans la langue courante.
 *
 * Remplace les get_posts() « premier trouvé » utilisés par cw_site_settings()
 * et cw_page_meta() : sans filtre de langue, ils renvoyaient le post français
 * quelle que soit la langue affichée.
 *
 * @param string $post_type CPT.
 * @return int 0 si aucun.
 */
function cw_get_singleton_id( string $post_type ): int {
	if ( ! post_type_exists( $post_type ) ) {
		return 0;
	}

	$args = array(
		'post_type'   => $post_type,
		'post_status' => 'publish',
		'numberposts' => -1,
		'fields'      => 'ids',
		'suppress_filters' => false,
	);

	// pll_get_post() ne s'applique pas ici : on cherche « le » post de la langue.
	if ( cw_polylang_active() ) {
		$args['lang'] = cw_locale();
	}

	$ids = get_posts( $args );

	foreach ( $ids as $id ) {
        if ( ! cw_polylang_active() || ! function_exists( 'pll_get_post_language' ) || pll_get_post_language( (int) $id, 'slug' ) === cw_locale() ) {
            return (int) $id;
        }
    }
    // A non-translatable singleton query may ignore lang: never leak FR copy into AR/EN.
    return 0;
}


/** The fixed templates need real Page translations as well as translated page_* data. */
function cw_fixed_page_source_id( string $slug ): int {
    if ( ! function_exists( 'cw_expected_pages' ) || ! array_key_exists( $slug, cw_expected_pages() ) ) {
        return 0;
    }
    $posts = get_posts( array(
        'post_type' => 'page', 'post_status' => 'publish', 'name' => $slug,
        'numberposts' => -1, 'lang' => '', 'suppress_filters' => false,
        'orderby' => 'ID', 'order' => 'ASC',
    ) );
    foreach ( $posts as $post ) {
        $language = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $post->ID, 'slug' ) : false;
        if ( $post->post_name === $slug && ( ! $language || $language === CW_DEFAULT_LOCALE ) ) {
            return (int) $post->ID;
        }
    }
    return 0;
}

/** Resolve a fixed path to its actual Polylang permalink (unique supported slugs). */
function cw_fixed_page_url( string $path, string $locale ): string {
    if ( ! cw_polylang_active() || ! function_exists( 'pll_get_post' ) ) {
        return '';
    }
    $slug = trim( $path, '/' );
    $source = cw_fixed_page_source_id( $slug );
    if ( ! $source ) {
        return '';
    }
    $id = $locale === CW_DEFAULT_LOCALE ? $source : (int) pll_get_post( $source, $locale );
    if ( ! $id || get_post_status( $id ) !== 'publish' ) {
        return '';
    }
    return (string) get_permalink( $id );
}

/**
 * One-time missing-page bootstrap, after inc/pages.php creates the source wrappers.
 * Existing translations and editorial Pages are never overwritten or republished.
 */
function cw_seed_fixed_page_translations(): void {
    if ( ! cw_polylang_active() || ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'cw_expected_pages' ) ) {
        return;
    }
    if ( get_option( 'cw_fixed_page_translations_version' ) === '1' ) {
        return;
    }
    $lock_key = 'cw_fixed_page_translation_lock';
    $locked_at = (int) get_option( $lock_key );
    if ( $locked_at && time() - $locked_at > 120 ) {
        delete_option( $lock_key );
    }
    if ( ! add_option( $lock_key, time(), '', false ) ) {
        return;
    }
    $complete = true;
    $titles = array(
        'ar' => array( 'pourquoi-carrierweb' => 'لماذا CarrierWeb', 'references' => 'المراجع والخبرة', 'contact' => 'اتصل بنا', 'connexion' => 'تسجيل الدخول', 'inscription' => 'طلب حساب', 'espace-client' => 'فضاء العملاء', 'mentions-legales' => 'الإشعارات القانونية', 'confidentialite' => 'سياسة الخصوصية' ),
        'en' => array( 'pourquoi-carrierweb' => 'Why CarrierWeb', 'references' => 'References and experience', 'contact' => 'Contact', 'connexion' => 'Sign in', 'inscription' => 'Request access', 'espace-client' => 'Client area', 'mentions-legales' => 'Legal notice', 'confidentialite' => 'Privacy policy' ),
    );
    try {
        foreach ( cw_expected_pages() as $slug => $title ) {
            $source = cw_fixed_page_source_id( $slug );
            if ( ! $source ) {
                $complete = false;
                continue;
            }
            if ( ! pll_get_post_language( $source, 'slug' ) ) {
                pll_set_post_language( $source, CW_DEFAULT_LOCALE );
            }
            $group = array( CW_DEFAULT_LOCALE => $source );
            foreach ( array( 'ar', 'en' ) as $locale ) {
                $existing = (int) pll_get_post( $source, $locale );
                if ( $existing ) {
                    $group[ $locale ] = $existing;
                    continue;
                }
                // Reuse a previously bootstrapped orphan after an interrupted request.
                $matches = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1,
                    'meta_key' => '_cw_fixed_source', 'meta_value' => $source,
                    'lang' => '', 'suppress_filters' => false ) );
                foreach ( $matches as $match ) {
                    if ( pll_get_post_language( $match->ID, 'slug' ) === $locale ) {
                        $existing = (int) $match->ID;
                        break;
                    }
                }
                if ( ! $existing ) {
                    $inserted = wp_insert_post( array(
                        'post_type' => 'page', 'post_status' => 'publish',
                        'post_name' => $slug . '-' . $locale, 'post_title' => $titles[ $locale ][ $slug ] ?? $title,
                        'post_content' => '', 'comment_status' => 'closed', 'ping_status' => 'closed',
                        'meta_input' => array( '_wp_page_template' => 'page-' . $slug . '.php', '_cw_fixed_source' => $source, '_cw_fixed_slug' => $slug ),
                    ), true );
                    if ( is_wp_error( $inserted ) || ! $inserted ) {
                        $complete = false;
                        continue;
                    }
                    $existing = (int) $inserted;
                    pll_set_post_language( $existing, $locale );
                }
                $group[ $locale ] = $existing;
            }
            pll_save_post_translations( $group );
        }
        if ( $complete ) {
            update_option( 'cw_fixed_page_translations_version', '1', false );
        }
    } finally {
        delete_option( $lock_key );
    }
}
add_action( 'wp_loaded', 'cw_seed_fixed_page_translations', 30 );

/** Resolve prefixed fixed-page aliases to the linked translation. */
add_filter( 'request', function ( $query_vars ) {
    if ( is_admin() || ! cw_polylang_active() || empty( $query_vars['pagename'] ) || ! is_string( $query_vars['pagename'] ) ) {
        return $query_vars;
    }
    $slug = trim( $query_vars['pagename'], '/' );
    $source = cw_fixed_page_source_id( $slug );
    if ( ! $source ) {
        return $query_vars;
    }
    $locale = $query_vars['lang'] ?? cw_locale();
    if ( ! in_array( $locale, CW_LOCALES, true ) ) {
        return $query_vars;
    }
    $id = $locale === CW_DEFAULT_LOCALE ? $source : (int) pll_get_post( $source, $locale );
    if ( $id && get_post_status( $id ) === 'publish' ) {
        $query_vars['page_id'] = $id;
        $query_vars['lang'] = $locale;
        unset( $query_vars['pagename'], $query_vars['name'] );
    }
    return $query_vars;
}, 8 );

/** Source lookup for detail links: each translated CPT keeps its supported unique slug. */
function cw_detail_source_id( string $post_type, string $slug ): int {
    if ( ! in_array( $post_type, array( 'solution', 'secteur', 'article' ), true ) || '' === $slug ) {
        return 0;
    }
    $posts = get_posts( array( 'post_type' => $post_type, 'name' => $slug, 'post_status' => 'publish',
        'numberposts' => -1, 'lang' => CW_DEFAULT_LOCALE, 'suppress_filters' => false,
        'orderby' => 'ID', 'order' => 'ASC' ) );
    foreach ( $posts as $post ) {
        if ( $post->post_name === $slug && pll_get_post_language( $post->ID, 'slug' ) === CW_DEFAULT_LOCALE ) {
            return (int) $post->ID;
        }
    }
    return 0;
}

/** Resolve detail URLs to the linked WordPress translation. */
function cw_detail_page_url( string $path, string $locale ): string {
    if ( ! cw_polylang_active() || ! function_exists( 'pll_get_post' ) || ! preg_match( '~^/(solutions|secteurs|ressources)/([^/?#]+)/?$~u', $path, $matches ) ) {
        return '';
    }
    $types = array( 'solutions' => 'solution', 'secteurs' => 'secteur', 'ressources' => 'article' );
    $source = cw_detail_source_id( $types[ $matches[1] ], $matches[2] );
    if ( ! $source ) {
        return '';
    }
    $id = $locale === CW_DEFAULT_LOCALE ? $source : (int) pll_get_post( $source, $locale );
    return $id && get_post_status( $id ) === 'publish' ? (string) get_permalink( $id ) : '';
}

/** Old prefixed detail URLs resolve to the translated ID and canonical unique-slug URL. */
add_filter( 'request', function ( $query_vars ) {
    if ( is_admin() || ! cw_polylang_active() ) {
        return $query_vars;
    }
    $locale = $query_vars['lang'] ?? cw_locale();
    if ( ! in_array( $locale, CW_LOCALES, true ) ) {
        return $query_vars;
    }
    foreach ( array( 'solution', 'secteur', 'article' ) as $type ) {
        $slug = $query_vars[ $type ] ?? ( ( $query_vars['post_type'] ?? '' ) === $type ? ( $query_vars['name'] ?? '' ) : '' );
        if ( ! is_string( $slug ) || '' === $slug ) {
            continue;
        }
        $source = cw_detail_source_id( $type, $slug );
        if ( ! $source ) {
            continue;
        }
        $id = $locale === CW_DEFAULT_LOCALE ? $source : (int) pll_get_post( $source, $locale );
        if ( $id && get_post_status( $id ) === 'publish' ) {
            $query_vars['p'] = $id;
            $query_vars['post_type'] = $type;
            $query_vars['lang'] = $locale;
            unset( $query_vars['name'], $query_vars[ $type ] );
        }
        break;
    }
    return $query_vars;
}, 9 );

/**
 * Link existing page-copy singletons once without changing any title, field or status.
 * Ambiguous counts or links to another record are left for an editor to resolve.
 */
function cw_link_fixed_singleton_translations(): void {
    if ( ! cw_polylang_active() || ! function_exists( 'pll_get_post_translations' ) || ! function_exists( 'pll_save_post_translations' ) ) {
        return;
    }
    $linked = get_option( 'cw_singleton_translation_links_v1', array() );
    $linked = is_array( $linked ) ? $linked : array();
    $changed = false;
    foreach ( array( 'page_accueil', 'page_pourquoi', 'page_references', 'page_ressources', 'page_contact' ) as $type ) {
        // After the initial migration, an editor remains free to change translation groups.
        if ( isset( $linked[ $type ] ) || ! post_type_exists( $type ) ) {
            continue;
        }
        $records = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1,
            'lang' => '', 'suppress_filters' => false, 'orderby' => 'ID', 'order' => 'ASC' ) );
        $candidates = array_fill_keys( CW_LOCALES, array() );
        foreach ( $records as $record ) {
            $locale = pll_get_post_language( $record->ID, 'slug' );
            if ( isset( $candidates[ $locale ] ) ) {
                $candidates[ $locale ][] = (int) $record->ID;
            }
        }
        $group = array();
        foreach ( $candidates as $locale => $ids ) {
            if ( count( $ids ) !== 1 ) {
                continue 2;
            }
            $group[ $locale ] = $ids[0];
        }
        foreach ( $group as $id ) {
            foreach ( (array) pll_get_post_translations( $id ) as $locale => $translation ) {
                if ( ! isset( $group[ $locale ] ) || (int) $translation !== $group[ $locale ] ) {
                    continue 3;
                }
            }
        }
        pll_save_post_translations( $group );
        foreach ( $group as $id ) {
            foreach ( $group as $locale => $translation ) {
                if ( (int) pll_get_post( $id, $locale ) !== $translation ) {
                    continue 3;
                }
            }
        }
        $linked[ $type ] = $group;
        $changed = true;
    }
    if ( $changed ) {
        update_option( 'cw_singleton_translation_links_v1', $linked, false );
    }
}
add_action( 'wp_loaded', 'cw_link_fixed_singleton_translations', 35 );
