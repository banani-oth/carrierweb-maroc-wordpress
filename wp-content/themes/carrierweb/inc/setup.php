<?php
/**
 * Supports du thème et réglages de base.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		/** Titre du document géré par WordPress. */
		add_theme_support( 'title-tag' );

		if ( function_exists( 'cw_polylang_active' ) && cw_polylang_active() ) {
			register_nav_menus( array( 'cw_primary' => 'Navigation principale', 'cw_footer' => 'Pied de page' ) );
		} else {
			$menus = array();
			foreach ( CW_LOCALES as $locale ) {
				$menus[ 'cw_primary_' . $locale ] = 'Navigation principale — ' . strtoupper( $locale );
				$menus[ 'cw_footer_' . $locale ] = 'Pied de page — ' . strtoupper( $locale );
			}
			register_nav_menus( $menus );
		}

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Balisage HTML5 : sans ça, WordPress produit encore du markup XHTML
		 * daté sur les formulaires de recherche/commentaires.
		 */
		add_theme_support(
			'html5',
			array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
		);

		/* Les pages utilisent des gabarits PHP et des champs ACF ; les styles de blocs ne sont pas chargés. */

		/* Les dates passent par cw_format_date(). La langue de l’administration reste un réglage WordPress. */
		load_theme_textdomain( 'carrierweb', CW_DIR . '/languages' );
	}
);

/**
 * Le header flottant fait ~82px déplié : le contenu doit démarrer dessous.
 */
add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'cw-site';


		$classes[] = 'min-h-screen';
		$classes[] = 'flex';
		$classes[] = 'flex-col';
		$classes[] = 'bg-cw-black';
		$classes[] = 'text-cw-gray-300';
		$classes[] = 'font-sans';

		if ( 'rtl' === cw_dir() ) {
			$classes[] = 'cw-rtl';
		}

		return $classes;
	}
);
