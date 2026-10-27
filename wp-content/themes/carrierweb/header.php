<?php
/**
 * En-tête du site
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cw_nav_items = cw_nav_items();
?>
<!doctype html>
<?php
/*
 * lang/dir posés depuis NOTRE locale, pas via language_attributes() : celle-ci
 * renvoie la locale WordPress (en_US ici, WPLANG étant vide), ce qui
 * annoncerait de l'anglais sur les pages françaises et arabes — une vraie
 * régression d'accessibilité (synthèse vocale, césure, moteurs de recherche).
 */
?>
<html lang="<?php echo esc_attr( cw_html_lang() ); ?>" dir="<?php echo esc_attr( cw_dir() ); ?>" class="antialiased">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	/**
	 * Anti-flash : pose data-theme sur <html> AVANT le premier rendu.
	 */
	?>
	<script>document.documentElement.classList.add('cw-header-js');(function(){try{var t=localStorage.getItem('cw-theme');if(t!=='light'&&t!=='dark'){t=window.matchMedia('(prefers-color-scheme: light)').matches?'light':'dark';}document.documentElement.dataset.theme=t;}catch(e){document.documentElement.dataset.theme='dark';}})();</script>
	<meta name="geo.region" content="MA">
	<meta name="geo.placename" content="Casablanca">
	<meta name="theme-color" content="#050708">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php // Lien d'évitement : premier élément focusable de chaque page (WCAG 2.4.1). ?>
<a href="#main-content" class="skip-link"><?php cw_e( 'common.skipToContent' ); ?></a>

<?php // Progression de lecture et retour en haut. ?>
<div class="cw-scroll-progress" data-cw-scroll-progress aria-hidden="true"></div>

<?php
/*
 * Barre « flottante » (floating pill) : détachée du bord haut, arrondie, fond
 * plat opaque — pas de flou, plus lisible sur le hero photo. Se compresse au
 * défilement (68 → 58px) via la classe is-scrolled posée par le JS.
 */
?>
<header class="site-header fixed top-0 left-0 right-0 z-50 px-3 pt-3 sm:px-4 sm:pt-4">
	<div
		class="cw-header-pill w-full mx-auto flex items-center justify-between gap-3 rounded-[28px] border pl-4 pr-3 sm:pl-5 shadow-[0_10px_40px_-8px_rgba(0,0,0,0.55)] transition-all duration-500"
		style="background-color: var(--cw-surface); border-color: var(--border-medium);"
		data-cw-header
	>
		<div class="cw-header-brand shrink-0" data-cw-header-brand>
			<?php get_template_part( 'template-parts/nav/logo' ); ?>
		</div>

		<nav
			aria-label="<?php echo esc_attr( (string) cw_t( 'header.nav.solutions' ) ); ?>"
			class="cw-header-nav"
			data-cw-header-nav
		>
			<?php foreach ( $cw_nav_items as $cw_item ) : ?>
				<?php
				$cw_has_mega = ! empty( $cw_item['mega'] );
				$cw_children = $cw_item['children'] ?? array();
				$cw_has_sub  = $cw_has_mega || ! empty( $cw_children );
				$cw_active   = cw_nav_active( $cw_item['href'] );
				?>
				<div class="relative group" <?php echo $cw_has_sub ? 'data-cw-dropdown' : ''; ?>>
					<a
						href="<?php echo esc_url( cw_nav_url( $cw_item['href'] ) ); ?>"
						<?php if ( ! empty( $cw_item['target'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
						class="nav-link flex items-center gap-1 py-2.5 whitespace-nowrap<?php echo $cw_active ? ' active' : ''; ?>"
						<?php echo $cw_active ? 'aria-current="page"' : ''; ?>
						<?php if ( $cw_has_sub ) : ?>
							aria-expanded="false"
							aria-haspopup="menu"
							data-cw-dropdown-trigger
						<?php endif; ?>
					>
						<?php echo esc_html( $cw_item['label'] ); ?>
						<?php if ( $cw_has_sub ) : ?>
							<span class="transition-transform duration-200 group-hover:rotate-180 group-[.is-open]:rotate-180 inline-flex">
								<?php cw_icon( 'chevron-down', 13 ); ?>
							</span>
						<?php endif; ?>
					</a>

					<?php if ( $cw_has_mega ) : ?>
						<?php // Méga-menu Solutions — panneau plat, 2 colonnes. ?>
						<div
							class="absolute top-full left-0 pt-4 z-50 hidden group-hover:block group-focus-within:block group-[.is-open]:block"
							data-cw-dropdown-panel
						>
							<div class="solid-panel w-[600px] shadow-2xl overflow-hidden">
								<div class="p-5 grid grid-cols-2 gap-1">
									<?php foreach ( $cw_children as $cw_sol ) : ?>
										<a
											href="<?php echo esc_url( cw_nav_url( $cw_sol['href'] ) ); ?>"
						<?php if ( ! empty( $cw_sol['target'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
											class="flex items-center gap-3.5 px-3.5 py-4 rounded-2xl hover:bg-white/[0.05] focus-visible:bg-white/[0.06] transition-colors group/item"
										>
											<div class="w-10 h-10 rounded-full bg-cw-red/10 flex items-center justify-center text-cw-red-light group-hover/item:bg-cw-red group-hover/item:text-white transition-colors flex-shrink-0">
												<?php cw_icon( $cw_sol['icon'], 16 ); ?>
											</div>
											<div class="min-w-0">
												<div class="text-[15px] font-semibold text-cw-gray-100 group-hover/item:text-white transition-colors truncate">
													<?php echo esc_html( $cw_sol['label'] ); ?>
												</div>
												<div class="text-[13px] text-cw-gray-500 mt-0.5 truncate">
													<?php echo esc_html( $cw_sol['desc'] ); ?>
												</div>
											</div>
										</a>
									<?php endforeach; ?>
								</div>
								<a
									href="<?php echo esc_url( cw_nav_url( $cw_item['href'] ) ); ?>"
						<?php if ( ! empty( $cw_item['target'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
									class="flex items-center justify-center gap-2 border-t py-4 text-sm font-semibold text-cw-red-light hover:text-cw-fg hover:bg-cw-red/10 transition-colors"
									style="border-color: var(--border-subtle);"
								>
									<?php cw_e( 'header.seeAllSolutions' ); ?>
									<?php cw_icon( 'arrow-right', 15 ); ?>
								</a>
							</div>
						</div>
					<?php elseif ( ! empty( $cw_children ) ) : ?>
						<?php // Menu déroulant simple — Secteurs. ?>
						<div
							class="absolute top-full left-0 pt-4 z-50 hidden group-hover:block group-focus-within:block group-[.is-open]:block"
							data-cw-dropdown-panel
						>
							<div class="solid-panel p-3 min-w-[280px] shadow-2xl">
								<?php foreach ( $cw_children as $cw_child ) : ?>
									<a
										href="<?php echo esc_url( cw_nav_url( $cw_child['href'] ) ); ?>"
						<?php if ( ! empty( $cw_child['target'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
										class="block px-4 py-3.5 text-[15px] text-cw-gray-300 hover:text-cw-fg hover:bg-white/[0.05] focus-visible:bg-white/[0.06] transition-colors rounded-xl"
									>
										<?php echo esc_html( $cw_child['label'] ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</nav>

		<?php // Droite : langue + thème + connexion + CTA + bouton menu mobile. ?>
		<div class="cw-header-actions flex items-center gap-1 shrink-0" data-cw-header-actions>
			<span class="hidden xl:block w-px h-5 mr-1" style="background: var(--border-medium);" aria-hidden="true"></span>

			<div class="hidden xl:flex items-center px-0.5">
				<?php get_template_part( 'template-parts/nav/language-switcher' ); ?>
			</div>

			<?php get_template_part( 'template-parts/nav/theme-toggle' ); ?>

			<span class="hidden md:block w-px h-5 mx-0.5" style="background: var(--border-medium);" aria-hidden="true"></span>

			<a
				href="<?php echo esc_url( cw_url( '/connexion' ) ); ?>"
				class="hidden md:inline-flex items-center text-sm font-medium text-cw-gray-300 hover:text-cw-fg hover:bg-white/[0.06] px-3 py-2.5 rounded-full transition-colors font-[family-name:var(--font-jakarta)] whitespace-nowrap"
			>
				<?php cw_e( 'header.login' ); ?>
			</a>

			<a
				href="<?php echo esc_url( cw_editor_link( 'demo', cw_url( '/contact' ) ) ); ?>"
				class="cta-primary text-xs px-4 py-2.5 rounded-xl !hidden sm:!inline-flex whitespace-nowrap"
				id="demo-btn"
			>
				<?php cw_e( 'common.requestDemo' ); ?>
			</a>

			<button
				type="button"
				class="cw-header-menu-button p-2 rounded-lg hover:bg-white/[0.06] transition-colors text-cw-fg"
				aria-label="<?php echo esc_attr( (string) cw_t( 'header.openMenu' ) ); ?>"
				aria-expanded="false"
				aria-controls="mobile-menu"
				data-cw-menu-open
			>
				<?php cw_icon( 'menu', 24 ); ?>
			</button>
		</div>
	</div>
</header>

<?php // ─── Menu mobile plein écran ─── ?>
<div
	id="mobile-menu"
	class="mobile-menu"
	role="dialog"
	aria-modal="true"
	aria-label="<?php echo esc_attr( (string) cw_t( 'header.nav.solutions' ) ); ?>"
	data-cw-mobile-menu
>
	<div class="flex flex-col h-full">
		<div class="flex items-center justify-between mb-8">
			<?php get_template_part( 'template-parts/nav/logo', null, array( 'compact' => true ) ); ?>
			<button
				type="button"
				class="p-2.5 rounded-xl hover:bg-white/[0.06] text-cw-fg transition-colors"
				aria-label="<?php echo esc_attr( (string) cw_t( 'header.closeMenu' ) ); ?>"
				data-cw-menu-close
			>
				<?php cw_icon( 'x', 22 ); ?>
			</button>
		</div>

		<nav aria-label="<?php echo esc_attr( (string) cw_t( 'header.nav.solutions' ) ); ?>" class="flex flex-col gap-0.5 flex-1 overflow-y-auto">
			<?php foreach ( $cw_nav_items as $cw_idx => $cw_item ) : ?>
				<?php
				$cw_sub = $cw_item['children'] ?? array();
				?>
				<div style="animation-delay: <?php echo esc_attr( (string) ( $cw_idx * 60 ) ); ?>ms" data-cw-mobile-item>
					<div class="flex items-center">
						<a
							href="<?php echo esc_url( cw_nav_url( $cw_item['href'] ) ); ?>"
						<?php if ( ! empty( $cw_item['target'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
							class="flex-1 px-4 py-3.5 text-cw-gray-200 hover:text-cw-fg hover:bg-white/[0.04] rounded-xl transition-all font-medium text-base font-[family-name:var(--font-jakarta)]"
						>
							<?php echo esc_html( $cw_item['label'] ); ?>
						</a>
						<?php if ( ! empty( $cw_sub ) ) : ?>
							<button
								type="button"
								class="p-3 mr-1 rounded-lg text-cw-gray-500 hover:text-cw-fg hover:bg-white/[0.06] transition-all"
								aria-expanded="false"
								aria-label="<?php echo esc_attr( cw_t( 'header.expand' ) . ' ' . $cw_item['label'] ); ?>"
								data-cw-mobile-toggle
								data-label-expand="<?php echo esc_attr( cw_t( 'header.expand' ) . ' ' . $cw_item['label'] ); ?>"
								data-label-collapse="<?php echo esc_attr( cw_t( 'header.collapse' ) . ' ' . $cw_item['label'] ); ?>"
							>
								<span class="inline-flex transition-transform duration-200">
									<?php cw_icon( 'chevron-down', 18 ); ?>
								</span>
							</button>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $cw_sub ) ) : ?>
						<div class="ml-5 border-l border-white/[0.06] pl-4 mb-2 hidden" data-cw-mobile-panel>
							<?php foreach ( $cw_sub as $cw_child ) : ?>
								<a
									href="<?php echo esc_url( cw_nav_url( $cw_child['href'] ) ); ?>"
						<?php if ( ! empty( $cw_child['target'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
									class="block px-3 py-2.5 text-sm text-cw-gray-400 hover:text-cw-fg transition-colors"
								>
									<?php echo esc_html( $cw_child['label'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</nav>

		<div class="pt-6 border-t border-white/[0.06] space-y-3 mt-auto">
			<div class="flex items-center justify-between">
				<?php get_template_part( 'template-parts/nav/language-switcher' ); ?>
				<?php get_template_part( 'template-parts/nav/theme-toggle' ); ?>
			</div>
			<div class="grid grid-cols-2 gap-3">
				<a href="<?php echo esc_url( cw_url( '/connexion' ) ); ?>" class="cta-secondary w-full justify-center text-sm">
					<?php cw_icon( 'log-in', 14 ); ?>
					<?php cw_e( 'header.login' ); ?>
				</a>
				<a href="<?php echo esc_url( cw_url( '/inscription' ) ); ?>" class="cta-outline w-full justify-center text-sm">
					<?php cw_e( 'header.createAccount' ); ?>
				</a>
			</div>
			<a
				href="<?php echo esc_url( cw_editor_link( 'client_portal', CW_CLIENT_PORTAL_URL ) ); ?>"
				target="_blank"
				rel="noopener noreferrer"
				class="flex items-center justify-center gap-1.5 text-xs text-cw-gray-400 hover:text-cw-fg transition-colors py-1"
			>
				<?php cw_icon( 'external-link', 13 ); ?>
				<span><?php cw_e( 'header.clientPortal' ); ?></span>
			</a>
			<a href="<?php echo esc_url( cw_editor_link( 'demo', cw_url( '/contact' ) ) ); ?>" class="cta-primary w-full justify-center text-sm">
				<?php cw_e( 'common.requestDemo' ); ?>
			</a>
		</div>
	</div>
</div>
