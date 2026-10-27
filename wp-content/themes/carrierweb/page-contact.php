<?php
/**
 * Page contact — /contact
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_phone   = cw_setting( 'contactPhone', '', '+212 (0)5 22 36 19 88' );
$cw_email   = cw_setting( 'contactEmail', '', 'info@carrierweb.ma' );
$cw_address = cw_setting( 'contactAddress', 'contact.addressValue' );
$cw_tel     = 'tel:' . preg_replace( '/[^\d+]/', '', $cw_phone );

// État renvoyé par la redirection du handler (parcours sans JavaScript).
$cw_status = isset( $_GET['cw'] ) && is_string( $_GET['cw'] ) ? sanitize_key( wp_unslash( $_GET['cw'] ) ) : '';
$cw_contact_messages = cw_contact_messages();
$cw_sent   = 'sent' === $cw_status;
$cw_error  = '' !== $cw_status && ! $cw_sent;

$cw_fleet_options = (array) cw_t( 'contact.fleetOptions', array() );
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-[110px] pb-20">

	<?php
	get_template_part(
		'template-parts/blocks/breadcrumb-hero',
		null,
		array(
			'breadcrumb' => cw_page_text( 'page_contact', 'breadcrumb', 'contact.breadcrumb' ),
			'label'      => cw_page_text( 'page_contact', 'label', 'contact.label' ),
			'title_pre'  => cw_page_text( 'page_contact', 'h1_pre', 'contact.h1Pre' ),
			'title_hl'   => cw_page_text( 'page_contact', 'h1_hl', 'contact.h1Hl' ),
			'desc'       => cw_page_text( 'page_contact', 'desc', 'contact.desc' ),
		)
	);
	?>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">

		<?php // ── Colonne gauche : coordonnées ── ?>
		<div class="lg:col-span-5 space-y-6">
			<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 sm:p-8">
				<h2 class="text-lg font-bold text-cw-fg mb-6 font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.infoTitle' ); ?></h2>

				<div class="space-y-6">
					<div class="flex items-start gap-4">
						<div class="w-10 h-10 rounded-lg bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light flex-shrink-0">
							<?php cw_icon( 'map-pin', 18 ); ?>
						</div>
						<div>
							<h3 class="text-cw-fg font-semibold text-xs font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.addressLabel' ); ?></h3>
							<p class="text-cw-gray-400 text-xs mt-1"><?php echo esc_html( $cw_address ); ?></p>
						</div>
					</div>

					<div class="flex items-start gap-4">
						<div class="w-10 h-10 rounded-lg bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light flex-shrink-0">
							<?php cw_icon( 'phone', 18 ); ?>
						</div>
						<div>
							<h3 class="text-cw-fg font-semibold text-xs font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.phoneLabel' ); ?></h3>
							<p class="text-cw-gray-400 text-xs mt-1" dir="ltr">
								<a href="<?php echo esc_url( $cw_tel ); ?>" class="hover:text-cw-fg transition-colors"><?php echo esc_html( $cw_phone ); ?></a>
							</p>
						</div>
					</div>

					<div class="flex items-start gap-4">
						<div class="w-10 h-10 rounded-lg bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light flex-shrink-0">
							<?php cw_icon( 'mail', 18 ); ?>
						</div>
						<div>
							<h3 class="text-cw-fg font-semibold text-xs font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.emailLabel' ); ?></h3>
							<p class="text-cw-gray-400 text-xs mt-1" dir="ltr">
								<a href="<?php echo esc_url( 'mailto:' . $cw_email ); ?>" class="hover:text-cw-fg transition-colors"><?php echo esc_html( $cw_email ); ?></a>
							</p>
						</div>
					</div>

					<div class="flex items-start gap-4">
						<div class="w-10 h-10 rounded-lg bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light flex-shrink-0">
							<?php cw_icon( 'clock', 18 ); ?>
						</div>
						<div>
							<h3 class="text-cw-fg font-semibold text-xs font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.availabilityLabel' ); ?></h3>
							<p class="text-cw-gray-400 text-xs mt-1 leading-relaxed">
								<?php cw_e( 'contact.availability1' ); ?><br>
								<?php cw_e( 'contact.availability2' ); ?>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 text-center space-y-3">
				<h2 class="text-cw-fg font-semibold text-sm font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.clientBoxTitle' ); ?></h2>
				<p class="text-cw-gray-400 text-xs leading-relaxed"><?php cw_e( 'contact.clientBoxDesc' ); ?></p>
				<a
					href="<?php echo esc_url( cw_editor_link( 'client_portal', CW_CLIENT_PORTAL_URL ) ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="inline-flex items-center gap-1.5 text-xs font-semibold text-cw-red-light hover:underline pt-1"
				>
					<span><?php cw_e( 'contact.clientBoxCta' ); ?></span>
					<span aria-hidden="true">→</span>
				</a>
			</div>

			<div class="bg-cw-green/5 border border-cw-green/20 rounded-2xl p-6 text-center space-y-3">
				<div class="w-10 h-10 rounded-full bg-cw-green/10 flex items-center justify-center text-cw-green mx-auto">
					<?php cw_icon( 'message-circle', 20 ); ?>
				</div>
				<h2 class="text-cw-green font-semibold text-sm font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.waTitle' ); ?></h2>
				<p class="text-cw-gray-500 text-xs leading-relaxed"><?php cw_e( 'contact.waDesc' ); ?></p>
				<a
					href="<?php echo esc_url( cw_editor_link( 'whatsapp', 'https://wa.me/212522361988' ) ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-cw-green/20 hover:bg-cw-green/30 text-cw-green border border-cw-green/30 rounded-xl text-xs font-bold transition-all w-full"
				>
					<span><?php cw_e( 'contact.waCta' ); ?></span>
				</a>
			</div>
		</div>

		<?php // ── Colonne droite : formulaire ── ?>
		<div class="lg:col-span-7">
			<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 sm:p-8">

				<?php // Panneau de succès : affiché après redirection, ou par le JS. ?>
				<div class="text-center py-12 space-y-4<?php echo $cw_sent ? '' : ' hidden'; ?>" data-cw-contact-success>
					<div class="w-16 h-16 rounded-full bg-cw-red/10 flex items-center justify-center text-cw-red-light mx-auto">
						<?php cw_icon( 'check-circle-2', 36 ); ?>
					</div>
					<h2 class="text-xl font-bold text-cw-fg font-[family-name:var(--font-jakarta)]"><?php echo esc_html( $cw_contact_messages['sent_title'] ); ?></h2>
					<p class="text-cw-gray-300 text-xs max-w-sm mx-auto leading-relaxed">
						<?php echo esc_html( $cw_contact_messages['sent_body'] ); ?>
						<a href="<?php echo esc_url( 'mailto:' . $cw_email ); ?>" class="text-cw-red-light hover:underline"><?php echo esc_html( $cw_email ); ?></a>
						<?php cw_e( 'contact.successOr' ); ?> <span dir="ltr"><?php echo esc_html( $cw_phone ); ?></span>.
					</p>
					<a href="<?php echo esc_url( cw_url( '/contact' ) ); ?>" class="cta-outline text-xs mt-4" data-cw-contact-reset>
						<?php cw_e( 'contact.sendAnother' ); ?>
					</a>
				</div>

				<form
					method="post"
					action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
					class="space-y-5<?php echo $cw_sent ? ' hidden' : ''; ?>"
					data-cw-contact-form
					data-cw-contact-messages="<?php echo esc_attr( wp_json_encode( $cw_contact_messages ) ); ?>"
				>
					<h2 class="text-lg font-bold text-cw-fg mb-2 font-[family-name:var(--font-jakarta)]"><?php cw_e( 'contact.formTitle' ); ?></h2>

					<p class="bg-cw-red/10 border border-cw-red/30 text-cw-red-light text-xs rounded-xl px-4 py-3<?php echo $cw_error ? '' : ' hidden'; ?>" role="alert" tabindex="-1" data-cw-contact-error><?php echo $cw_error ? esc_html( $cw_contact_messages[ $cw_status ] ?? $cw_contact_messages['unknown'] ) : ''; ?></p>

					<input type="hidden" name="action" value="<?php echo esc_attr( CW_CONTACT_ACTION ); ?>">
					<?php wp_nonce_field( CW_CONTACT_ACTION, 'cw_nonce' ); ?>
					<input type="hidden" name="cw_locale" value="<?php echo esc_attr( cw_locale() ); ?>">
					<input type="hidden" name="cw_ts" value="<?php echo esc_attr( (string) time() ); ?>">

					<?php
					/*
					 * Pot de miel : masqué visuellement, pas via `hidden` — un robot
					 * qui remplit les champs cachés le remplirait quand même. Sorti
					 * de l'ordre de tabulation et de l'arbre d'accessibilité.
					 */
					?>
					<div class="absolute w-px h-px overflow-hidden -m-px p-0 border-0" aria-hidden="true">
						<label for="cw_website">Ne pas remplir</label>
						<input type="text" id="cw_website" name="cw_website" tabindex="-1" autocomplete="off">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div class="space-y-1.5">
							<label for="nom" class="text-[11px] text-cw-gray-400 font-medium"><?php cw_e( 'contact.nameLabel' ); ?></label>
							<input id="nom" name="nom" type="text" maxlength="150" required class="form-input" placeholder="<?php echo esc_attr( (string) cw_t( 'contact.namePh' ) ); ?>" autocomplete="name">
						</div>
						<div class="space-y-1.5">
							<label for="entreprise" class="text-[11px] text-cw-gray-400 font-medium"><?php cw_e( 'contact.companyLabel' ); ?></label>
							<input id="entreprise" name="entreprise" type="text" maxlength="180" class="form-input" placeholder="<?php echo esc_attr( (string) cw_t( 'contact.companyPh' ) ); ?>" autocomplete="organization">
						</div>
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div class="space-y-1.5">
							<label for="email" class="text-[11px] text-cw-gray-400 font-medium"><?php cw_e( 'contact.emailFieldLabel' ); ?></label>
							<input id="email" name="email" type="email" maxlength="254" required class="form-input" placeholder="<?php echo esc_attr( (string) cw_t( 'contact.emailPh' ) ); ?>" dir="ltr" autocomplete="email">
						</div>
						<div class="space-y-1.5">
							<label for="telephone" class="text-[11px] text-cw-gray-400 font-medium"><?php cw_e( 'contact.phoneFieldLabel' ); ?></label>
							<input id="telephone" name="telephone" type="tel" maxlength="60" class="form-input" placeholder="<?php echo esc_attr( (string) cw_t( 'contact.phonePh' ) ); ?>" dir="ltr" autocomplete="tel">
						</div>
					</div>

					<div class="space-y-1.5">
						<label for="tailleFlotte" class="text-[11px] text-cw-gray-400 font-medium"><?php cw_e( 'contact.fleetLabel' ); ?></label>
						<select id="tailleFlotte" name="tailleFlotte" class="form-input">
							<?php foreach ( $cw_fleet_options as $cw_opt ) : ?>
								<option value="<?php echo esc_attr( (string) $cw_opt ); ?>"><?php echo esc_html( (string) $cw_opt ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="space-y-1.5">
						<label for="message" class="text-[11px] text-cw-gray-400 font-medium"><?php cw_e( 'contact.messageLabel' ); ?></label>
						<textarea id="message" name="message" required maxlength="5000" rows="5" class="form-input" placeholder="<?php echo esc_attr( (string) cw_t( 'contact.messagePh' ) ); ?>"></textarea>
					</div>

					<button
						type="submit"
						class="cta-primary w-full justify-center text-sm py-4"
						data-label-idle="<?php echo esc_attr( (string) cw_t( 'contact.submit' ) ); ?>"
						data-label-sending="<?php echo esc_attr( (string) cw_t( 'contact.sending' ) ); ?>"
					>
						<span data-cw-submit-label><?php cw_e( 'contact.submit' ); ?></span>
						<?php cw_icon( 'send', 16 ); ?>
					</button>
				</form>
			</div>
		</div>
	</section>

	<?php // Carte de l'agence de Casablanca. loading=lazy : hors écran au chargement. ?>
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
		<div class="rounded-2xl overflow-hidden border border-white/[0.04] h-[380px]">
			<iframe
				title="<?php echo esc_attr( cw_t( 'contact.addressLabel' ) . ' — CarrierWeb Maroc' ); ?>"
				src="<?php echo esc_url( cw_editor_link( 'map', 'https://www.google.com/maps?q=39%20rue%20Normandie%2C%20Casablanca%2C%20Maroc&output=embed' ) ); ?>"
				width="100%"
				height="380"
				style="border:0"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
			></iframe>
		</div>
	</section>

</main>

<?php
get_footer();
