<?php
/**
 * Page Espace Client — /espace-client
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cw_features = (array) cw_t( 'clientArea.features', array() );
$cw_phone    = cw_setting( 'contactPhone', '', '+212 (0)5 22 36 19 88' );
$cw_email    = cw_setting( 'contactEmail', '', 'info@carrierweb.ma' );
$cw_tel      = 'tel:' . preg_replace( '/[^\d+]/', '', $cw_phone );
?>

<main id="main-content" class="bg-cw-black min-h-screen pt-[110px] pb-20">

	<?php
	get_template_part(
		'template-parts/blocks/breadcrumb-hero',
		null,
		array(
			'breadcrumb' => (string) cw_t( 'clientArea.breadcrumb' ),
			'label'      => (string) cw_t( 'clientArea.label' ),
			'title_pre'  => (string) cw_t( 'clientArea.h1Pre' ),
			'title_hl'   => (string) cw_t( 'clientArea.h1Hl' ),
			'desc'       => (string) cw_t( 'clientArea.desc' ),
		)
	);
	?>

	<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">

		<div class="lg:col-span-7 bg-cw-surface border border-white/[0.04] rounded-2xl p-6 sm:p-8 space-y-6">
			<div class="space-y-2">
				<h2 class="text-lg font-bold text-cw-fg font-[family-name:var(--font-jakarta)]"><?php cw_e( 'clientArea.portalTitle' ); ?></h2>
				<p class="text-cw-gray-400 text-xs leading-relaxed"><?php cw_e( 'clientArea.portalDesc' ); ?></p>
			</div>

			<?php if ( ! empty( $cw_features ) ) : ?>
				<ul class="space-y-3">
					<?php foreach ( $cw_features as $cw_feature ) : ?>
						<li class="flex items-start gap-3 text-cw-gray-300 text-xs leading-relaxed">
							<span class="text-cw-red-light mt-0.5 flex-shrink-0 inline-flex"><?php cw_icon( 'check-circle-2', 16 ); ?></span>
							<span><?php echo esc_html( (string) $cw_feature ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<a
				href="<?php echo esc_url( cw_editor_link( 'client_portal', CW_CLIENT_PORTAL_URL ) ); ?>"
				target="_blank"
				rel="noopener noreferrer"
				class="cta-primary w-full justify-center text-sm py-4"
			>
				<?php cw_icon( 'external-link', 16 ); ?>
				<span><?php cw_e( 'clientArea.openPortal' ); ?></span>
			</a>

			<p class="flex items-start gap-2 text-[11px] text-cw-gray-500 leading-relaxed">
				<span class="text-cw-red-light mt-0.5 flex-shrink-0 inline-flex"><?php cw_icon( 'lock', 14 ); ?></span>
				<span><?php cw_e( 'clientArea.securityNote' ); ?></span>
			</p>
		</div>

		<div class="lg:col-span-5 space-y-6">
			<div class="bg-cw-surface border border-white/[0.04] rounded-2xl p-6 space-y-3">
				<h2 class="text-sm font-bold text-cw-fg font-[family-name:var(--font-jakarta)]"><?php cw_e( 'clientArea.helpTitle' ); ?></h2>
				<p class="text-cw-gray-400 text-xs leading-relaxed"><?php cw_e( 'clientArea.helpDesc' ); ?></p>
			</div>

			<div class="bg-gradient-to-br from-cw-red/10 to-cw-surface border border-cw-red/20 rounded-2xl p-6 space-y-4">
				<h2 class="text-sm font-bold text-cw-fg font-[family-name:var(--font-jakarta)]"><?php cw_e( 'clientArea.supportTitle' ); ?></h2>

				<p class="flex items-center gap-2.5 text-xs text-cw-gray-300">
					<span class="text-cw-red-light inline-flex"><?php cw_icon( 'phone', 15 ); ?></span>
					<span><?php cw_e( 'clientArea.byPhone' ); ?></span>
					<a href="<?php echo esc_url( $cw_tel ); ?>" class="hover:text-cw-fg transition-colors font-semibold" dir="ltr"><?php echo esc_html( $cw_phone ); ?></a>
				</p>

				<p class="flex items-center gap-2.5 text-xs text-cw-gray-300">
					<span class="text-cw-red-light inline-flex"><?php cw_icon( 'mail', 15 ); ?></span>
					<span><?php cw_e( 'clientArea.byEmail' ); ?></span>
					<a href="<?php echo esc_url( 'mailto:' . $cw_email ); ?>" class="hover:text-cw-fg transition-colors font-semibold" dir="ltr"><?php echo esc_html( $cw_email ); ?></a>
				</p>

				<p class="text-[11px] text-cw-gray-500 leading-relaxed pt-1 border-t border-white/[0.04]">
					<?php cw_e( 'clientArea.supportNote' ); ?>
				</p>
			</div>
		</div>

	</section>
</main>

<?php
get_footer();
