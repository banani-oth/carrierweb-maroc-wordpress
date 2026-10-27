<?php
/**
 * Plugin Name: CarrierWeb — Types de contenu et URL
 * Description: Enregistre les solutions et secteurs, puis configure les archives et leurs URL.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Incrémenter à CHAQUE changement de rewrite/has_archive ci-dessous.
 * Déclenche un flush unique des permaliens (voir cw_maybe_flush_rewrites).
 */
define( 'CW_REWRITE_VERSION', '2' );

/**
 * 1) CPT `solution` et `secteur` — enregistrés en code.
 *
 * Priorité 11 : Custom Post Type UI enregistre à la priorité 10. Tant que ses
 * définitions restent en base, la nôtre doit passer APRÈS pour gagner
 * (register_post_type est un « dernier écrit gagne »). Une fois les deux CPT
 * supprimés dans CPT UI, la priorité n'a plus d'importance — on la laisse à 11,
 * c'est sans effet de bord.
 *
 * `supports` : pas d'`editor` (post_content vide sur les 17 posts, vérifié le
 * 28/07/2026) ; `page-attributes` AJOUTÉ car les listings sont triés par
 * menu_order — les valeurs existent déjà en base (1..6 / 1..4) mais la boîte
 * « Ordre » était masquée dans l'admin, donc non modifiable par l'équipe.
 */
add_action(
	'init',
	function () {
		$common = array(
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'supports'            => array( 'title', 'custom-fields', 'page-attributes', 'thumbnail' ),
			'show_in_graphql'     => true,
		);

		register_post_type(
			'solution',
			$common + array(
				'label'               => 'Solutions',
				'menu_icon'           => 'dashicons-screenoptions',
				'has_archive'         => 'solutions',
				'rewrite'             => array(
					'slug'       => 'solutions',
					'with_front' => false,
				),
				'graphql_single_name' => 'solution',
				'graphql_plural_name' => 'solutions',
			)
		);

		register_post_type(
			'secteur',
			$common + array(
				'label'               => 'Secteurs',
				'menu_icon'           => 'dashicons-category',
				'has_archive'         => 'secteurs',
				'rewrite'             => array(
					'slug'       => 'secteurs',
					'with_front' => false,
				),
				'graphql_single_name' => 'secteur',
				'graphql_plural_name' => 'secteurs',
			)
		);
	},
	11
);

/**
 * 2) Routage des CPT enregistrés dans les AUTRES mu-plugins.
 */
add_filter(
	'register_post_type_args',
	function ( $args, $post_type ) {
		switch ( $post_type ) {

			// Les articles sont publiés sous /ressources.
			case 'article':
				$args['has_archive'] = 'ressources';
				$args['rewrite']     = array(
					'slug'       => 'ressources',
					'with_front' => false,
				);
				// Les listings d'articles sont triés par menu_order eux aussi.
				if ( ! in_array( 'page-attributes', (array) $args['supports'], true ) ) {
					$args['supports'][] = 'page-attributes';
				}
				break;

			/*
			 * `temoignage` et `reglage` sont des SOURCES DE DONNÉES, pas des pages :
			 * ils sont lus par les templates (accueil, références) et n'ont aucun
			 * gabarit propre. Les laisser publiquement interrogeables produisait des
			 * URL vides indexables (/temoignage/xxx). On garde show_ui pour que
			 * l'équipe continue de les éditer dans wp-admin.
			 */
			case 'temoignage':
			case 'reglage':
				$args['public']             = false;
				$args['publicly_queryable'] = false;
				$args['show_ui']            = true;
				$args['show_in_menu']       = true;
				$args['has_archive']        = false;
				$args['rewrite']            = false;
				$args['query_var']          = false;
				break;
		}

		return $args;
	},
	10,
	2
);

/**
 * 3) Flush des permaliens versionné.
 *
 * Sans ça, /solutions renvoie 404 après un déploiement tant que personne n'a
 * ouvert Réglages > Permaliens — le grand classique WordPress. On flushe une
 * seule fois, quand CW_REWRITE_VERSION change (jamais à chaque chargement :
 * flush_rewrite_rules() est très coûteux).
 */
add_action(
	'init',
	function () {
		if ( get_option( 'cw_rewrite_version' ) === CW_REWRITE_VERSION ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( 'cw_rewrite_version', CW_REWRITE_VERSION, false );
	},
	99
);
