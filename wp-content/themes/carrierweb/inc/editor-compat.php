<?php
/** Existing ACF values supply the editor's initial copy. */
if ( ! defined( 'ABSPATH' ) ) exit;

function cw_editor_legacy_meta( string $type, string $locale ): array {
    $ids = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'lang' => $locale, 'suppress_filters' => false ) );
    $id = 0;
    foreach ( $ids as $candidate ) {
        if ( ! function_exists( 'pll_get_post_language' ) || pll_get_post_language( (int) $candidate, 'slug' ) === $locale ) { $id = (int) $candidate; break; }
    }
    if ( ! $id ) return array();
    $raw = get_post_meta( $id );
    $french = array();
    if ( 'fr' !== $locale && function_exists( 'pll_get_post' ) ) {
        $fr_id = pll_get_post( $id, 'fr' );
        if ( $fr_id ) $french = get_post_meta( $fr_id );
    }
    $out = array();
    foreach ( $raw as $key => $value ) {
        if ( str_starts_with( $key, '_' ) ) continue;
        $text = (string) ( $value[0] ?? '' );
        if ( '' === trim( $text ) || ( isset( $french[$key][0] ) && trim( $text ) === trim( (string) $french[$key][0] ) ) ) continue;
        $out[$key] = $text;
    }
    return $out;
}

function cw_editor_set_default( array &$dict, string $path, $value ): void {
    $node =& $dict;
    foreach ( explode( '.', $path ) as $part ) {
        if ( ! isset( $node[$part] ) || ! is_array( $node[$part] ) ) $node[$part] = array();
        $node =& $node[$part];
    }
    $node = $value;
}

add_filter( 'cw_editor_defaults', function( array $dict, string $locale ): array {
    $meta = array();
    foreach ( array( 'page_accueil', 'page_contact', 'page_pourquoi', 'page_references', 'page_ressources', 'reglage' ) as $type ) $meta[$type] = cw_editor_legacy_meta( $type, $locale );
    $fields = array(
        array( 'page_accueil', 'cta_button', 'cta.button' ),
        array( 'page_accueil', 'cta_desc', 'cta.desc' ),
        array( 'page_accueil', 'cta_label', 'cta.label' ),
        array( 'page_accueil', 'cta_title_hl', 'cta.titleHl' ),
        array( 'page_accueil', 'cta_title_pre', 'cta.titlePre' ),
        array( 'page_accueil', 'faq_desc', 'homeFaq.desc' ),
        array( 'page_accueil', 'faq_label', 'homeFaq.label' ),
        array( 'page_accueil', 'faq_title_hl', 'homeFaq.titleHl' ),
        array( 'page_accueil', 'faq_title_pre', 'homeFaq.titlePre' ),
        array( 'page_accueil', 'partners_line', 'partners.line' ),
        array( 'page_accueil', 'sectors_desc', 'sectorsHome.desc' ),
        array( 'page_accueil', 'sectors_label', 'sectorsHome.label' ),
        array( 'page_accueil', 'sectors_title_hl', 'sectorsHome.titleHl' ),
        array( 'page_accueil', 'sectors_title_pre', 'sectorsHome.titlePre' ),
        array( 'page_accueil', 'services_desc', 'services.desc' ),
        array( 'page_accueil', 'services_label', 'services.label' ),
        array( 'page_accueil', 'services_title_hl', 'services.titleHl' ),
        array( 'page_accueil', 'services_title_pre', 'services.titlePre' ),
        array( 'page_accueil', 'why_label', 'why.label' ),
        array( 'page_accueil', 'why_paragraph', 'why.paragraph' ),
        array( 'page_accueil', 'why_title_hl', 'why.titleHl' ),
        array( 'page_accueil', 'why_title_pre', 'why.titlePre' ),
        array( 'page_contact', 'breadcrumb', 'contact.breadcrumb' ),
        array( 'page_contact', 'desc', 'contact.desc' ),
        array( 'page_contact', 'h1_hl', 'contact.h1Hl' ),
        array( 'page_contact', 'h1_pre', 'contact.h1Pre' ),
        array( 'page_contact', 'label', 'contact.label' ),
        array( 'page_pourquoi', 'h1', 'whyPage.h1' ),
        array( 'page_pourquoi', 'label', 'whyPage.label' ),
        array( 'page_pourquoi', 'perf_p1', 'whyPage.perfP1' ),
        array( 'page_pourquoi', 'perf_p2', 'whyPage.perfP2' ),
        array( 'page_pourquoi', 'perf_title', 'whyPage.perfTitle' ),
        array( 'page_pourquoi', 'side_title', 'whyPage.sideTitle' ),
        array( 'page_pourquoi', 'specs_desc', 'whyPage.specsDesc' ),
        array( 'page_pourquoi', 'specs_title', 'whyPage.specsTitle' ),
        array( 'page_pourquoi', 'tagline', 'whyPage.tagline' ),
        array( 'page_references', 'h1', 'referencesPage.h1' ),
        array( 'page_references', 'label', 'referencesPage.label' ),
        array( 'page_references', 'presence_p1', 'referencesPage.presenceP1' ),
        array( 'page_references', 'presence_p2', 'referencesPage.presenceP2' ),
        array( 'page_references', 'presence_title', 'referencesPage.presenceTitle' ),
        array( 'page_references', 'refs_desc', 'referencesPage.refsDesc' ),
        array( 'page_references', 'refs_title', 'referencesPage.refsTitle' ),
        array( 'page_references', 'side_title', 'referencesPage.sideTitle' ),
        array( 'page_references', 'tagline', 'referencesPage.tagline' ),
        array( 'page_ressources', 'breadcrumb', 'resources.breadcrumb' ),
        array( 'page_ressources', 'desc', 'resources.desc' ),
        array( 'page_ressources', 'h1_hl', 'resources.h1Hl' ),
        array( 'page_ressources', 'h1_pre', 'resources.h1Pre' ),
        array( 'page_ressources', 'label', 'resources.label' ),
        array( 'page_ressources', 'newsletter_cta', 'resources.newsletterCta' ),
        array( 'page_ressources', 'newsletter_desc', 'resources.newsletterDesc' ),
        array( 'page_ressources', 'newsletter_title', 'resources.newsletterTitle' ),
    );
    foreach ( $fields as [$type, $key, $path] ) {
        if ( isset( $meta[$type][$key] ) ) cw_editor_set_default( $dict, $path, $meta[$type][$key] );
    }
    $settings = array( 'footerBlurb' => 'footer.blurb', 'heroTitleLine1' => 'hero.titleA', 'heroTitleHighlight' => 'hero.titleBhl', 'heroTitleLine3' => 'hero.titleC', 'heroSubtitle' => 'hero.subtitle' );
    foreach ( $settings as $key => $path ) {
        if ( isset( $meta['reglage'][$key] ) ) cw_editor_set_default( $dict, $path, $meta['reglage'][$key] );
    }
    $dict['site'] = array(
        'contactPhone' => $meta['reglage']['contactPhone'] ?? '+212 (0)5 22 36 19 88',
        'contactEmail' => $meta['reglage']['contactEmail'] ?? 'info@carrierweb.ma',
        'contactAddress' => $meta['reglage']['contactAddress'] ?? ( $dict['contact']['addressValue'] ?? '' ),
    );
    $lists = array(
        array( 'page_accueil', 'why_feature', 3, 'why.features', array( 'title'=>'title', 'desc'=>'description' ) ),
        array( 'page_accueil', 'why_metric', 3, 'why.metrics', array( 'val'=>'val', 'label'=>'label', 'desc'=>'desc' ) ),
        array( 'page_accueil', 'faq', 4, 'homeFaq.items', array( 'q'=>'q', 'a'=>'a' ) ),
        array( 'page_pourquoi', 'spec', 4, 'whyPage.specs', array( 'title'=>'title', 'desc'=>'desc' ) ),
        array( 'page_pourquoi', 'side', 3, 'whyPage.sideBlocks', array( 'title'=>'title', 'desc'=>'desc' ) ),
        array( 'page_references', 'metric', 3, 'referencesPage.metrics', array( 'val'=>'val', 'label'=>'label', 'desc'=>'desc' ) ),
        array( 'page_references', 'side', 4, 'referencesPage.sideBlocks', array( 'title'=>'title', 'desc'=>'desc' ) ),
    );
    foreach ( $lists as [$type, $prefix, $count, $path, $keys] ) {
        $parts = explode( '.', $path );
        $list = $dict[$parts[0]][$parts[1]] ?? array();
        for ( $i=1; $i<=$count; $i++ ) foreach ( $keys as $key=>$dest ) {
            if ( isset( $meta[$type][$prefix.'_'.$i.'_'.$key] ) ) $list[$i-1][$dest] = $meta[$type][$prefix.'_'.$i.'_'.$key];
        }
        cw_editor_set_default( $dict, $path, $list );
    }
    foreach ( array( 'page_pourquoi'=>'whyPage', 'page_references'=>'referencesPage' ) as $type=>$section ) {
        foreach ( $dict[$section]['checks'] ?? array() as $i=>$value ) {
            if ( isset( $meta[$type]['check_'.($i+1)] ) ) $dict[$section]['checks'][$i] = $meta[$type]['check_'.($i+1)];
        }
    }
    $dict['dashboard']['kpiValues'] = array( '48/50', '2', 'OK' );
    $dict['dashboard']['previewUrl'] = 'https://portal.carrierweb.ma';
    return $dict;
}, 10, 2 );

/** The new content screen replaces the historical page-copy menus. */
add_action( 'admin_menu', function() {
    foreach ( array( 'reglage', 'page_accueil', 'page_pourquoi', 'page_references', 'page_ressources', 'page_contact' ) as $type ) {
        remove_menu_page( 'edit.php?post_type=' . $type );
    }
}, 100 );
