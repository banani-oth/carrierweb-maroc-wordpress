<?php
/** Editable legal documents, initialized once from the supplied French copy. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function cw_legal_page_key( $post ): string {
    if ( ! $post || 'page' !== $post->post_type ) { return ''; }
    $template = (string) get_post_meta( $post->ID, '_wp_page_template', true );
    foreach ( array( 'mentions-legales', 'confidentialite' ) as $key ) {
        if ( 'page-' . $key . '.php' === $template || preg_match( '/^' . preg_quote( $key, '/' ) . '(?:-(?:fr|en|ar))?$/', $post->post_name ) ) { return $key; }
    }
    return '';
}

function cw_legal_default_content( string $key ): string {
    $documents = require CW_DIR . '/inc/legal-data.php';
    $document = $documents[ $key ] ?? array();
    $blocks = array();
    foreach ( $document['sections'] ?? array() as $section ) {
        $blocks[] = '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $section['title'] ) . '</h2><!-- /wp:heading -->';
        $blocks[] = '<!-- wp:paragraph --><p>' . esc_html( $section['body'] ) . '</p><!-- /wp:paragraph -->';
    }
    return implode( "\n\n", $blocks );
}

function cw_seed_legal_editor_content(): void {
    if ( get_option( 'cw_legal_editor_seeded_v1' ) ) { return; }
    $pages = get_posts( array(
        'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'pending', 'private' ),
        'numberposts' => -1, 'lang' => '', 'suppress_filters' => true,
    ) );
    $found = false;
    $complete = true;
    foreach ( $pages as $page ) {
        $key = cw_legal_page_key( $page );
        if ( '' === $key ) { continue; }
        $found = true;
        if ( '' !== trim( (string) $page->post_content ) || '1' === (string) get_post_meta( $page->ID, '_cw_legal_content_saved', true ) ) { continue; }
        $content = cw_legal_default_content( $key );
        if ( '' === $content ) { $complete = false; continue; }
        $GLOBALS['cw_legal_seeding'] = true;
        try {
            $result = wp_update_post( wp_slash( array( 'ID' => $page->ID, 'post_content' => $content ) ), true );
        } finally {
            unset( $GLOBALS['cw_legal_seeding'] );
        }
        if ( ! $result || is_wp_error( $result ) ) { $complete = false; continue; }
        update_post_meta( $page->ID, '_cw_legal_content_saved', '1' );
        update_post_meta( $page->ID, '_cw_legal_seed_language', 'fr' );
        update_post_meta( $page->ID, '_cw_legal_seed_hash', hash( 'sha256', $content ) );
    }
    if ( $found && $complete ) { update_option( 'cw_legal_editor_seeded_v1', 1, false ); }
}
// Runs after the ordinary page wrappers and their Polylang translations exist.
add_action( 'wp_loaded', 'cw_seed_legal_editor_content', 40 );

function cw_mark_legal_editor_save( int $post_id, $post, bool $update ): void {
    if ( ! $update || ! empty( $GLOBALS['cw_legal_seeding'] ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) { return; }
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || '' === cw_legal_page_key( $post ) ) { return; }
    if ( ! ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) || ! current_user_can( 'edit_post', $post_id ) ) { return; }
    update_post_meta( $post_id, '_cw_legal_content_saved', '1' );
    $seed_hash = (string) get_post_meta( $post_id, '_cw_legal_seed_hash', true );
    if ( '' !== $seed_hash && ! hash_equals( $seed_hash, hash( 'sha256', (string) $post->post_content ) ) ) {
        delete_post_meta( $post_id, '_cw_legal_seed_language' );
        delete_post_meta( $post_id, '_cw_legal_seed_hash' );
    }
}
add_action( 'save_post_page', 'cw_mark_legal_editor_save', 10, 3 );
