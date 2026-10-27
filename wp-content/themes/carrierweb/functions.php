<?php
/**
 * CarrierWeb Maroc — bootstrap du thème.
 *
 * Ce fichier reste volontairement mince : chaque module de inc/ s'enregistre
 * lui-même (add_action / add_filter au niveau du fichier). Pour ajouter une
 * fonctionnalité, créer un module et l'ajouter à $modules ci-dessous — ne rien
 * écrire directement ici.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Version du thème — sert de cache-buster pour les assets (voir inc/enqueue.php). */
define( 'CW_VERSION', '0.3.2' );

/** Chemin absolu du thème, sans slash final. */
define( 'CW_DIR', get_stylesheet_directory() );

/** URL du thème, sans slash final. */
define( 'CW_URI', get_stylesheet_directory_uri() );

/**
 * Ordre significatif :
 *  - i18n avant tout module qui traduit (les gabarits appellent cw_t()) ;
 *  - numbered avant data (data s'appuie sur cw_collect_numbered()).
 */
$cw_modules = array(
	'i18n',     // locale, URLs localisées, cw_t() — AVANT tout module qui traduit
	'routing',  // détection /ar//en/ maison — neutralisée si Polylang est actif
	'polylang', // intégration Polylang (langues, locale, traduction des posts)
	'icons',    // cw_icon() — SVG Lucide inline
	'setup',    // supports du thème, menus, tailles d'images
	'enqueue',      // CSS/JS/polices + purge des styles de blocs inutiles
	'editor',       // Textes, listes et médias modifiables dans WordPress.
	'editor-compat', // Reprise des valeurs des champs historiques.
	'settings',     // cw_site_settings() — CPT reglage
	'fixed-pages',  // cw_page_text() / cw_page_list() — CPT page_*
	'data',         // cw_get_testimonials(), solutions, secteurs, articles
	'nav',          // structure de navigation partagée header/footer
	'editor-legal',
	'pages',        // amorce des pages WordPress portant les URL du thème
	'contact-form', // traitement du formulaire de contact (admin-post)
	'seo',          // titres, meta, canonical, hreflang, OG/Twitter
	'schema',       // JSON-LD — dépend de seo (CW_SITE_URL)
	'sitemap',      // /sitemap.xml avec alternates + robots.txt
	'translate',    // bouton « Traduire depuis le français » (DeepL / Azure)
);

foreach ( $cw_modules as $cw_module ) {
	$cw_path = CW_DIR . '/inc/' . $cw_module . '.php';

	if ( is_readable( $cw_path ) ) {
		require_once $cw_path;
		continue;
	}

	// Un module manquant est une erreur de déploiement, pas une page blanche :
	// on le signale dans l'admin plutôt que de laisser le thème échouer en silence.
	add_action(
		'admin_notices',
		function () use ( $cw_module ) {
			printf(
				'<div class="notice notice-error"><p><strong>CarrierWeb :</strong> module <code>inc/%s.php</code> introuvable.</p></div>',
				esc_html( $cw_module )
			);
		}
	);
}

unset( $cw_modules, $cw_module, $cw_path );
