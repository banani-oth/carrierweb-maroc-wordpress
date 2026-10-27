<?php
/**
 * Plugin Name: CarrierWeb — Témoignages
 * Description: CPT `temoignage` + champs ACF Free exposés à WPGraphQL pour rendre
 *              les avis clients de l'accueil éditables. Auto-seed des 3 avis
 *              actuels au premier chargement. Même modèle que cw-reglages.php.
 */

if (!defined('ABSPATH')) exit;

// 1) CPT `temoignage` exposé à WPGraphQL.
add_action('init', function () {
    register_post_type('temoignage', [
        'label'               => 'Témoignages',
        'public'              => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-format-quote',
        'supports'            => ['title', 'custom-fields', 'page-attributes'], // page-attributes = champ « Ordre »
        'has_archive'         => false,
        'show_in_graphql'     => true,
        'graphql_single_name' => 'temoignage',
        'graphql_plural_name' => 'temoignages',
    ]);
});

// 2) Field group ACF Free (Text/Textarea) exposé à WPGraphQL.
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $field = function ($key, $name, $label, $type = 'text') {
        return [
            'key'   => 'field_cwtem_' . $key,
            'label' => $label,
            'name'  => $name,
            'type'  => $type,
            'show_in_graphql' => 1,
        ];
    };

    acf_add_local_field_group([
        'key'      => 'group_cw_temoignages',
        'title'    => 'Témoignage',
        'fields'   => [
            $field('quote',   'quote',   'Citation', 'textarea'),
            $field('author',  'author',  'Auteur (fonction, ex. « Directeur d’Exploitation »)'),
            $field('role',    'role',    'Secteur / activité (ex. « Transport routier de marchandises »)'),
            $field('company', 'company', 'Ville (ex. « Casablanca »)'),
        ],
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'temoignage',
        ]]],
        'show_in_graphql'     => 1,
        'graphql_field_name'  => 'temoignageFields',
        'map_graphql_types_from_location_rules' => 0,
    ]);
});

// 3) Auto-seed des 3 avis actuels. Sur `wp_loaded` : tout est enregistré
//    (CPT sur `init`, field groups ACF sur `acf/init`), et wp_insert_post est
//    pleinement opérationnel — pas d'ambiguïté de timing comme sur `acf/init`.
//    Idempotent et auto-réparant : on ne seed QUE si aucun témoignage n'existe.
add_action('wp_loaded', function () {
    if (!post_type_exists('temoignage')) return;
    if (!function_exists('update_field')) return;

    $existing = get_posts([
        'post_type'   => 'temoignage',
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);
    if (!empty($existing)) return; // déjà des témoignages → on ne touche à rien

    $seed = [
        [
            'title'   => 'Directeur d’Exploitation — Casablanca',
            'quote'   => "Les sondes carburant ont mis fin aux écarts inexpliqués entre le gazole acheté et les kilomètres parcourus. En trois mois, nous avons identifié deux cas de siphonnage et réduit sensiblement notre budget carburant.",
            'author'  => "Directeur d’Exploitation",
            'role'    => "Transport routier de marchandises",
            'company' => "Casablanca",
        ],
        [
            'title'   => 'Responsable Qualité — Agadir',
            'quote'   => "Avec le ReeferMate™, chaque livraison frigorifique part avec son rapport de température certifié. Nos clients de la grande distribution ne contestent plus la chaîne du froid, et les audits ONSSA se passent sans stress.",
            'author'  => "Responsable Qualité",
            'role'    => "Logistique frigorifique",
            'company' => "Agadir",
        ],
        [
            'title'   => 'Directeur des Systèmes d’Information — Tanger',
            'quote'   => "L'API CarrierWeb alimente directement notre TMS : positions, kilométrages et statuts de livraison remontent sans double saisie. Notre exploitation et notre facturation travaillent enfin sur les mêmes chiffres.",
            'author'  => "Directeur des Systèmes d'Information",
            'role'    => "Supply chain & distribution",
            'company' => "Tanger",
        ],
    ];

    foreach ($seed as $i => $row) {
        $id = wp_insert_post([
            'post_type'   => 'temoignage',
            'post_status' => 'publish',
            'post_title'  => $row['title'],
            'menu_order'  => $i + 1, // conserve l'ordre éditorial
        ]);
        if ($id && !is_wp_error($id)) {
            update_field('field_cwtem_quote',   $row['quote'],   $id);
            update_field('field_cwtem_author',  $row['author'],  $id);
            update_field('field_cwtem_role',    $row['role'],    $id);
            update_field('field_cwtem_company', $row['company'], $id);
        }
    }
});
