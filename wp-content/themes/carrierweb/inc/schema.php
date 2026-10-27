<?php
/**
 * Données structurées JSON-LD
 *
 * @id : Google les rattache ainsi les unes aux autres au lieu de les traiter
 * @package CarrierWeb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche un bloc JSON-LD.
 *
 * JSON_UNESCAPED_UNICODE : sans lui l'arabe sortirait en séquences \uXXXX —
 * valide, mais illisible et inutilement volumineux.
 *
 * @param array<string,mixed> $data Graphe.
 */
function cw_json_ld( array $data ): void {
	$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	if ( false === $json ) {
		return;
	}

	// wp_json_encode échappe déjà ; on neutralise seulement </script>.
	echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . '</script>' . "\n";
}

/** Coordonnées, mutualisées par plusieurs schémas. */
function cw_schema_address(): array {
	return array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => cw_setting( 'contactAddress', 'contact.addressValue', '39 rue Normandie, 20000 Casablanca, Maroc' ),
		'addressCountry'  => 'MA',
	);
}

/** Organization — l'entité racine référencée par les autres. */
function cw_schema_organization(): array {
	return array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Organization',
		'@id'         => CW_SITE_URL . '/#organization',
		'name'        => 'CarrierWeb Maroc',
		'legalName'   => 'CarrierWeb Maroc SARL AU',
		'url'         => CW_SITE_URL,
		'logo'        => cw_media_url( 'logo', CW_URI . '/assets/img/logo.png' ),
		'description' => 'CarrierWeb Maroc fournit des solutions de gestion de flotte, de télématique et de suivi GPS en temps réel pour le transport routier au Maroc et en Afrique.',
		'address'     => cw_schema_address(),
		'contactPoint' => array(
			'@type'             => 'ContactPoint',
			'telephone'         => cw_setting( 'contactPhone', '', '+212-5-22-36-19-88' ),
			'email'             => cw_setting( 'contactEmail', '', 'info@carrierweb.ma' ),
			'contactType'       => 'customer service',
			'availableLanguage' => array( 'French', 'Arabic', 'English' ),
			'areaServed'        => array( 'MA', 'DZ', 'TN', 'SN', 'CI' ),
		),
		'sameAs'             => array( 'https://www.carrierweb.com' ),
		'parentOrganization' => array(
			'@type' => 'Organization',
			'name'  => 'CarrierWeb Group of Companies',
			'url'   => 'https://www.carrierweb.com',
		),
	);
}

/** LocalBusiness — agence de Casablanca. */
function cw_schema_local_business(): array {
	return array(
		'@context'   => 'https://schema.org',
		'@type'      => 'LocalBusiness',
		'@id'        => CW_SITE_URL . '/#localbusiness',
		'name'       => 'CarrierWeb Maroc',
		'image'      => cw_media_url( 'social_image', CW_URI . '/assets/img/og-image.png' ),
		'url'        => CW_SITE_URL,
		'telephone'  => cw_setting( 'contactPhone', '', '+212-5-22-36-19-88' ),
		'email'      => cw_setting( 'contactEmail', '', 'info@carrierweb.ma' ),
		'priceRange' => '$$',
		'address'    => cw_schema_address(),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
				'opens'     => '09:00',
				'closes'    => '18:00',
			),
		),
		'parentOrganization' => array( '@id' => CW_SITE_URL . '/#organization' ),
	);
}

/** WebSite. */
function cw_schema_website(): array {
	return array(
		'@context'   => 'https://schema.org',
		'@type'      => 'WebSite',
		'@id'        => CW_SITE_URL . '/#website',
		'name'       => 'CarrierWeb Maroc',
		'url'        => CW_SITE_URL . cw_path( '/' ),
		'inLanguage' => cw_html_lang(),
		'publisher'  => array( '@id' => CW_SITE_URL . '/#organization' ),
	);
}

/** Service + catalogue des 6 solutions (accueil). */
function cw_schema_service(): array {
	$offers = array();

	foreach ( cw_get_solutions() as $solution ) {
		$offers[] = array(
			'@type'       => 'Offer',
			'itemOffered' => array(
				'@type' => 'Service',
				'name'  => $solution['title'],
				'url'   => $solution['permalink'],
			),
		);
	}

	return array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'serviceType' => (string) cw_t( 'meta.solutions.title' ),
		'provider'    => array( '@id' => CW_SITE_URL . '/#organization' ),
		'areaServed'  => array(
			'@type' => 'Country',
			'name'  => 'Morocco',
		),
		'description'    => (string) cw_t( 'meta.ogDescription' ),
		'hasOfferCatalog' => array(
			'@type'           => 'OfferCatalog',
			'name'            => 'Solutions CarrierWeb',
			'itemListElement' => $offers,
		),
	);
}

/**
 * FAQPage.
 *
 * @param array<int,array{q:string,a:string}> $items Questions/réponses.
 */
function cw_schema_faq( array $items ): array {
	$entities = array();

	foreach ( $items as $item ) {
		if ( empty( $item['q'] ) || empty( $item['a'] ) ) {
			continue;
		}

		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $item['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['a'],
			),
		);
	}

	return array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'inLanguage' => cw_html_lang(),
		'mainEntity' => $entities,
	);
}

/**
 * BreadcrumbList.
 *
 * @param array<int,array{name:string,url:string}> $trail Fil d'Ariane.
 */
function cw_schema_breadcrumb( array $trail ): array {
	$items = array();

	foreach ( $trail as $i => $step ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $step['name'],
			'item'     => $step['url'],
		);
	}

	return array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);
}

/**
 * Émission des schémas selon le contexte.
 *
 * Priorité 20 : après le bloc meta de inc/seo.php.
 */
add_action(
	'wp_head',
	function () {
		$home = cw_url( '/' );

		// ── Accueil : entités globales + catalogue + FAQ ──
		if ( is_front_page() ) {
			cw_json_ld( cw_schema_organization() );
			cw_json_ld( cw_schema_local_business() );
			cw_json_ld( cw_schema_website() );
			cw_json_ld( cw_schema_service() );

			$faqs = cw_home_list( 'faq', array( 'q', 'a' ), 4, 'homeFaq.items' );

			if ( ! empty( $faqs ) ) {
				cw_json_ld( cw_schema_faq( $faqs ) );
			}

			return;
		}

		// ── Solution : Service + fil d'Ariane + FAQ ──
		if ( is_singular( 'solution' ) ) {
			$solution = cw_get_solution( get_the_ID() );

			cw_json_ld(
				array(
					'@context'    => 'https://schema.org',
					'@type'       => 'Service',
					'name'        => $solution['title'],
					'description' => $solution['tagline'],
					'serviceType' => $solution['title'],
					'provider'    => array( '@id' => CW_SITE_URL . '/#organization' ),
					'areaServed'  => array(
						'@type' => 'Country',
						'name'  => 'Morocco',
					),
					'url'         => $solution['permalink'],
				)
			);

			cw_json_ld(
				cw_schema_breadcrumb(
					array(
						array(
							'name' => (string) cw_t( 'common.home' ),
							'url'  => $home,
						),
						array(
							'name' => (string) cw_t( 'header.nav.solutions' ),
							'url'  => get_post_type_archive_link( 'solution' ),
						),
						array(
							'name' => $solution['title'],
							'url'  => $solution['permalink'],
						),
					)
				)
			);

			if ( ! empty( $solution['faq'] ) ) {
				$faqs = array_map(
					static fn( $f ) => array(
						'q' => $f['question'],
						'a' => $f['answer'],
					),
					$solution['faq']
				);

				cw_json_ld( cw_schema_faq( $faqs ) );
			}

			return;
		}

		// ── Secteur : fil d'Ariane ──
		if ( is_singular( 'secteur' ) ) {
			$sector = cw_get_sector( get_the_ID() );

			cw_json_ld(
				cw_schema_breadcrumb(
					array(
						array(
							'name' => (string) cw_t( 'common.home' ),
							'url'  => $home,
						),
						array(
							'name' => (string) cw_t( 'header.nav.sectors' ),
							'url'  => get_post_type_archive_link( 'secteur' ),
						),
						array(
							'name' => $sector['title'],
							'url'  => $sector['permalink'],
						),
					)
				)
			);

			return;
		}

		// ── Article : Article + fil d'Ariane ──
		if ( is_singular( 'article' ) ) {
			$article = cw_get_article( get_the_ID() );

			cw_json_ld(
				array(
					'@context'         => 'https://schema.org',
					'@type'            => 'Article',
					'headline'         => $article['title'],
					'description'      => $article['metaDescription'],
					'inLanguage'       => cw_html_lang(),
					'datePublished'    => $article['publishedDate'],
					'dateModified'     => get_the_modified_date( 'c' ),
					'author'           => array( '@id' => CW_SITE_URL . '/#organization' ),
					'publisher'        => array( '@id' => CW_SITE_URL . '/#organization' ),
					'mainEntityOfPage' => array(
						'@type' => 'WebPage',
						'@id'   => $article['permalink'],
					),
				)
			);

			cw_json_ld(
				cw_schema_breadcrumb(
					array(
						array(
							'name' => (string) cw_t( 'common.home' ),
							'url'  => $home,
						),
						array(
							'name' => (string) cw_t( 'resources.breadcrumb' ),
							'url'  => get_post_type_archive_link( 'article' ),
						),
						array(
							'name' => $article['title'],
							'url'  => $article['permalink'],
						),
					)
				)
			);
		}
	},
	20
);
