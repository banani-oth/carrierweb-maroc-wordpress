<?php
/**
 * Rendu SVG des icônes Lucide définies dans inc/icon-data.php.
 *
 * Les noms inconnus utilisent une icône de repli.
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Table des icônes, chargée une seule fois.
 *
 * @return array<string, array<int, array{0:string,1:array<string,string>}>>
 */
function cw_icon_data(): array {
	static $data = null;

	if ( null === $data ) {
		$file = CW_DIR . '/inc/icon-data.php';
		$data = is_readable( $file ) ? (array) require $file : array();
	}

	return $data;
}

/**
 * Icône de repli quand la clé est absente ou inconnue.
 */
const CW_ICON_FALLBACK = 'settings-2';

/**
 * Construit le SVG d'une icône.
 *
 * @param string|null          $name  Slug Lucide (ex. « map-pin »). Correspond
 *                                    aux valeurs du champ ACF `icon`.
 * @param int                  $size  Taille en px (width/height).
 * @param array<string,string> $attrs Attributs supplémentaires (class, aria-*…).
 * @return string SVG prêt à l'affichage, ou chaîne vide si aucune donnée.
 */
function cw_get_icon( ?string $name, int $size = 24, array $attrs = array() ): string {
	$icons = cw_icon_data();

	if ( empty( $icons ) ) {
		return '';
	}

	$key = $name && isset( $icons[ $name ] ) ? $name : CW_ICON_FALLBACK;

	if ( ! isset( $icons[ $key ] ) ) {
		return '';
	}

	// Attributs SVG communs aux icônes Lucide.
	$svg_attrs = array(
		'xmlns'            => 'http://www.w3.org/2000/svg',
		'width'            => (string) $size,
		'height'           => (string) $size,
		'viewBox'          => '0 0 24 24',
		'fill'             => 'none',
		'stroke'           => 'currentColor',
		'stroke-width'     => '2',
		'stroke-linecap'   => 'round',
		'stroke-linejoin'  => 'round',
	);

	/*
	 * Une icône purement décorative doit être masquée aux lecteurs d'écran.
	 * On ne force aria-hidden que si l'appelant n'a fourni ni aria-hidden ni
	 * aria-label — sinon on écraserait une icône délibérément accessible.
	 */
	if ( ! isset( $attrs['aria-label'] ) && ! isset( $attrs['aria-hidden'] ) ) {
		$attrs['aria-hidden'] = 'true';
	}

	if ( isset( $attrs['aria-label'] ) ) {
		$attrs['role'] = 'img';
	}

	$svg_attrs = array_merge( $svg_attrs, $attrs );

	$out = '<svg';
	foreach ( $svg_attrs as $k => $v ) {
		$out .= sprintf( ' %s="%s"', esc_attr( $k ), esc_attr( $v ) );
	}
	$out .= '>';

	foreach ( $icons[ $key ] as $node ) {
		list( $tag, $node_attrs ) = $node;
		$out                     .= '<' . esc_attr( $tag );

		foreach ( $node_attrs as $k => $v ) {
			$out .= sprintf( ' %s="%s"', esc_attr( $k ), esc_attr( $v ) );
		}

		$out .= '/>';
	}

	return $out . '</svg>';
}

/**
 * Affiche une icône. Raccourci de cw_get_icon() pour les gabarits.
 *
 * @param string|null          $name  Slug Lucide.
 * @param int                  $size  Taille en px.
 * @param array<string,string> $attrs Attributs supplémentaires.
 */
function cw_icon( ?string $name, int $size = 24, array $attrs = array() ): void {
	// Le SVG est construit intégralement ici avec esc_attr() sur chaque
	// attribut : rien ne provient de l'utilisateur, l'échappement est fait.
	echo cw_get_icon( $name, $size, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
