<?php
/**
 * Client access request gateway. CarrierWeb's team provisions portal access.
 * The contact form handles requests; no public account or password is created.
 *
 * @package CarrierWeb
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
$cw_email = cw_setting( 'contactEmail', '', 'info@carrierweb.ma' );
$cw_copy = (array) cw_t( 'auth.accessRequest', array() );
ob_start();
?>
<div class="space-y-5">
    <div class="w-16 h-16 rounded-full bg-cw-red/10 flex items-center justify-center text-cw-red-light">
        <?php cw_icon( 'headset', 30 ); ?>
    </div>
    <p class="text-sm text-cw-gray-300 leading-relaxed"><?php echo esc_html( $cw_copy['body'] ); ?></p>
    <a href="<?php echo esc_url( cw_editor_link( 'register', cw_url( '/contact' ) ) ); ?>" class="cta-primary w-full justify-center text-sm py-3.5">
        <span><?php echo esc_html( $cw_copy['cta'] ); ?></span>
        <?php cw_icon( 'arrow-right', 16 ); ?>
    </a>
    <p class="text-xs text-cw-gray-400 leading-relaxed"><?php echo esc_html( $cw_copy['note'] ); ?></p>
    <p class="text-xs text-cw-gray-400 leading-relaxed">
        <?php cw_e( 'clientArea.byEmail' ); ?>
        <a href="<?php echo esc_url( 'mailto:' . $cw_email ); ?>" class="text-cw-red-light hover:underline" dir="ltr"><?php echo esc_html( $cw_email ); ?></a>
    </p>
</div>
<?php
$cw_content = ob_get_clean();
ob_start();
?>
<p class="text-xs text-cw-gray-400">
    <?php cw_e( 'auth.register.already' ); ?>
    <a href="<?php echo esc_url( cw_url( '/connexion' ) ); ?>" class="text-cw-red-light hover:underline font-semibold"><?php cw_e( 'auth.register.signIn' ); ?></a>
</p>
<?php
$cw_footer = ob_get_clean();
get_template_part(
    'template-parts/blocks/auth-shell',
    null,
    array(
        'title'    => $cw_copy['title'],
        'subtitle' => (string) cw_t( 'auth.register.subtitle' ),
        'content'  => $cw_content,
        'footer'   => $cw_footer,
    )
);
get_footer();
