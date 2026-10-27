<?php
/**
 * Chargement des assets du thème + dégraissage des styles du core.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sous-ensembles « latin » d'Inter et Plus Jakarta Sans.
 */
const CW_PRELOAD_FONTS = array(
	'83afe278b6a6bb3c-s.p.2bn3s6zvc0dyp.woff2', // Inter, latin
	'fba5a26ea33df6a3-s.p.18rizl4rsrl42.woff2', // Plus Jakarta Sans, latin
);

/**
 * Version d'asset basée sur le mtime du fichier.
 *
 * En dev, CW_VERSION ne change pas à chaque rebuild CSS : sans ça le navigateur
 * sert un app.css périmé et on croit à tort que le style ne s'applique pas.
 *
 * @param string $rel Chemin relatif à la racine du thème.
 * @return string
 */
function cw_asset_version( string $rel ): string {
	$path = CW_DIR . '/' . ltrim( $rel, '/' );

	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	return CW_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		// ── Feuille de style compilée ─────────────────────────────────────
		// style.css n'est JAMAIS chargé : il ne contient que l'en-tête de thème.
		$css = 'assets/dist/app.css';

		if ( file_exists( CW_DIR . '/' . $css ) ) {
			wp_enqueue_style( 'carrierweb', CW_URI . '/' . $css, array(), cw_asset_version( $css ) );
		} else {
			// Erreur de déploiement classique : thème installé sans `npm run build`.
			add_action(
				'wp_body_open',
				function () {
					if ( ! current_user_can( 'manage_options' ) ) {
						return;
					}
					echo '<p style="background:#bf2026;color:#fff;padding:12px;margin:0;font:14px system-ui;text-align:center">'
						. 'CarrierWeb : <code>assets/dist/app.css</code> absent — lancer <code>npm install &amp;&amp; npm run build</code> dans le thème.'
						. '</p>';
				}
			);
		}

		// ── JavaScript ────────────────────────────────────────────────────
		$js = 'assets/dist/app.js';

		if ( file_exists( CW_DIR . '/' . $js ) ) {
			wp_enqueue_script(
				'carrierweb',
				CW_URI . '/' . $js,
				array(),
				cw_asset_version( $js ),
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);


			wp_localize_script(
				'carrierweb',
				'CW',
				array(
					'ajaxUrl' => admin_url( 'admin-post.php' ),
					'locale'  => determine_locale(),
					'isRtl'   => is_rtl(),
				)
			);
		}

		// ── Commentaires : inutilisés (aucun CPT ne supporte 'comments') ──
		wp_dequeue_script( 'comment-reply' );
	}
);

/**
 * Préchargement des polices latines.
 *
 * Priorité 2 : après le <meta charset> du core, mais avant la feuille de style
 * — un preload déclaré après le CSS qui l'utilise n'apporte plus rien.
 */
add_action(
	'wp_head',
	function () {
		foreach ( CW_PRELOAD_FONTS as $file ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( CW_URI . '/assets/fonts/' . $file )
			);
		}
	},
	2
);

/**
 * Purge des styles Gutenberg sur le FRONT uniquement.
 *
 * Le design n'utilise aucun bloc : post_content est vide sur les 17 posts,
 * tout le contenu vient des champs ACF rendus par nos gabarits. Ces feuilles
 * ne servent donc à rien et pèsent ~90 Ko cumulés.
 *
 * On ne touche jamais à l'admin (is_admin()) : l'éditeur en a besoin.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_admin() ) {
			return;
		}

		foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles', 'wc-blocks-style' ) as $handle ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
	},
	100
);

/**
 * Emoji du core : script + CSS injectés sur chaque page pour remplacer des
 * emoji que le design n'utilise pas. ~10 Ko de JS pour rien.
 */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}
);
