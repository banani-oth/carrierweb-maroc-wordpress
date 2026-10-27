<?php
/**
 * Formulaire de contact via admin-post.php, avec envoi natif et réponse JSON.
 *
 * Validation serveur, nonce, champ anti-robot, délai minimal et limite par IP.
 * Les réglages SMTP sont lus dans wp-config.php.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Action admin-post. */
const CW_CONTACT_ACTION = 'cw_contact';

/** Délai minimal entre l'affichage du formulaire et son envoi (secondes). */
const CW_CONTACT_MIN_SECONDS = 3;

/** Nombre d'envois autorisés par IP et par heure. */
const CW_CONTACT_RATE_LIMIT = 5;

/**
 * Destinataire des demandes.
 *
 * Priorité : constante wp-config > champ « E-mail » des réglages du site.
 */
function cw_contact_recipient(): string {
	if ( defined( 'CW_CONTACT_TO' ) && CW_CONTACT_TO ) {
		return (string) CW_CONTACT_TO;
	}

	$email = function_exists( 'cw_setting' ) ? cw_setting( 'contactEmail' ) : '';
	if ( ! $email && function_exists( 'cw_site_settings' ) ) { $email = cw_site_settings()['contactEmail'] ?? ''; }
	if ( $email ) { return $email; }

	return (string) get_option( 'admin_email' );
}

/**
 * Champs attendus : nom du champ => libellé dans l'e-mail.
 */
function cw_contact_fields(): array {
	return array(
		'nom'          => 'Nom complet',
		'entreprise'   => 'Entreprise',
		'email'        => 'E-mail',
		'telephone'    => 'Téléphone',
		'tailleFlotte' => 'Taille de flotte',
	);
}

/**
 * Traitement de l'envoi.
 *
 * Renvoie un code de résultat plutôt que d'afficher quoi que ce soit : la
 * même logique sert à la redirection (sans JS) et à la réponse JSON (avec JS).
 *
 * @return array{ok:bool, code:string}
 */
/** Validated language survives the unprefixed admin-post endpoint. */
function cw_contact_locale(): string {
    $value = $_POST['cw_locale'] ?? '';
    return is_string( $value ) && in_array( $value, array( 'fr', 'ar', 'en' ), true ) ? $value : 'fr';
}

/** User-facing feedback kept with the handler so JS and native POST agree. */
function cw_contact_messages( ?string $locale = null ): array {
    $locale = $locale ?? cw_locale();
    $messages = array(
        'fr' => array(
            'sent_title' => 'Demande acceptée par le service e-mail',
            'sent_body' => "Le service e-mail configuré a accepté votre demande. Cela ne confirme pas sa réception dans la boîte de l'équipe. Pour toute urgence, contactez-nous directement :",
            'bad_nonce' => 'Le formulaire a expiré. Rechargez la page puis réessayez.',
            'stale_form' => 'Le formulaire a expiré. Rechargez la page puis réessayez.',
            'too_fast' => 'Veuillez patienter quelques secondes puis renvoyer votre demande.',
            'rate_limited' => 'Trop de demandes envoyées. Merci de réessayer dans une heure ou de nous contacter directement.',
            'invalid_fields' => 'Vérifiez le nom, une adresse e-mail valide et le message. Respectez les limites de longueur indiquées.',
            'send_failed' => "Le service e-mail n'a pas accepté la demande. Réessayez ou contactez-nous directement.",
            'unknown' => "La réponse du serveur n'a pas pu être confirmée. Votre demande a peut-être été transmise. Aucun nouvel envoi automatique n'a eu lieu ; vérifiez avant de réessayer ou contactez-nous directement.",
        ),
        'en' => array(
            'sent_title' => 'Request accepted by the email service',
            'sent_body' => "The configured email service accepted your request. This does not confirm arrival in the team's inbox. For urgent enquiries, contact us directly:",
            'bad_nonce' => 'This form has expired. Reload the page and try again.',
            'stale_form' => 'This form has expired. Reload the page and try again.',
            'too_fast' => 'Please wait a few seconds, then submit your request again.',
            'rate_limited' => 'Too many requests. Try again in one hour or contact us directly.',
            'invalid_fields' => 'Check your name, a valid email address and message. Keep within the field length limits.',
            'send_failed' => 'The email service did not accept the request. Retry or contact us directly.',
            'unknown' => 'The server response could not be confirmed. Your request may have been submitted. It was not sent again automatically; check before retrying or contact us directly.',
        ),
        'ar' => array(
            'sent_title' => 'قبلت خدمة البريد الإلكتروني طلبك',
            'sent_body' => 'قبلت خدمة البريد الإلكتروني المُعدّة طلبك. هذا لا يؤكد وصوله إلى صندوق بريد الفريق. للحالات العاجلة تواصل معنا مباشرة:',
            'bad_nonce' => 'انتهت صلاحية النموذج. أعد تحميل الصفحة ثم حاول مرة أخرى.',
            'stale_form' => 'انتهت صلاحية النموذج. أعد تحميل الصفحة ثم حاول مرة أخرى.',
            'too_fast' => 'يرجى الانتظار بضع ثوانٍ ثم إرسال طلبك مرة أخرى.',
            'rate_limited' => 'طلبات كثيرة. حاول بعد ساعة أو تواصل معنا مباشرة.',
            'invalid_fields' => 'تحقق من الاسم وعنوان البريد الإلكتروني والرسالة، والتزم بالحد الأقصى لطول الحقول.',
            'send_failed' => 'لم تقبل خدمة البريد الإلكتروني الطلب. حاول مرة أخرى أو تواصل معنا مباشرة.',
            'unknown' => 'تعذر تأكيد رد الخادم. ربما تم إرسال طلبك. لم تتم إعادة إرساله تلقائيًا؛ تحقق قبل المحاولة مجددًا أو تواصل معنا مباشرة.',
        ),
    );
    return $messages[ $locale ] ?? $messages['fr'];
}

function cw_contact_process(): array {
    $nonce = $_POST['cw_nonce'] ?? '';
    if ( ! is_string( $nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $nonce ) ), CW_CONTACT_ACTION ) ) {
        return array( 'ok' => false, 'code' => 'bad_nonce' );
    }

    $honeypot = $_POST['cw_website'] ?? '';
    if ( ! is_string( $honeypot ) || '' !== trim( wp_unslash( $honeypot ) ) ) {
        return array( 'ok' => false, 'code' => 'invalid_fields' );
    }

    $raw_ts = $_POST['cw_ts'] ?? '';
    if ( ! is_scalar( $raw_ts ) || ! ctype_digit( (string) $raw_ts ) ) {
        return array( 'ok' => false, 'code' => 'stale_form' );
    }
    $elapsed = time() - (int) $raw_ts;
    if ( $elapsed < -60 || $elapsed > DAY_IN_SECONDS ) {
        return array( 'ok' => false, 'code' => 'stale_form' );
    }
    if ( $elapsed < CW_CONTACT_MIN_SECONDS ) {
        // Fast autofill must receive a retry prompt, never a false success.
        return array( 'ok' => false, 'code' => 'too_fast' );
    }

    $limits = array( 'nom' => 150, 'entreprise' => 180, 'email' => 254, 'telephone' => 60, 'tailleFlotte' => 100, 'message' => 5000 );
    $data = array();
    foreach ( $limits as $field => $limit ) {
        $raw = $_POST[ $field ] ?? '';
        if ( ! is_string( $raw ) || strlen( $raw ) > $limit * 4 ) {
            return array( 'ok' => false, 'code' => 'invalid_fields' );
        }
        $value = trim( wp_unslash( $raw ) );
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
        if ( $length > $limit ) {
            return array( 'ok' => false, 'code' => 'invalid_fields' );
        }
        $data[ $field ] = 'message' === $field ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
    }
    if ( '' === $data['nom'] || '' === $data['email'] || '' === $data['message'] || ! is_email( $data['email'] ) ) {
        return array( 'ok' => false, 'code' => 'invalid_fields' );
    }
    $recipient = cw_contact_recipient();
    if ( ! is_email( $recipient ) ) {
        return array( 'ok' => false, 'code' => 'send_failed' );
    }

    $ip = isset( $_SERVER['REMOTE_ADDR'] ) && is_string( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    $key = 'cw_contact_' . hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) );
    $hits = (int) get_transient( $key );
    if ( $hits >= CW_CONTACT_RATE_LIMIT ) {
        return array( 'ok' => false, 'code' => 'rate_limited' );
    }
    // Bound validated transport attempts, including a misconfigured mail service.
    set_transient( $key, $hits + 1, HOUR_IN_SECONDS );

    $rows = '';
    foreach ( cw_contact_fields() as $field => $label ) {
        $value = '' !== $data[ $field ] ? $data[ $field ] : '—';
        $rows .= sprintf( '<tr><td style="padding:4px 12px 4px 0;color:#666">%s</td><td style="padding:4px 0"><strong>%s</strong></td></tr>', esc_html( $label ), esc_html( $value ) );
    }
    $html = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111">'
        . '<h2>Nouvelle demande — CarrierWeb Maroc</h2><table>' . $rows . '</table>'
        . '<p>Message :</p><p style="white-space:pre-wrap">' . esc_html( $data['message'] ) . '</p></div>';
    $subject = 'Demande de démo — ' . ( '' !== $data['entreprise'] ? $data['entreprise'] : $data['nom'] );
    // Only the validated email belongs in a mail header; display names remain in the body.
    $headers = array( 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $data['email'] );
    $accepted = wp_mail( $recipient, $subject, $html, $headers );
    return array( 'ok' => (bool) $accepted, 'code' => $accepted ? 'sent' : 'send_failed' );
}

/**
 * Point d'entrée admin-post.
 *
 * Répond en JSON si la requête vient de fetch (JS actif), sinon redirige vers
 * /contact avec un paramètre d'état (fonctionnement sans JS).
 */
function cw_contact_handle(): void {
	$result = cw_contact_process();
	$messages = cw_contact_messages( cw_contact_locale() );
	$result['message'] = $messages[ $result['ok'] ? 'sent_body' : $result['code'] ] ?? $messages['unknown'];

	$is_ajax = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] )
		&& 'xmlhttprequest' === strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) );

	if ( $is_ajax ) {
		wp_send_json( $result, $result['ok'] ? 200 : 400 );
	}

	// POST-Redirect-Get : évite le renvoi du formulaire au rafraîchissement.
	wp_safe_redirect(
		add_query_arg( 'cw', $result['ok'] ? 'sent' : $result['code'], cw_url( '/contact', cw_contact_locale() ) ),
		303
	);
	exit;
}

add_action( 'admin_post_' . CW_CONTACT_ACTION, 'cw_contact_handle' );
add_action( 'admin_post_nopriv_' . CW_CONTACT_ACTION, 'cw_contact_handle' );

/**
 * Un SMTP est-il configuré ?
 */
function cw_contact_smtp_configured(): bool {
	return defined( 'CW_SMTP_HOST' ) && defined( 'CW_SMTP_USER' ) && defined( 'CW_SMTP_PASS' );
}

/**
 * Branche PHPMailer sur le SMTP déclaré dans wp-config.php.
 *
 * Sans ces constantes, WordPress utilise la fonction mail() de PHP — ce qui,
 * sous LocalWP, aboutit dans Mailpit : parfait pour tester sans le moindre
 * identifiant réel.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $mailer Instance PHPMailer.
 */
add_action(
	'phpmailer_init',
	function ( $mailer ) {
		if ( ! cw_contact_smtp_configured() ) {
			return;
		}

		$mailer->isSMTP();
		$mailer->Host       = CW_SMTP_HOST;
		$mailer->SMTPAuth   = true;
		$mailer->Username   = CW_SMTP_USER;
		$mailer->Password   = CW_SMTP_PASS;
		$mailer->Port       = defined( 'CW_SMTP_PORT' ) ? (int) CW_SMTP_PORT : 587;
		$mailer->SMTPSecure = 465 === $mailer->Port ? 'ssl' : 'tls';

		if ( defined( 'CW_SMTP_FROM' ) ) {
			$mailer->setFrom( CW_SMTP_FROM, 'CarrierWeb Maroc — Site' );
		}
	}
);

/**
 * Expéditeur par défaut : sans ça WordPress envoie depuis
 * wordpress@<domaine>, que la plupart des filtres anti-spam rejettent.
 */
add_filter(
	'wp_mail_from_name',
	function () {
		return 'CarrierWeb Maroc — Site';
	}
);
