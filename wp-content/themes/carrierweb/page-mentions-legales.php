<?php
/**
 * Mentions légales — /mentions-legales
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part(
	'template-parts/blocks/legal-page',
	null,
	array(
		'key'   => 'mentions-legales',
		'title' => (string) cw_t( 'footer.legal' ),
	)
);

get_footer();
