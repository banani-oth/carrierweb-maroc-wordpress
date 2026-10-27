<?php
/**
 * Client portal gateway. Authentication takes place on CarrierWeb's portal.
 * This marketing site does not request or store client credentials.
 *
 * @package CarrierWeb
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
$cw_features = (array) cw_t( 'clientArea.features', array() );
$cw_portal   = cw_editor_link( 'client_portal', CW_CLIENT_PORTAL_URL );
$cw_access_label = (string) cw_t( 'auth.requestLabel' );
ob_start();
?>
<div class="space-y-5">
    <p class="text-sm text-cw-gray-300 leading-relaxed"><?php cw_e( 'clientArea.portalDesc' ); ?></p>
    <ul class="space-y-3">
        <?php foreach ( $cw_features as $cw_feature ) : ?>
            <li class="flex items-start gap-3 text-cw-gray-300 text-xs leading-relaxed">
                <span class="text-cw-red-light mt-0.5 flex-shrink-0 inline-flex"><?php cw_icon( 'check-circle-2', 16 ); ?></span>
                <span><?php echo esc_html( (string) $cw_feature ); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="<?php echo esc_url( $cw_portal ); ?>" target="_blank" rel="noopener noreferrer" class="cta-primary w-full justify-center text-sm py-3.5">
        <?php cw_icon( 'external-link', 16 ); ?>
        <span><?php cw_e( 'clientArea.openPortal' ); ?></span>
    </a>
    <p class="text-xs text-cw-gray-400 leading-relaxed"><?php cw_e( 'clientArea.securityNote' ); ?></p>
    <a href="<?php echo esc_url( cw_editor_link( 'demo', cw_url( '/contact' ) ) ); ?>" class="inline-flex items-center gap-2 text-xs text-cw-red-light hover:underline">
        <?php cw_icon( 'headset', 15 ); ?>
        <?php cw_e( 'clientArea.helpTitle' ); ?>
    </a>
</div>
<?php
$cw_content = ob_get_clean();
ob_start();
?>
<p class="text-xs text-cw-gray-400">
    <?php cw_e( 'auth.login.noAccount' ); ?>
    <a href="<?php echo esc_url( cw_url( '/inscription' ) ); ?>" class="text-cw-red-light hover:underline font-semibold"><?php echo esc_html( $cw_access_label ); ?></a>
</p>
<?php
$cw_footer = ob_get_clean();
get_template_part(
    'template-parts/blocks/auth-shell',
    null,
    array(
        'title'    => (string) cw_t( 'clientArea.portalTitle' ),
        'subtitle' => (string) cw_t( 'auth.login.subtitle' ),
        'content'  => $cw_content,
        'footer'   => $cw_footer,
    )
);
get_footer();
