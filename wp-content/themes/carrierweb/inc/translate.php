<?php
/**
 * Traduction automatique depuis le français.
 *
 * POURQUOI UN BOUTON, ET PAS UNE TRADUCTION AUTOMATIQUE EN ARRIÈRE-PLAN
 * Les traductions arabe et anglaise déjà en base ont été relues par des
 * humains — arabe standard soigné, noms de marque et sigles techniques laissés
 * en caractères latins. Une traduction machine serait de MOINS BONNE qualité.
 * Elle ne doit donc jamais s'exécuter toute seule ni écraser quoi que ce soit
 * en silence : l'équipe clique, relit, corrige, puis publie.
 *
 * CE QUI N'EST JAMAIS TRADUIT
 * Certains champs ressemblent à du texte mais sont structurels : `icon`
 * (« map-pin »), les `href` (« /solutions/suivi-gps »), les dates, les durées
 * de lecture. Les traduire casserait les icônes et les liens. Voir
 * cw_translate_skips().
 *
 * CONFIGURATION (dans wp-config.php, hors dépôt) :
 *   define( 'CW_TRANSLATE_PROVIDER', 'deepl' );   // ou 'azure'
 *   define( 'CW_TRANSLATE_KEY', '…' );
 *   define( 'CW_TRANSLATE_AZURE_REGION', 'westeurope' ); // azure uniquement
 *
 * DeepL : offre gratuite 500 000 caractères/mois, permanente. Le site entier
 * pèse ~130 000 caractères — la limite ne sera jamais atteinte en usage normal.
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Fournisseur configuré. */
function cw_translate_provider(): string {
	return defined( 'CW_TRANSLATE_PROVIDER' ) ? strtolower( (string) CW_TRANSLATE_PROVIDER ) : 'deepl';
}

/** Une clé d'API est-elle configurée ? */
function cw_translate_configured(): bool {
	return defined( 'CW_TRANSLATE_KEY' ) && '' !== (string) CW_TRANSLATE_KEY;
}

/**
 * Champs à NE PAS traduire.
 *
 * - clés exactes : champs structurels ;
 * - motifs : familles de champs numérotés (icônes, liens).
 *
 * @return array{exact:array<int,string>, patterns:array<int,string>}
 */
function cw_translate_skips(): array {
	return array(
		'exact'    => array(
			'icon',            // slug Lucide : « map-pin »
			'publishedDate',   // date ISO
			'readingMinutes',  // nombre
			'linkedinUrl',
			'contactEmail',
			'contactPhone',
		),
		'patterns' => array(
			'/_\d+_icon$/',    // stat_1_icon, feature_2_icon
			'/_\d+_href$/',    // recsol_1_href
			'/^related\d+Href$/', // related1Href
			'/Href$/',
			'/Url$/',
		),
	);
}

/** Ce champ doit-il être traduit ? */
function cw_translate_should_skip( string $key ): bool {
	$skips = cw_translate_skips();

	if ( in_array( $key, $skips['exact'], true ) ) {
		return true;
	}

	foreach ( $skips['patterns'] as $pattern ) {
		if ( preg_match( $pattern, $key ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Traduit un lot de chaînes.
 *
 * Envoi groupé : une seule requête HTTP pour tous les champs d'un contenu,
 * au lieu d'une par champ.
 *
 * @param array<int,string> $texts  Chaînes source.
 * @param string            $target Locale cible ('ar' | 'en').
 * @return array<int,string>|WP_Error Traductions dans le même ordre.
 */
function cw_translate_batch( array $texts, string $target ) {
	if ( ! cw_translate_configured() ) {
		return new WP_Error( 'no_key', __( 'Aucune clé de traduction configurée (CW_TRANSLATE_KEY dans wp-config.php).', 'carrierweb' ) );
	}

	if ( empty( $texts ) ) {
		return array();
	}

	return 'azure' === cw_translate_provider()
		? cw_translate_azure( $texts, $target )
		: cw_translate_deepl( $texts, $target );
}

/**
 * DeepL.
 *
 * @param array<int,string> $texts  Chaînes.
 * @param string            $target 'ar' | 'en'.
 * @return array<int,string>|WP_Error
 */
function cw_translate_deepl( array $texts, string $target ) {
	$key = (string) CW_TRANSLATE_KEY;

	// Les clés d'offre gratuite se terminent par « :fx » et visent un autre hôte.
	$host = str_ends_with( $key, ':fx' ) ? 'https://api-free.deepl.com' : 'https://api.deepl.com';

	$response = wp_remote_post(
		$host . '/v2/translate',
		array(
			'timeout' => 45,
			'headers' => array(
				'Authorization' => 'DeepL-Auth-Key ' . $key,
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode(
				array(
					'text'        => array_values( $texts ),
					'source_lang' => 'FR',
					'target_lang' => 'en' === $target ? 'EN-GB' : strtoupper( $target ),
					/*
					 * PAS de tag_handling : le contenu est du texte brut, et
					 * certaines valeurs ressemblent à des balises — stat_1_value
					 * vaut « <5m » (précision de localisation). En mode XML,
					 * DeepL y verrait une balise ouvrante et pourrait la
					 * supprimer ou casser la chaîne.
					 */
					'preserve_formatting' => true,
				)
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code ) {
		return new WP_Error(
			'deepl_error',
			sprintf(
				/* translators: 1: HTTP code, 2: message */
				__( 'DeepL a répondu %1$d : %2$s', 'carrierweb' ),
				$code,
				isset( $body['message'] ) ? $body['message'] : wp_remote_retrieve_body( $response )
			)
		);
	}

	if ( empty( $body['translations'] ) ) {
		return new WP_Error( 'deepl_empty', __( 'Réponse DeepL vide.', 'carrierweb' ) );
	}

	return array_map( static fn( $t ) => $t['text'] ?? '', $body['translations'] );
}

/**
 * Microsoft Azure Translator.
 *
 * @param array<int,string> $texts  Chaînes.
 * @param string            $target 'ar' | 'en'.
 * @return array<int,string>|WP_Error
 */
function cw_translate_azure( array $texts, string $target ) {
	$region = defined( 'CW_TRANSLATE_AZURE_REGION' ) ? (string) CW_TRANSLATE_AZURE_REGION : 'global';

	$payload = array_map( static fn( $t ) => array( 'Text' => $t ), array_values( $texts ) );

	$response = wp_remote_post(
		'https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&from=fr&to=' . rawurlencode( $target ),
		array(
			'timeout' => 45,
			'headers' => array(
				'Ocp-Apim-Subscription-Key'    => (string) CW_TRANSLATE_KEY,
				'Ocp-Apim-Subscription-Region' => $region,
				'Content-Type'                 => 'application/json',
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code ) {
		return new WP_Error(
			'azure_error',
			sprintf(
				/* translators: 1: HTTP code, 2: message */
				__( 'Azure a répondu %1$d : %2$s', 'carrierweb' ),
				$code,
				isset( $body['error']['message'] ) ? $body['error']['message'] : wp_remote_retrieve_body( $response )
			)
		);
	}

	return array_map(
		static fn( $item ) => $item['translations'][0]['text'] ?? '',
		(array) $body
	);
}

/**
 * Traduit un contenu depuis sa version française.
 *
 * @param int $post_id Contenu cible (traduction ar/en existante).
 * @return array{filled:int, skipped:int}|WP_Error
 */
function cw_translate_post( int $post_id ) {
	if ( ! function_exists( 'pll_get_post_language' ) ) {
		return new WP_Error( 'no_polylang', __( 'Polylang est requis.', 'carrierweb' ) );
	}

	$target = pll_get_post_language( $post_id );

	if ( ! $target || 'fr' === $target ) {
		return new WP_Error( 'bad_target', __( 'Ce contenu est la version française : rien à traduire.', 'carrierweb' ) );
	}

	$source_id = pll_get_post( $post_id, 'fr' );

	if ( ! $source_id ) {
		return new WP_Error( 'no_source', __( 'Aucune version française liée à ce contenu.', 'carrierweb' ) );
	}

	// ── Rassemble les champs à traduire ──
	$meta    = get_post_meta( $source_id );
	$keys    = array();
	$values  = array();
	$skipped = 0;

	foreach ( $meta as $key => $raw ) {
		if ( '_' === $key[0] ) {
			continue; // références internes ACF
		}

		if ( cw_translate_should_skip( $key ) ) {
			++$skipped;
			continue;
		}

		$value = is_array( $raw ) ? ( $raw[0] ?? '' ) : $raw;
		$value = trim( (string) $value );

		if ( '' === $value || is_numeric( $value ) ) {
			continue;
		}

		$keys[]   = $key;
		$values[] = $value;
	}

	// Le titre aussi.
	$source_title = get_the_title( $source_id );

	if ( '' !== $source_title ) {
		$keys[]   = '__title__';
		$values[] = $source_title;
	}

	if ( empty( $values ) ) {
		return new WP_Error( 'nothing', __( 'Aucun champ à traduire.', 'carrierweb' ) );
	}

	$translated = cw_translate_batch( $values, $target );

	if ( is_wp_error( $translated ) ) {
		return $translated;
	}

	// ── Écriture ──
	$filled = 0;

	foreach ( $keys as $i => $key ) {
		$value = $translated[ $i ] ?? '';

		if ( '' === $value ) {
			continue;
		}

		if ( '__title__' === $key ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => $value,
				)
			);
		} else {
			update_post_meta( $post_id, $key, $value );
		}

		++$filled;
	}

	// Copie telle quelle des champs structurels : icônes et liens doivent
	// rester identiques au français, sinon les visuels et la navigation cassent.
	foreach ( $meta as $key => $raw ) {
		if ( '_' === $key[0] || ! cw_translate_should_skip( $key ) ) {
			continue;
		}

		$value = is_array( $raw ) ? ( $raw[0] ?? '' ) : $raw;
		update_post_meta( $post_id, $key, $value );
	}

	return array(
		'filled'  => $filled,
		'skipped' => $skipped,
	);
}

/* ── Interface d'administration ──────────────────────────────────────── */

add_action(
	'add_meta_boxes',
	function ( $post_type, $post ) {
		$translatable = array( 'solution', 'secteur', 'article', 'temoignage', 'reglage' );

		if ( ! in_array( $post_type, $translatable, true ) || ! function_exists( 'pll_get_post_language' ) ) {
			return;
		}

		$lang = pll_get_post_language( $post->ID );

		// Le bouton n'a de sens que sur une traduction, jamais sur le français.
		if ( ! $lang || 'fr' === $lang ) {
			return;
		}

		add_meta_box(
			'cw-translate',
			__( 'Traduction automatique', 'carrierweb' ),
			'cw_translate_metabox',
			$post_type,
			'side',
			'high'
		);
	},
	10,
	2
);

/**
 * Contenu de la boîte.
 *
 * @param WP_Post $post Contenu courant.
 */
function cw_translate_metabox( $post ): void {
	$lang      = pll_get_post_language( $post->ID );
	$source_id = function_exists( 'pll_get_post' ) ? pll_get_post( $post->ID, 'fr' ) : 0;
	$names     = cw_locale_names();

	echo '<div class="cw-translate-box">';

	if ( ! cw_translate_configured() ) {
		echo '<p style="margin:0 0 8px">' . esc_html__( 'Aucune clé d\'API configurée.', 'carrierweb' ) . '</p>';
		echo '<p style="margin:0;color:#666;font-size:12px">'
			. esc_html__( 'Ajouter dans wp-config.php :', 'carrierweb' )
			. '<br><code>define( \'CW_TRANSLATE_KEY\', \'…\' );</code></p>';
		echo '</div>';
		return;
	}

	if ( ! $source_id ) {
		echo '<p style="margin:0">' . esc_html__( 'Aucune version française liée.', 'carrierweb' ) . '</p></div>';
		return;
	}

	printf(
		'<p style="margin:0 0 10px">%s</p>',
		esc_html(
			sprintf(
				/* translators: %s: langue cible */
				__( 'Remplit les champs de cette fiche à partir du français, via %1$s, vers : %2$s.', 'carrierweb' ),
				'deepl' === cw_translate_provider() ? 'DeepL' : 'Azure',
				$names[ $lang ] ?? $lang
			)
		)
	);

	echo '<p style="margin:0 0 10px;color:#a00;font-size:12px">'
		. esc_html__( 'Écrase les champs de cette langue. Relisez avant de publier : une traduction automatique reste moins bonne qu\'une relecture humaine.', 'carrierweb' )
		. '</p>';

	wp_nonce_field( 'cw_translate_' . $post->ID, 'cw_translate_nonce' );

	printf(
		'<button type="button" class="button button-secondary" id="cw-translate-btn" data-post="%d">%s</button>',
		(int) $post->ID,
		esc_html__( 'Traduire depuis le français', 'carrierweb' )
	);

	echo '<span id="cw-translate-status" style="display:block;margin-top:8px;font-size:12px"></span>';
	echo '</div>';

	// Script inline : quelques lignes, uniquement sur cet écran.
	?>
	<script>
	(function () {
		var btn = document.getElementById('cw-translate-btn');
		var out = document.getElementById('cw-translate-status');

		if (!btn) return;

		btn.addEventListener('click', function () {
			if (!window.confirm(<?php echo wp_json_encode( __( 'Remplacer les champs de cette langue par une traduction automatique du français ?', 'carrierweb' ) ); ?>)) return;

			btn.disabled = true;
			out.textContent = <?php echo wp_json_encode( __( 'Traduction en cours…', 'carrierweb' ) ); ?>;

			var data = new FormData();
			data.append('action', 'cw_translate_post');
			data.append('post_id', btn.dataset.post);
			data.append('nonce', document.getElementById('cw_translate_nonce').value);

			fetch(ajaxurl, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (res.success) {
						out.style.color = '#046b2d';
						out.textContent = res.data.message;
						// Recharge pour afficher les champs remplis.
						setTimeout(function () { window.location.reload(); }, 1200);
					} else {
						btn.disabled = false;
						out.style.color = '#a00';
						out.textContent = res.data && res.data.message ? res.data.message : 'Erreur.';
					}
				})
				.catch(function () {
					btn.disabled = false;
					out.style.color = '#a00';
					out.textContent = <?php echo wp_json_encode( __( 'Échec réseau.', 'carrierweb' ) ); ?>;
				});
		});
	})();
	</script>
	<?php
}

/** Traitement AJAX. */
add_action(
	'wp_ajax_cw_translate_post',
	function () {
		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		$nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! $post_id || ! wp_verify_nonce( $nonce, 'cw_translate_' . $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Jeton de sécurité invalide.', 'carrierweb' ) ), 403 );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Droits insuffisants.', 'carrierweb' ) ), 403 );
		}

		$result = cw_translate_post( $post_id );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		wp_send_json_success(
			array(
				'message' => sprintf(
					/* translators: 1: champs remplis, 2: champs conservés */
					__( '%1$d champ(s) traduit(s), %2$d conservé(s) tels quels (icônes, liens).', 'carrierweb' ),
					$result['filled'],
					$result['skipped']
				),
			)
		);
	}
);
