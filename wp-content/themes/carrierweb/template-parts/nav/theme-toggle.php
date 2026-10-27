<?php
/**
 * Bouton de choix du thème clair ou sombre.
 *
 * Les deux icônes sont rendues ; data-theme détermine celle qui est affichée.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<button
	type="button"
	class="cw-theme-toggle inline-flex items-center justify-center w-10 h-10 rounded-lg border border-white/[0.08] text-cw-gray-300 hover:text-cw-fg hover:border-cw-red/40 transition-colors"
	data-cw-theme-toggle
	data-label-light="<?php echo esc_attr( (string) cw_t( 'common.themeLightAria' ) ); ?>"
	data-label-dark="<?php echo esc_attr( (string) cw_t( 'common.themeDarkAria' ) ); ?>"
	aria-label="<?php echo esc_attr( (string) cw_t( 'common.themeLightAria' ) ); ?>"
>
	<?php // Visible en thème sombre : propose de passer en clair. ?>
	<span class="cw-theme-icon-dark inline-flex"><?php cw_icon( 'sun', 18 ); ?></span>
	<?php // Visible en thème clair : propose de passer en sombre. ?>
	<span class="cw-theme-icon-light inline-flex"><?php cw_icon( 'moon', 18 ); ?></span>
</button>
