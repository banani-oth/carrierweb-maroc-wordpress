<?php
/**
 * Bandeau d'en-tête avec fil d'Ariane.
 *
 * Motif commun aux listings et aux pages de détail (solutions, secteurs,
 * ressources, pages fixes). Centralisé pour éviter de répéter le balisage — et
 * son échappement — dans huit gabarits.
 *
 * @package CarrierWeb
 *
 * @var string $args['breadcrumb'] Libellé de la page courante dans le fil.
 * @var string $args['label']      Sur-titre (section-label).
 * @var string $args['title_pre']  Début du titre.
 * @var string $args['title_hl']   Fragment en dégradé.
 * @var string $args['desc']       Texte d'introduction.
 * @var array  $args['parent']     Optionnel : ['label' => …, 'href' => …] intercalé dans le fil.
 * @var string $args['icon']       Optionnel : icône affichée en médaillon.
 * @var string $args['badge']      Optionnel : petit badge au-dessus du titre.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_bc      = $args['breadcrumb'] ?? '';
$cw_label   = $args['label'] ?? '';
$cw_pre     = $args['title_pre'] ?? '';
$cw_hl      = $args['title_hl'] ?? '';
$cw_desc    = $args['desc'] ?? '';
$cw_parent  = $args['parent'] ?? null;
$cw_icon    = $args['icon'] ?? '';
$cw_badge   = $args['badge'] ?? '';
?>
<section class="relative py-12 bg-gradient-to-b from-cw-red/10 to-cw-black overflow-hidden border-b border-white/[0.04]">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

		<nav class="flex items-center gap-1.5 text-xs text-cw-gray-500 mb-6" aria-label="<?php echo esc_attr( (string) cw_t( 'common.breadcrumbAria' ) ); ?>">
			<a href="<?php echo esc_url( cw_url( '/' ) ); ?>" class="hover:text-cw-fg transition-colors"><?php cw_e( 'common.home' ); ?></a>
			<?php cw_icon( 'chevron-right', 10 ); ?>
			<?php if ( $cw_parent ) : ?>
				<a href="<?php echo esc_url( cw_url( $cw_parent['href'] ) ); ?>" class="hover:text-cw-fg transition-colors"><?php echo esc_html( $cw_parent['label'] ); ?></a>
				<?php cw_icon( 'chevron-right', 10 ); ?>
			<?php endif; ?>
			<span class="text-cw-gray-300" aria-current="page"><?php echo esc_html( $cw_bc ); ?></span>
		</nav>

		<div class="flex items-start gap-5">
			<?php if ( $cw_icon ) : ?>
				<div class="w-16 h-16 rounded-2xl bg-cw-red/10 border border-cw-red/20 flex items-center justify-center text-cw-red-light flex-shrink-0">
					<?php cw_icon( $cw_icon, 32 ); ?>
				</div>
			<?php endif; ?>

			<div class="space-y-3 min-w-0">
				<?php if ( $cw_badge ) : ?>
					<span class="inline-flex items-center rounded-md bg-cw-red/10 border border-cw-red/20 px-2.5 py-1 text-[10px] font-bold tracking-[0.14em] uppercase text-cw-red-light">
						<?php echo esc_html( $cw_badge ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $cw_label ) : ?>
					<span class="section-label"><?php echo esc_html( $cw_label ); ?></span>
				<?php endif; ?>

				<h1 class="page-title">
					<?php echo esc_html( $cw_pre ); ?><?php if ( '' !== $cw_hl ) : ?><span class="gradient-text"><?php echo esc_html( $cw_hl ); ?></span><?php endif; ?>
				</h1>

				<?php if ( $cw_desc ) : ?>
					<p class="page-desc max-w-3xl"><?php echo esc_html( $cw_desc ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
