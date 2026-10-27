<?php
/**
 * Création automatique des pages WordPress nécessaires au thème.
 *
 * Le thème s'appuie sur la hiérarchie `page-{slug}.php` : chaque page ci-dessous
 * a son gabarit dédié, sans aucune assignation à faire dans wp-admin. Encore
 * faut-il que l'objet Page existe — d'où cette amorce, calquée sur le motif
 * idempotent déjà éprouvé dans mu-plugins/cw-reglages.php.
 *
 * ⚠️ NE JAMAIS ajouter ici « solutions », « secteurs » ni « ressources » : ce
 * sont des ARCHIVES de CPT. Une page portant le même slug prendrait le pas sur
 * l'archive et casserait le listing en silence, sans aucune erreur.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Incrémenter pour rejouer l'amorce après ajout d'une page. */
const CW_PAGES_VERSION = '1';

/**
 * Pages attendues : slug => titre.
 *
 * Le contenu reste vide : tout le texte vient des gabarits et des CPT
 * `page_*`. La page WordPress ne sert qu'à porter l'URL.
 */
function cw_expected_pages(): array {
	return array(
		'pourquoi-carrierweb' => 'Pourquoi CarrierWeb',
		'references'          => 'Références',
		'contact'             => 'Contact',
		'connexion'           => 'Connexion',
		'inscription'         => 'Inscription',
		'espace-client'       => 'Espace Client',
		'mentions-legales'    => 'Mentions légales',
		'confidentialite'     => 'Politique de confidentialité',
	);
}

add_action(
	'wp_loaded',
	function () {
		if ( get_option( 'cw_pages_version' ) === CW_PAGES_VERSION ) {
			return;
		}

		$created = false;

		foreach ( cw_expected_pages() as $slug => $title ) {
			// get_page_by_path() couvre aussi les pages en brouillon ou en corbeille.
			$existing = get_posts(
				array(
					'post_type'   => 'page',
					'name'        => $slug,
					'post_status' => 'any',
					'numberposts' => 1,
					'fields'      => 'ids',
				)
			);

			if ( ! empty( $existing ) ) {
				continue;
			}

			$id = wp_insert_post(
				array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'post_title'     => $title,
					'post_name'      => $slug,
					'post_content'   => '',
					// Le contenu ne passe pas par l'éditeur : inutile d'ouvrir
					// les commentaires sur des pages institutionnelles.
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
				)
			);

			if ( $id && ! is_wp_error( $id ) ) {
				$created = true;
			}
		}

		update_option( 'cw_pages_version', CW_PAGES_VERSION, false );

		// De nouvelles pages = de nouvelles règles de réécriture à enregistrer.
		if ( $created ) {
			flush_rewrite_rules( false );
		}
	},
	20
);
