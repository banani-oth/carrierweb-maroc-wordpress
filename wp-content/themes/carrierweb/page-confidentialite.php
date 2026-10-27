<?php
/**
 * Politique de confidentialité — /confidentialite
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
		'key'   => 'confidentialite',
		'title' => (string) cw_t( 'footer.privacy' ),
	)
);

get_footer();
