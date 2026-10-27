<?php
/** Navigation WordPress et cartes des contenus publiés. @package CarrierWeb */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Accepte aussi les URL externes, les ancres et les liens de contact. */
function cw_nav_url( string $href ): string {
    if ( '' === $href || '#' === $href[0] || preg_match( '~^[a-z][a-z0-9+.-]*:~i', $href ) || str_starts_with( $href, '//' ) ) { return $href; }
    return cw_url( $href );
}
function cw_nav_active( string $href ): bool {
    $url = cw_nav_url( $href );
    if ( wp_parse_url( $url, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { return false; }
    $path = (string) wp_parse_url( $url, PHP_URL_PATH );
    $base = (string) wp_parse_url( home_url(), PHP_URL_PATH );
    if ( $base && '/' !== $base && str_starts_with( $path, $base ) ) { $path = substr( $path, strlen( $base ) ); }
    return cw_is_active( cw_strip_locale( '/' . ltrim( untrailingslashit( $path ), '/' ) ) );
}

/** Une fiche modifiée met également à jour sa carte sur l'accueil. */
function cw_nav_solutions(): array {
    return array_map( static fn( $item ) => array(
        'href' => $item['permalink'], 'icon' => $item['icon'], 'label' => $item['title'],
        'desc' => $item['shortDescription'] ?: $item['tagline'],
    ), cw_get_solutions() );
}
function cw_nav_sectors(): array {
    return array_map( static fn( $item ) => array(
        'href' => $item['permalink'], 'icon' => $item['icon'], 'label' => $item['title'],
        'desc' => $item['listingDescription'] ?: $item['tagline'],
    ), cw_get_sectors() );
}
function cw_default_nav_items(): array {
    $out = array();
    foreach ( array( 'home' => '/', 'solutions' => '/solutions', 'sectors' => '/secteurs', 'why' => '/pourquoi-carrierweb', 'references' => '/references', 'resources' => '/ressources', 'contact' => '/contact' ) as $key => $href ) {
        $item = array( 'key' => $key, 'label' => (string) cw_t( 'header.nav.' . $key ), 'href' => $href );
        if ( 'solutions' === $key ) { $item['mega'] = true; $item['children'] = cw_nav_solutions(); }
        elseif ( 'sectors' === $key ) { $item['children'] = cw_nav_sectors(); }
        $out[] = $item;
    }
    return $out;
}

/** Polylang conserve les emplacements par langue dans ses propres options. */
function cw_menu_location_id( string $area, string $locale ): int {
    if ( function_exists( 'cw_polylang_active' ) && cw_polylang_active() ) {
        $menus = PLL()->options->get( 'nav_menus' );
        return (int) ( $menus[ get_option( 'stylesheet' ) ][ 'cw_' . $area ][ $locale ] ?? 0 );
    }
    $locations = get_nav_menu_locations();
    return (int) ( $locations[ 'cw_' . $area . '_' . $locale ] ?? 0 );
}

/** Collapse identical siblings without losing their submenu entries. */
function cw_unique_menu_items( array $items, array &$duplicates = array() ): array {
    $duplicates = array();
    do {
        $seen = array();
        $changed = false;
        foreach ( $items as $id => $item ) {
            $label = preg_replace( '/\s+/u', ' ', trim( (string) $item['label'] ) );
            $signature = json_encode( array( (int) $item['parent'], $label, rtrim( cw_nav_url( $item['href'] ), '/' ), $item['target'] ?? '' ) );
            if ( ! isset( $seen[$signature] ) ) { $seen[$signature] = $id; continue; }
            $keep = $seen[$signature];
            $duplicates[$id] = $keep;
            $items[$keep]['mega'] = ! empty( $items[$keep]['mega'] ) || ! empty( $item['mega'] );
            if ( empty( $items[$keep]['desc'] ) ) { $items[$keep]['desc'] = $item['desc'] ?? ''; }
            unset( $items[$id] );
            $changed = true;
        }
        foreach ( $items as &$item ) {
            while ( isset( $duplicates[$item['parent']] ) ) { $item['parent'] = $duplicates[$item['parent']]; }
        }
        unset( $item );
    } while ( $changed );
    return $items;
}

/** Un menu enregistré vide reste vide. Aucun lien supprimé n'est recréé. */
function cw_wordpress_menu( string $area ): ?array {
    $id = cw_menu_location_id( $area, cw_locale() );
    if ( ! $id ) { return get_option( 'cw_menus_seeded_v1' ) ? array() : null; }
    $items = wp_get_nav_menu_items( $id, array( 'post_status' => 'publish' ) );
    if ( ! $items ) { return array(); }
    $mapped = array();
    foreach ( $items as $item ) {
        if ( 'post_type' === $item->type ) {
            $post = get_post( $item->object_id );
            if ( ! $post || 'publish' !== $post->post_status ) { continue; }
        }
        $mapped[ $item->ID ] = array(
            'key' => 'menu-' . $item->ID, 'label' => $item->title, 'href' => $item->url,
            'icon' => 'post_type' === $item->type ? cw_meta( (int) $item->object_id, 'icon', 'arrow-right' ) : 'arrow-right',
            'desc' => $item->description, 'target' => '_blank' === $item->target ? '_blank' : '',
            'children' => array(), 'parent' => (int) $item->menu_item_parent,
            'mega' => in_array( 'cw-mega', (array) $item->classes, true ),
        );
        if ( ! $mapped[ $item->ID ]['desc'] && 'solution' === $item->object ) {
            $mapped[ $item->ID ]['desc'] = cw_meta( (int) $item->object_id, 'shortDescription' );
        }
    }
    $mapped = cw_unique_menu_items( $mapped );
    $out = array();
    foreach ( $mapped as $id => $item ) {
        if ( $item['parent'] && isset( $mapped[ $item['parent'] ] ) ) { continue; }
        $item['children'] = cw_menu_children( $id, $mapped );
        $item['mega'] = $item['mega'] && ! empty( $item['children'] );
        $out[] = $item;
    }
    return $out;
}
/** Les niveaux supplémentaires sont affichés dans le panneau du parent. */
function cw_menu_children( int $parent, array $items, int $depth = 0 ): array {
    if ( $depth > 10 ) { return array(); }
    $out = array();
    foreach ( $items as $id => $item ) {
        if ( $parent !== $item['parent'] ) { continue; }
        $out[] = $item;
        $out = array_merge( $out, cw_menu_children( $id, $items, $depth + 1 ) );
    }
    return $out;
}
function cw_nav_items(): array {
    $menu = cw_wordpress_menu( 'primary' );
    return null === $menu ? cw_default_nav_items() : $menu;
}
function cw_footer_nav_items(): array {
    $menu = cw_wordpress_menu( 'footer' );
    if ( null === $menu ) {
        return array_filter( cw_default_nav_items(), static fn( $item ) => ! in_array( $item['key'], array( 'home', 'solutions' ), true ) );
    }
    $links = array();
    foreach ( $menu as $item ) {
        $links[] = $item;
        foreach ( $item['children'] as $child ) { $links[] = $child; }
    }
    return $links;
}

/** Installation initiale : les modifications suivantes appartiennent à l'équipe. */
add_action( 'init', function () {
    if ( get_option( 'cw_menus_seeded_v1' ) || ! function_exists( 'wp_create_nav_menu' ) ) { return; }
    // The database lock serializes simultaneous first-page requests.
    global $wpdb;
    $lock_key = 'cw_menu_seed_lock';
    $previous_lock = (int) get_option( $lock_key );
    if ( $previous_lock && $previous_lock < time() - 300 ) {
        $removed = $wpdb->delete( $wpdb->options, array( 'option_name' => $lock_key, 'option_value' => (string) $previous_lock ), array( '%s', '%s' ) );
        if ( $removed ) { wp_cache_delete( $lock_key, 'options' ); }
    }
    $lock_value = (string) time();
    $acquired = $wpdb->query( $wpdb->prepare(
        "INSERT IGNORE INTO `$wpdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s)",
        $lock_key, $lock_value, 'off'
    ) );
    if ( 1 !== $acquired ) { return; }
    wp_cache_delete( $lock_key, 'options' );
    wp_cache_delete( 'notoptions', 'options' );
    try {
        $polylang = function_exists( 'cw_polylang_active' ) && cw_polylang_active();
        $locations = get_nav_menu_locations();
        $language_menus = $polylang ? PLL()->options->get( 'nav_menus' ) : array();
        $stylesheet = get_option( 'stylesheet' );
        $complete = true;
        foreach ( CW_LOCALES as $locale ) {
            $dict = (array) require CW_DIR . '/inc/dictionaries/' . $locale . '.php';
            foreach ( array( 'primary', 'footer' ) as $area ) {
                $location = 'cw_' . $area . '_' . $locale;
                if ( cw_menu_location_id( $area, $locale ) ) { continue; }
                $name = 'CarrierWeb — ' . ( 'primary' === $area ? 'Navigation' : 'Pied de page' ) . ' — ' . strtoupper( $locale );
                $existing = wp_get_nav_menu_object( $name );
                $id = $existing ? $existing->term_id : wp_create_nav_menu( $name );
                if ( is_wp_error( $id ) ) { $complete = false; continue; }
                if ( $polylang ) {
                    $language_menus[ $stylesheet ][ 'cw_' . $area ][ $locale ] = (int) $id;
                    if ( CW_DEFAULT_LOCALE === $locale ) { $locations[ 'cw_' . $area ] = (int) $id; }
                } else {
                    $locations[ $location ] = (int) $id;
                }
                if ( $existing ) { continue; }
                foreach ( array( 'home' => '/', 'solutions' => '/solutions', 'sectors' => '/secteurs', 'why' => '/pourquoi-carrierweb', 'references' => '/references', 'resources' => '/ressources', 'contact' => '/contact' ) as $key => $path ) {
                    if ( 'footer' === $area && in_array( $key, array( 'home', 'solutions' ), true ) ) { continue; }
                    $parent = wp_update_nav_menu_item( (int) $id, 0, array(
                        'menu-item-title' => $dict['header']['nav'][ $key ] ?? $key,
                        'menu-item-url' => cw_url( $path, $locale ), 'menu-item-type' => 'custom',
                        'menu-item-status' => 'publish', 'menu-item-classes' => 'solutions' === $key ? 'cw-mega' : '',
                    ) );
                    if ( 'primary' !== $area || is_wp_error( $parent ) || ! in_array( $key, array( 'solutions', 'sectors' ), true ) ) { continue; }
                    $type = 'solutions' === $key ? 'solution' : 'secteur';
                    $posts = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1, 'lang' => $locale, 'suppress_filters' => false, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ) ) );
                    foreach ( $posts as $post ) {
                        if ( function_exists( 'pll_get_post_language' ) && pll_get_post_language( $post->ID, 'slug' ) !== $locale ) { continue; }
                        wp_update_nav_menu_item( (int) $id, 0, array(
                            'menu-item-object-id' => $post->ID, 'menu-item-object' => $type,
                            'menu-item-type' => 'post_type', 'menu-item-parent-id' => (int) $parent, 'menu-item-status' => 'publish',
                        ) );
                    }
                }
            }
        }
        if ( $polylang ) {
            PLL()->options->set( 'nav_menus', $language_menus );
            PLL()->options->save();
        }
        set_theme_mod( 'nav_menu_locations', $locations );
        if ( $complete ) { update_option( 'cw_menus_seeded_v1', 1, false ); }
    } finally {
        $wpdb->delete( $wpdb->options, array( 'option_name' => $lock_key, 'option_value' => $lock_value ), array( '%s', '%s' ) );
        wp_cache_delete( $lock_key, 'options' );
    }
}, 99 );
