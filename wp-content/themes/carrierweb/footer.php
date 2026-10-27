<?php
/**
 * Pied de page
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_blurb    = cw_setting( 'footerBlurb', 'footer.blurb' );
$cw_address  = cw_setting( 'contactAddress', '', '39 rue Normandie, 20000 Casablanca, Maroc' );
$cw_phone    = cw_setting( 'contactPhone', '', '+212 (0)5 22 36 19 88' );
$cw_email    = cw_setting( 'contactEmail', '', 'info@carrierweb.ma' );
$cw_linkedin = cw_setting( 'linkedinUrl', '', 'https://www.linkedin.com/company/carrierweb' );

// tel: n'accepte que chiffres et +.
$cw_tel_href = 'tel:' . preg_replace( '/[^\d+]/', '', $cw_phone );

$cw_solutions = cw_nav_solutions();
?>
<footer class="relative bg-cw-black-deep border-t border-white/[0.04] py-16 text-cw-gray-400 text-sm" role="contentinfo">
	<div class="max-w-7xl mx-auto px-4 sm:px-6">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

			<?php // Colonne 1 — marque et coordonnées. ?>
			<div class="space-y-4">
				<a
					href="<?php echo esc_url( cw_url( '/' ) ); ?>"
					aria-label="<?php echo esc_attr( (string) cw_t( 'header.logoAria' ) ); ?>"
					class="inline-flex items-center gap-2"
				>
					<span class="flex items-center bg-white rounded-lg px-2.5 py-2">
						<img
							src="<?php echo esc_url( cw_media_url( 'logo', CW_URI . '/assets/img/logo.webp' ) ); ?>"
							alt="<?php echo esc_attr( cw_media_alt( 'logo', 'CarrierWeb' ) ); ?>"
							width="560"
							height="54"
							loading="lazy"
							decoding="async"
							class="h-[14px] w-auto"
						>
					</span>
					<span class="inline-flex items-center rounded-md bg-cw-red px-1.5 py-[3px] text-[9px] font-bold tracking-[0.18em] text-white leading-none font-[family-name:var(--font-jakarta)]">
						<?php cw_e( 'header.countryBadge' ); ?>
					</span>
				</a>

				<p class="text-xs text-cw-gray-500 leading-relaxed"><?php echo esc_html( $cw_blurb ); ?></p>

				<div class="space-y-3 pt-2">
					<p class="flex items-start gap-2.5 text-xs">
						<span class="text-cw-red-light mt-0.5 flex-shrink-0 inline-flex"><?php cw_icon( 'map-pin', 16 ); ?></span>
						<span><?php echo esc_html( $cw_address ); ?></span>
					</p>
					<p class="flex items-center gap-2.5 text-xs">
						<span class="text-cw-red-light flex-shrink-0 inline-flex"><?php cw_icon( 'phone', 16 ); ?></span>
						<?php // dir="ltr" : un numéro reste lisible de gauche à droite même en arabe. ?>
						<a href="<?php echo esc_url( $cw_tel_href ); ?>" class="hover:text-cw-fg transition-colors" dir="ltr"><?php echo esc_html( $cw_phone ); ?></a>
					</p>
					<p class="flex items-center gap-2.5 text-xs">
						<span class="text-cw-red-light flex-shrink-0 inline-flex"><?php cw_icon( 'mail', 16 ); ?></span>
						<a href="<?php echo esc_url( 'mailto:' . $cw_email ); ?>" class="hover:text-cw-fg transition-colors" dir="ltr"><?php echo esc_html( $cw_email ); ?></a>
					</p>
				</div>
			</div>

			<?php // Colonne 2 — solutions. ?>
			<div>
				<h4 class="text-cw-fg font-bold text-sm uppercase tracking-wider mb-4 font-[family-name:var(--font-jakarta)]">
					<?php cw_e( 'footer.solutionsTitle' ); ?>
				</h4>
				<ul class="space-y-2 text-xs">
					<?php foreach ( $cw_solutions as $cw_sol ) : ?>
						<li>
							<a href="<?php echo esc_url( cw_nav_url( $cw_sol['href'] ) ); ?>" class="hover:text-cw-fg transition-colors">
								<?php echo esc_html( $cw_sol['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<li>
						<a href="<?php echo esc_url( cw_url( '/solutions' ) ); ?>" class="text-cw-red-light hover:text-cw-fg transition-colors font-medium">
							<?php cw_e( 'footer.allSolutions' ); ?>
						</a>
					</li>
				</ul>
			</div>

			<?php // Colonne 3 — navigation. ?>
			<div>
				<h4 class="text-cw-fg font-bold text-sm uppercase tracking-wider mb-4 font-[family-name:var(--font-jakarta)]">
					<?php cw_e( 'footer.navTitle' ); ?>
				</h4>
				<ul class="space-y-2 text-xs">
					<?php
					foreach ( cw_footer_nav_items() as $cw_link ) :
						?>
						<li>
							<a href="<?php echo esc_url( cw_nav_url( $cw_link['href'] ) ); ?>" <?php if ( ! empty( $cw_link['target'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?> class="hover:text-cw-fg transition-colors">
								<?php echo esc_html( $cw_link['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
					<li>
						<a
							href="<?php echo esc_url( cw_editor_link( 'client_portal', CW_CLIENT_PORTAL_URL ) ); ?>"
							target="_blank"
							rel="noopener noreferrer"
							class="hover:text-cw-fg transition-colors flex items-center gap-1"
						>
							<span><?php cw_e( 'footer.navClientArea' ); ?></span>
							<?php cw_icon( 'external-link', 10 ); ?>
						</a>
					</li>
				</ul>
			</div>

			<?php // Colonne 4 — accès client et support. ?>
			<div class="space-y-5">
				<h4 class="text-cw-fg font-bold text-sm uppercase tracking-wider font-[family-name:var(--font-jakarta)]">
					<?php cw_e( 'footer.supportTitle' ); ?>
				</h4>

				<a
					href="<?php echo esc_url( cw_editor_link( 'client_portal', CW_CLIENT_PORTAL_URL ) ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="cta-outline w-full justify-center text-xs py-3"
				>
					<?php cw_icon( 'external-link', 14 ); ?>
					<span><?php cw_e( 'footer.portalCta' ); ?></span>
				</a>

				<div class="bg-cw-surface border border-white/[0.04] p-4 rounded-xl space-y-3">
					<p class="text-[11px] leading-relaxed text-cw-gray-500"><?php cw_e( 'footer.supportBlurb' ); ?></p>
					<a
						href="<?php echo esc_url( cw_editor_link( 'whatsapp', 'https://wa.me/212522361988' ) ); ?>"
						target="_blank"
						rel="noopener noreferrer"
						class="flex items-center justify-center gap-2 w-full py-2 bg-cw-green/10 hover:bg-cw-green/20 text-cw-green border border-cw-green/20 rounded-lg text-xs font-semibold transition-all"
					>
						<?php cw_icon( 'message-circle', 14 ); ?>
						<span><?php cw_e( 'footer.whatsapp' ); ?></span>
					</a>
				</div>

				<div class="flex gap-3">
					<a
						href="<?php echo esc_url( $cw_linkedin ); ?>"
						target="_blank"
						rel="noopener noreferrer"
						class="w-9 h-9 rounded-lg bg-cw-black border border-white/[0.04] flex items-center justify-center text-cw-gray-400 hover:border-cw-red hover:text-cw-red-light hover:bg-cw-red/5 transition-all"
						aria-label="LinkedIn"
					>
						<?php // Logo de marque : absent de Lucide, conservé en SVG inline comme dans Footer.tsx. ?>
						<svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
						</svg>
					</a>
				</div>
			</div>
		</div>

		<div class="border-t border-white/[0.04] mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-cw-gray-500">
			<p><?php cw_e( 'footer.rights' ); ?></p>
			<div class="flex gap-4">
				<?php // Les pages légales font foi en français : liens toujours vers la racine. ?>
				<a href="<?php echo esc_url( home_url( '/mentions-legales' ) ); ?>" class="hover:text-cw-fg transition-colors">
					<?php cw_e( 'footer.legal' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/confidentialite' ) ); ?>" class="hover:text-cw-fg transition-colors">
					<?php cw_e( 'footer.privacy' ); ?>
				</a>
			</div>
		</div>
	</div>
</footer>

<?php // Bouton de retour en haut. ?>
<button
	type="button"
	class="cw-back-to-top fixed bottom-6 right-6 z-40 w-11 h-11 rounded-full bg-cw-red text-white shadow-lg items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none hidden"
	aria-label="<?php echo esc_attr( (string) cw_t( 'common.backToTop' ) ); ?>"
	data-cw-back-to-top
>
	<?php cw_icon( 'arrow-up', 20 ); ?>
</button>

<?php wp_footer(); ?>
</body>
</html>
