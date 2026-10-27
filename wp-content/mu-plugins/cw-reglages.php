<?php
/**
 * Plugin Name: CarrierWeb — Réglages du site
 * Description: Valeurs initiales des coordonnées et de la présentation du site.
 */

if (!defined('ABSPATH')) exit;

// 1) CPT `reglage` exposé à WPGraphQL.
add_action('init', function () {
    register_post_type('reglage', [
        'label'               => 'Réglages',
        'public'              => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-admin-settings',
        'supports'            => ['title', 'custom-fields'],
        'has_archive'         => false,
        'show_in_graphql'     => true,
        'graphql_single_name' => 'reglage',
        'graphql_plural_name' => 'reglages',
    ]);
});

// 2) Field group ACF Free (Text/Textarea uniquement) exposé à WPGraphQL.
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $text = function ($key, $name, $label, $type = 'text') {
        return [
            'key'   => 'field_cwreg_' . $key,
            'label' => $label,
            'name'  => $name,
            'type'  => $type,
            'show_in_graphql' => 1,
        ];
    };

    acf_add_local_field_group([
        'key'      => 'group_cw_reglages',
        'title'    => 'Réglages du site',
        'fields'   => [
            $text('footerblurb', 'footerBlurb', 'Footer — texte de présentation', 'textarea'),
            $text('phone', 'contactPhone', 'Téléphone'),
            $text('email', 'contactEmail', 'E-mail'),
            $text('address', 'contactAddress', 'Adresse'),
            $text('linkedin', 'linkedinUrl', 'URL LinkedIn'),
            $text('h1', 'heroTitleLine1', 'Accueil — titre ligne 1'),
            $text('hhl', 'heroTitleHighlight', 'Accueil — mot mis en avant (rouge)'),
            $text('h3', 'heroTitleLine3', 'Accueil — titre ligne 3'),
            $text('hsub', 'heroSubtitle', 'Accueil — sous-titre', 'textarea'),
        ],
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'reglage',
        ]]],
        'show_in_graphql'     => 1,
        'graphql_field_name'  => 'reglagesFields',
        'map_graphql_types_from_location_rules' => 0,
    ]);
});

// 3) Crée le post unique « reglages-site » une seule fois (données de départ).
//    Sur `acf/init` priorité 20 (APRÈS l'enregistrement du field group en 10)
//    pour que les clés de champ soient connues d'update_field().
add_action('acf/init', function () {
    if (get_option('cw_reglages_seeded_v2')) return;
    if (!post_type_exists('reglage')) return;

    $existing = get_page_by_path('reglages-site', OBJECT, 'reglage');
    $id = $existing ? $existing->ID : wp_insert_post([
        'post_type'   => 'reglage',
        'post_status' => 'publish',
        'post_title'  => 'Réglages du site',
        'post_name'   => 'reglages-site',
    ]);

    if ($id && !is_wp_error($id) && function_exists('update_field')) {
        // Valeurs de départ = celles du site (le front retombe dessus de toute
        // façon si un champ est vidé). Patricia les modifie ensuite dans l'admin.
        update_field('field_cwreg_footerblurb', "Solutions de télématique embarquée avancée, géolocalisation et gestion de carburant pour les flottes de transport au Maroc.", $id);
        update_field('field_cwreg_phone', '+212 (0)5 22 36 19 88', $id);
        update_field('field_cwreg_email', 'info@carrierweb.ma', $id);
        update_field('field_cwreg_address', '39 rue Normandie, 20000 Casablanca, Maroc', $id);
        update_field('field_cwreg_linkedin', 'https://www.linkedin.com/company/carrierweb', $id);
        update_field('field_cwreg_h1', 'Pilotez votre flotte', $id);
        update_field('field_cwreg_hhl', 'temps réel', $id);
        update_field('field_cwreg_h3', 'Réduisez vos coûts', $id);
        update_field('field_cwreg_hsub', "Suivi GPS, contrôle du carburant, chaîne du froid certifiée et communication chauffeurs : CarrierWeb équipe les transporteurs et logisticiens marocains, de Casablanca à Tanger Med.", $id);
    }

    update_option('cw_reglages_seeded_v2', 1);
}, 20);

// Settings records are kept separately for each language. No automatic deletion.
