<?php
/**
 * Logo CarrierWeb + badge « MAROC ».
 *
 * Le logo officiel est du texte noir sur fond transparent : il est posé dans
 * une pastille blanche pour rester lisible sur le fond sombre du site.
 *
 * @package CarrierWeb
 *
 * @var bool $compact Version réduite (menu mobile).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$compact = isset( $args['compact'] ) ? (bool) $args['compact'] : false;
?>
<a
	href="<?php echo esc_url( cw_url( '/' ) ); ?>"
	aria-label="<?php echo esc_attr( (string) cw_t( 'header.logoAria' ) ); ?>"
	class="flex items-center gap-2 flex-shrink-0 group"
>
	<span class="flex items-center bg-white rounded-lg px-2.5 py-2 ring-1 ring-white/20 transition-shadow duration-300 group-hover:shadow-[0_0_18px_rgba(191,32,38,0.35)]">
		<img
			src="<?php echo esc_url( cw_media_url( 'logo', CW_URI . '/assets/img/logo.webp' ) ); ?>"
			alt="<?php echo esc_attr( cw_media_alt( 'logo', 'CarrierWeb' ) ); ?>"
			width="560"
			height="54"
			<?php // Le logo est au-dessus de la ligne de flottaison sur toutes les pages. ?>
			fetchpriority="high"
			decoding="async"
			class="<?php echo $compact ? 'h-[12px] w-auto' : 'h-[13px] w-auto sm:h-[15px]'; ?>"
		>
	</span>
	<span class="inline-flex items-center rounded-md bg-cw-red px-1.5 py-[3px] text-[9px] font-bold tracking-[0.18em] text-white leading-none font-[family-name:var(--font-jakarta)]">
		<?php cw_e( 'header.countryBadge' ); ?>
	</span>
</a>
