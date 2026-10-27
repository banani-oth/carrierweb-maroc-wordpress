<?php
/**
 * Coque des pages Connexion / Inscription
 *
 * @package CarrierWeb
 * @var string $args['title']    Titre du panneau.
 * @var string $args['subtitle'] Sous-titre.
 * @var string $args['content']  Balisage déjà échappé du panneau.
 * @var string $args['footer']   Balisage déjà échappé sous le panneau.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_title    = $args['title'] ?? '';
$cw_subtitle = $args['subtitle'] ?? '';
$cw_content  = $args['content'] ?? '';
$cw_footer   = $args['footer'] ?? '';
?>
<main id="main-content" class="bg-cw-black min-h-screen flex items-center justify-center px-4 py-32">
	<div class="w-full max-w-md">

		<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="inline-flex items-center gap-2 text-xs text-cw-gray-500 hover:text-cw-fg transition-colors mb-8">
			<?php cw_icon( 'arrow-left', 14 ); ?>
			<?php cw_e( 'auth.backHome' ); ?>
		</a>

		<div class="bg-cw-surface border border-white/[0.06] rounded-2xl p-7 sm:p-9 shadow-2xl">
			<div class="mb-7">
				<h1 class="text-2xl font-extrabold text-cw-fg font-[family-name:var(--font-jakarta)] tracking-tight">
					<?php echo esc_html( $cw_title ); ?>
				</h1>
				<p class="text-cw-gray-400 text-xs leading-relaxed mt-2"><?php echo esc_html( $cw_subtitle ); ?></p>
			</div>

			<?php
			// Contenu construit et échappé par le gabarit appelant.
			echo $cw_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>

		<?php if ( '' !== $cw_footer ) : ?>
			<div class="text-center mt-6">
				<?php echo $cw_footer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>

	</div>
</main>
