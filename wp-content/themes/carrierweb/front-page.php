<?php
/**
 * Page d'accueil
 *
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" role="main">
	<?php
	$cw_sections = array(
		'hero',
		'trust-bar',
		'video-showcase',
		'services',
		'why',
		'sectors',
		'dashboard-preview',
		'partners',
		'testimonials',
		'faq',
		'cta',
	);

	foreach ( $cw_sections as $cw_section ) {
		get_template_part( 'template-parts/home/' . $cw_section );
	}
	?>
</main>

<?php
get_footer();
