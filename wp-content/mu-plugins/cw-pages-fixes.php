<?php
/**
 * Plugin Name: CarrierWeb — Textes des pages
 * Description: Champs ACF et contenu initial des pages fixes. Les valeurs existantes sont conservées.
 */

if (!defined('ABSPATH')) exit;

// ── 1) CPTs — un par page, chacun visible dans la barre latérale ─────────
add_action('init', function () {
    $pages = [
        ['page_accueil',    'Page : Accueil',       'pageAccueil',    'pagesAccueil',    'dashicons-admin-home',     21],
        ['page_pourquoi',   'Page : Pourquoi nous', 'pagePourquoi',   'pagesPourquoi',   'dashicons-lightbulb',      22],
        ['page_references', 'Page : Références',    'pageReferences', 'pagesReferences', 'dashicons-location',       23],
        ['page_ressources', 'Page : Ressources',    'pageRessources', 'pagesRessources', 'dashicons-book',           24],
        ['page_contact',    'Page : Contact',       'pageContact',    'pagesContact',    'dashicons-email',          25],
    ];
    foreach ($pages as [$slug, $label, $single, $plural, $icon, $pos]) {
        register_post_type($slug, [
            'label'               => $label,
            'public'              => true,
            'publicly_queryable'  => false, // pas de page front WP ; lecture via GraphQL
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => $icon,
            'menu_position'       => $pos,
            'supports'            => ['title'],
            'has_archive'         => false,
            'show_in_graphql'     => true,
            'graphql_single_name' => $single,
            'graphql_plural_name' => $plural,
        ]);
    }
});

// ── 2) Groupes de champs ACF Free (Text/Textarea + onglets) ─────────────
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $mk = function (string $prefix) {
        return [
            'f' => function ($name, $label, $type = 'text') use ($prefix) {
                return [
                    'key'             => "field_{$prefix}_{$name}",
                    'label'           => $label,
                    'name'            => $name,
                    'type'            => $type,
                    'show_in_graphql' => 1,
                ];
            },
            'tab' => function ($label) use ($prefix) {
                static $i = 0;
                $i++;
                return [
                    'key'             => "field_{$prefix}_tab{$i}",
                    'label'           => $label,
                    'type'            => 'tab',
                    'show_in_graphql' => 0,
                ];
            },
        ];
    };

    $group = function ($key, $title, $cpt, $graphqlName, $fields) {
        acf_add_local_field_group([
            'key'      => $key,
            'title'    => $title,
            'fields'   => $fields,
            'location' => [[[
                'param'    => 'post_type',
                'operator' => '==',
                'value'    => $cpt,
            ]]],
            'show_in_graphql'     => 1,
            'graphql_field_name'  => $graphqlName,
            'map_graphql_types_from_location_rules' => 0,
        ]);
    };

    // ---- Page : Pourquoi nous --------------------------------------------
    ['f' => $f, 'tab' => $tab] = $mk('cwpq');
    $group('group_cw_page_pourquoi', 'Page : Pourquoi nous — textes', 'page_pourquoi', 'pourquoiFields', [
        $tab('En-tête'),
        $f('label',   'Étiquette (au-dessus du titre)'),
        $f('h1',      'Titre principal'),
        $f('tagline', 'Phrase d’accroche', 'textarea'),
        $tab('Spécifications (4 cartes)'),
        $f('specs_title', 'Titre de la section'),
        $f('specs_desc',  'Sous-titre de la section'),
        $f('spec_1_title', 'Carte 1 — titre'), $f('spec_1_desc', 'Carte 1 — texte', 'textarea'),
        $f('spec_2_title', 'Carte 2 — titre'), $f('spec_2_desc', 'Carte 2 — texte', 'textarea'),
        $f('spec_3_title', 'Carte 3 — titre'), $f('spec_3_desc', 'Carte 3 — texte', 'textarea'),
        $f('spec_4_title', 'Carte 4 — titre'), $f('spec_4_desc', 'Carte 4 — texte', 'textarea'),
        $tab('Performance & Précision'),
        $f('perf_title', 'Titre du bloc'),
        $f('perf_p1', 'Paragraphe 1', 'textarea'),
        $f('perf_p2', 'Paragraphe 2', 'textarea'),
        $f('check_1', 'Point ✓ 1'), $f('check_2', 'Point ✓ 2'), $f('check_3', 'Point ✓ 3'),
        $tab('Encadré latéral'),
        $f('side_title', 'Titre de l’encadré'),
        $f('side_1_title', 'Bloc 1 — titre'), $f('side_1_desc', 'Bloc 1 — texte', 'textarea'),
        $f('side_2_title', 'Bloc 2 — titre'), $f('side_2_desc', 'Bloc 2 — texte', 'textarea'),
        $f('side_3_title', 'Bloc 3 — titre'), $f('side_3_desc', 'Bloc 3 — texte', 'textarea'),
    ]);

    // ---- Page : Références ------------------------------------------------
    ['f' => $f, 'tab' => $tab] = $mk('cwref');
    $group('group_cw_page_references', 'Page : Références — textes', 'page_references', 'referencesFields', [
        $tab('En-tête'),
        $f('label',   'Étiquette (au-dessus du titre)'),
        $f('h1',      'Titre principal'),
        $f('tagline', 'Phrase d’accroche', 'textarea'),
        $tab('Chiffres clés (3 cartes)'),
        $f('metric_1_val', 'Carte 1 — chiffre'), $f('metric_1_label', 'Carte 1 — libellé'), $f('metric_1_desc', 'Carte 1 — texte', 'textarea'),
        $f('metric_2_val', 'Carte 2 — chiffre'), $f('metric_2_label', 'Carte 2 — libellé'), $f('metric_2_desc', 'Carte 2 — texte', 'textarea'),
        $f('metric_3_val', 'Carte 3 — chiffre'), $f('metric_3_label', 'Carte 3 — libellé'), $f('metric_3_desc', 'Carte 3 — texte', 'textarea'),
        $tab('Implantation'),
        $f('presence_title', 'Titre du bloc'),
        $f('presence_p1', 'Paragraphe 1', 'textarea'),
        $f('presence_p2', 'Paragraphe 2', 'textarea'),
        $f('check_1', 'Point ✓ 1'), $f('check_2', 'Point ✓ 2'), $f('check_3', 'Point ✓ 3'), $f('check_4', 'Point ✓ 4'),
        $tab('Encadré « Notre couverture »'),
        $f('side_title', 'Titre de l’encadré'),
        $f('side_1_title', 'Bloc 1 — titre'), $f('side_1_desc', 'Bloc 1 — texte', 'textarea'),
        $f('side_2_title', 'Bloc 2 — titre'), $f('side_2_desc', 'Bloc 2 — texte', 'textarea'),
        $f('side_3_title', 'Bloc 3 — titre'), $f('side_3_desc', 'Bloc 3 — texte', 'textarea'),
        $f('side_4_title', 'Bloc 4 — titre'), $f('side_4_desc', 'Bloc 4 — texte', 'textarea'),
        $tab('Références clients'),
        $f('refs_title', 'Titre de la section'),
        $f('refs_desc',  'Sous-titre de la section', 'textarea'),
    ]);

    // ---- Page : Ressources --------------------------------------------------
    ['f' => $f, 'tab' => $tab] = $mk('cwres');
    $group('group_cw_page_ressources', 'Page : Ressources — textes', 'page_ressources', 'ressourcesFields', [
        $tab('En-tête'),
        $f('label',  'Étiquette (au-dessus du titre)'),
        $f('h1_pre', 'Titre — début'),
        $f('h1_hl',  'Titre — partie rouge'),
        $f('desc',   'Phrase d’introduction', 'textarea'),
        $tab('Newsletter'),
        $f('newsletter_title', 'Titre du bloc newsletter'),
        $f('newsletter_desc',  'Texte du bloc newsletter', 'textarea'),
        $f('newsletter_cta',   'Texte du bouton'),
    ]);

    // ---- Page : Contact ------------------------------------------------------
    ['f' => $f, 'tab' => $tab] = $mk('cwcon');
    $group('group_cw_page_contact', 'Page : Contact — textes', 'page_contact', 'contactFields', [
        $tab('En-tête'),
        $f('label',  'Étiquette (au-dessus du titre)'),
        $f('h1_pre', 'Titre — début'),
        $f('h1_hl',  'Titre — partie rouge'),
        $f('desc',   'Phrase d’introduction', 'textarea'),
        $tab('Coordonnées & disponibilité'),
        $f('address_value', 'Adresse affichée'),
        $f('availability_1', 'Disponibilité — ligne 1'),
        $f('availability_2', 'Disponibilité — ligne 2'),
        $tab('Encadrés'),
        $f('client_box_title', 'Encadré client — titre'),
        $f('client_box_desc',  'Encadré client — texte', 'textarea'),
        $f('wa_title', 'Encadré WhatsApp — titre'),
        $f('wa_desc',  'Encadré WhatsApp — texte', 'textarea'),
        $f('form_title', 'Titre du formulaire'),
    ]);

    // ---- Page : Accueil -------------------------------------------------------
    // (Le bandeau « hero » et le pied de page restent dans « Réglages » ;
    //  les témoignages dans « Témoignages » ; solutions/secteurs dans leurs CPT.)
    ['f' => $f, 'tab' => $tab] = $mk('cwacc');
    $group('group_cw_page_accueil', 'Page : Accueil — textes des sections', 'page_accueil', 'accueilFields', [
        $tab('Section Solutions'),
        $f('services_label',     'Étiquette'),
        $f('services_title_pre', 'Titre — début'),
        $f('services_title_hl',  'Titre — partie rouge'),
        $f('services_desc',      'Texte d’introduction', 'textarea'),
        $tab('Section Pourquoi CarrierWeb'),
        $f('why_label',     'Étiquette'),
        $f('why_title_pre', 'Titre — début'),
        $f('why_title_hl',  'Titre — partie rouge'),
        $f('why_paragraph', 'Paragraphe', 'textarea'),
        $f('why_feature_1_title', 'Atout 1 — titre'), $f('why_feature_1_desc', 'Atout 1 — texte', 'textarea'),
        $f('why_feature_2_title', 'Atout 2 — titre'), $f('why_feature_2_desc', 'Atout 2 — texte', 'textarea'),
        $f('why_feature_3_title', 'Atout 3 — titre'), $f('why_feature_3_desc', 'Atout 3 — texte', 'textarea'),
        $f('why_metric_1_val', 'Chiffre 1'), $f('why_metric_1_label', 'Chiffre 1 — libellé'), $f('why_metric_1_desc', 'Chiffre 1 — texte'),
        $f('why_metric_2_val', 'Chiffre 2'), $f('why_metric_2_label', 'Chiffre 2 — libellé'), $f('why_metric_2_desc', 'Chiffre 2 — texte'),
        $f('why_metric_3_val', 'Chiffre 3'), $f('why_metric_3_label', 'Chiffre 3 — libellé'), $f('why_metric_3_desc', 'Chiffre 3 — texte'),
        $tab('Section Secteurs'),
        $f('sectors_label',     'Étiquette'),
        $f('sectors_title_pre', 'Titre — début'),
        $f('sectors_title_hl',  'Titre — partie rouge'),
        $f('sectors_desc',      'Texte d’introduction', 'textarea'),
        $tab('Bandeau partenaires'),
        $f('partners_line', 'Phrase du bandeau'),
        $tab('FAQ'),
        $f('faq_label',     'Étiquette'),
        $f('faq_title_pre', 'Titre — début'),
        $f('faq_title_hl',  'Titre — partie rouge'),
        $f('faq_desc',      'Texte d’introduction', 'textarea'),
        $f('faq_1_q', 'Question 1'), $f('faq_1_a', 'Réponse 1', 'textarea'),
        $f('faq_2_q', 'Question 2'), $f('faq_2_a', 'Réponse 2', 'textarea'),
        $f('faq_3_q', 'Question 3'), $f('faq_3_a', 'Réponse 3', 'textarea'),
        $f('faq_4_q', 'Question 4'), $f('faq_4_a', 'Réponse 4', 'textarea'),
        $tab('Bandeau final (CTA)'),
        $f('cta_label',     'Étiquette'),
        $f('cta_title_pre', 'Titre — début'),
        $f('cta_title_hl',  'Titre — partie rouge'),
        $f('cta_desc',      'Texte', 'textarea'),
        $f('cta_button',    'Texte du bouton'),
    ]);
});

// ── 3) Auto-seed : un post par page, pré-rempli avec les textes actuels ──
//     Idempotent : on ne crée le post QUE si le CPT est vide. Les valeurs

//     exactement la même chose avant et après le seed.
add_action('wp_loaded', function () {
    if (!function_exists('update_field')) return;

    $seed = function (string $cpt, string $title, string $prefix, array $values) {
        if (!post_type_exists($cpt)) return;
        $existing = get_posts(['post_type' => $cpt, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids']);
        if (!empty($existing)) return;
        $id = wp_insert_post(['post_type' => $cpt, 'post_status' => 'publish', 'post_title' => $title]);
        if (!$id || is_wp_error($id)) return;
        foreach ($values as $name => $value) {
            update_field("field_{$prefix}_{$name}", $value, $id);
        }
    };

    $seed('page_pourquoi', 'Pourquoi nous — textes de la page', 'cwpq', [
        'label' => 'MATÉRIEL & LOGICIEL',
        'h1' => 'Notre Technologie',
        'tagline' => 'Une infrastructure IoT performante et robuste pour piloter vos actifs au Maroc.',
        'specs_title' => 'Spécifications Matérielles & Réseau',
        'specs_desc' => 'Conçu pour répondre aux contraintes du transport professionnel.',
        'spec_1_title' => 'Technologie Embarquée Propriétaire',
        'spec_1_desc' => 'Ordinateurs de bord et boîtiers GPS conçus spécifiquement pour résister aux conditions routières extrêmes.',
        'spec_2_title' => 'Connectivité Multi-Opérateurs',
        'spec_2_desc' => 'Basculement automatique entre les principaux réseaux télécoms marocains pour garantir zéro zone blanche.',
        'spec_3_title' => 'Sécurité & Robustesse',
        'spec_3_desc' => 'Boîtiers renforcés et inviolables avec alertes instantanées en cas de sabotage ou de débranchement.',
        'spec_4_title' => 'Hébergement Cloud Sécurisé',
        'spec_4_desc' => 'Serveurs redondés à haute disponibilité assurant un accès en ligne 24h/24 et 7j/7.',
        'perf_title' => 'Performance & Précision',
        'perf_p1' => "Contrairement aux solutions de géolocalisation classiques basées sur de simples traceurs d'importation, CarrierWeb conçoit et développe son propre écosystème matériel (ordinateur de bord, sonde ReeferMate™) et logiciel.",
        'perf_p2' => "Cette maîtrise totale garantit une collecte de données de haute précision sur l'état du véhicule, la température des remorques frigorifiques et la consommation réelle de gazole sans perturber l'ordinateur de bord d'origine du constructeur.",
        'check_1' => 'Matériel certifié CE et homologué ANRT au Maroc',
        'check_2' => 'Mises à jour firmware automatiques et à distance (OTA)',
        'check_3' => 'Installation physique par nos techniciens qualifiés à Casablanca',
        'side_title' => 'Pourquoi CarrierWeb',
        'side_1_title' => 'Rentabilité (ROI)',
        'side_1_desc' => 'Réduisez le ralenti moteur et le budget carburant de votre flotte de 15% en moyenne.',
        'side_2_title' => 'API ouverte',
        'side_2_desc' => 'Connectez facilement nos données de trajet et température à SAP, Sage ou votre TMS.',
        'side_3_title' => 'Support de proximité',
        'side_3_desc' => 'Bénéficiez de la présence de nos équipes de techniciens d’assistance à Casablanca.',
    ]);

    $seed('page_references', 'Références — textes de la page', 'cwref', [
        'label' => 'Présence Locale',
        'h1' => 'Nos références & notre expérience au Maroc',
        'tagline' => "Plus qu'un fournisseur : un partenaire télématique implanté à Casablanca, qui connaît le terrain, les axes et les contraintes du transport marocain.",
        'metric_1_val' => '15 ans', 'metric_1_label' => "D'expérience télématique", 'metric_1_desc' => 'Au service des flottes de transport',
        'metric_2_val' => '500+', 'metric_2_label' => 'Flottes accompagnées', 'metric_2_desc' => 'Du TIR international à la distribution urbaine',
        'metric_3_val' => '24h/7', 'metric_3_label' => 'Support de proximité', 'metric_3_desc' => 'Techniciens et service client à Casablanca',
        'presence_title' => 'Une implantation réelle, pas une simple représentation',
        'presence_p1' => "CarrierWeb Maroc est la filiale marocaine du groupe CarrierWeb. Nos équipes commerciales et techniques sont basées à Casablanca : l'installation des boîtiers, le calibrage des sondes carburant et le support quotidien sont assurés localement, dans nos ateliers ou directement dans vos dépôts.",
        'presence_p2' => "Cette proximité fait la différence au quotidien : un matériel remplacé sous 24 heures, un interlocuteur qui connaît vos axes (Casablanca–Tanger Med, corridors TIR vers l'Europe) et une plateforme connectée aux réseaux des trois opérateurs marocains (IAM, Orange, Inwi) pour une couverture maximale du territoire.",
        'check_1' => "Équipes d'installation et de support basées à Casablanca",
        'check_2' => 'Interventions dans vos dépôts, partout au Maroc',
        'check_3' => 'Couverture GSM multi-opérateurs (IAM, Orange, Inwi)',
        'check_4' => 'Suivi cross-border TIR Maroc – Europe',
        'side_title' => 'Notre couverture',
        'side_1_title' => 'Casablanca — siège & ateliers',
        'side_1_desc' => 'Direction, service client et préparation du matériel au 39 rue de Normandie.',
        'side_2_title' => 'Interventions nationales',
        'side_2_desc' => 'Installations et maintenance planifiées dans vos dépôts, sur tout le territoire.',
        'side_3_title' => 'Corridors TIR & export',
        'side_3_desc' => 'Suivi continu Maroc – Europe : Tanger Med et axes espagnols et français.',
        'side_4_title' => 'Groupe CarrierWeb',
        'side_4_desc' => "L'appui d'un groupe télématique international et de sa R&D matérielle.",
        'refs_title' => 'Des références actives dans chaque secteur clé',
        'refs_desc' => 'Transport routier, chaîne du froid, supply chain : des flottes marocaines de toutes tailles utilisent CarrierWeb au quotidien.',
    ]);

    $seed('page_ressources', 'Ressources — textes de la page', 'cwres', [
        'label' => 'Ressources',
        'h1_pre' => 'Ressources & Guides ',
        'h1_hl' => 'Télématiques',
        'desc' => "Analyses sectorielles, bonnes pratiques et guides d'optimisation logistique dédiés au marché du transport au Maroc et en Afrique.",
        'newsletter_title' => 'Abonnez-vous à notre newsletter logistique',
        'newsletter_desc' => 'Recevez directement dans votre boîte mail nos prochains conseils et actualités de la gestion de flotte au Maroc.',
        'newsletter_cta' => "S'abonner par e-mail",
    ]);

    $seed('page_contact', 'Contact — textes de la page', 'cwcon', [
        'label' => 'Contact',
        'h1_pre' => 'Parlons de Votre ',
        'h1_hl' => 'Projet',
        'desc' => "Une question, besoin d'une étude de rentabilité ou d'une démonstration personnalisée gratuite de notre plateforme ? Notre équipe à Casablanca vous répond.",
        'address_value' => '39 rue Normandie, 20000 Casablanca, Maroc',
        'availability_1' => 'Support technique : 24h/24, 7j/7',
        'availability_2' => 'Bureaux commerciaux : Lundi au Samedi (09:00 - 18:00)',
        'client_box_title' => 'Déjà client CarrierWeb ?',
        'client_box_desc' => "Notre assistance technique à Casablanca se tient à votre entière disposition pour l'installation de matériel ou toute question sur la plateforme.",
        'wa_title' => 'Contact direct WhatsApp',
        'wa_desc' => 'Envoyez un message rapide à notre service commercial. Réponses sous quelques minutes.',
        'form_title' => 'Envoyer un message',
    ]);

    $seed('page_accueil', 'Accueil — textes des sections', 'cwacc', [
        'services_label' => 'Solutions Télématiques',
        'services_title_pre' => 'Quelles solutions pour ',
        'services_title_hl' => 'rentabiliser votre flotte ?',
        'services_desc' => "Six solutions connectées, du suivi GPS à l'intégration ERP, pour réduire vos coûts d'exploitation et sécuriser vos opérations de transport au Maroc.",
        'why_label' => 'Avantage Compétitif',
        'why_title_pre' => 'La référence IoT pour le ',
        'why_title_hl' => 'Transport Routier',
        'why_paragraph' => 'Depuis 15 ans, CarrierWeb accompagne les plus grandes flottes marocaines de Casablanca, Tanger et Agadir avec une infrastructure technologique propriétaire, robuste et performante.',
        'why_feature_1_title' => 'Surveillance 24/7 de vos actifs',
        'why_feature_1_desc' => 'Visualisez à tout moment la position, la vitesse et le statut opérationnel de chaque véhicule grâce au réseau GSM multi-opérateurs.',
        'why_feature_2_title' => 'Réduction drastique des dépenses',
        'why_feature_2_desc' => 'Éliminez les siphonnages de carburant, réduisez les kilomètres superflus et diminuez les temps d’arrêt moteur excessifs.',
        'why_feature_3_title' => 'Rapports & analyses décisionnels',
        'why_feature_3_desc' => 'Générez des rapports automatiques configurés pour vos services d’exploitation, de maintenance et de comptabilité.',
        'why_metric_1_val' => '−25%', 'why_metric_1_label' => 'De Budget Carburant', 'why_metric_1_desc' => 'Grâce à notre sonde de réservoir brevetée',
        'why_metric_2_val' => '500+', 'why_metric_2_label' => 'Flottes au Maroc', 'why_metric_2_desc' => 'Entreprises partenaires qui nous font confiance',
        'why_metric_3_val' => '24h/7', 'why_metric_3_label' => 'Support Proximité', 'why_metric_3_desc' => 'Équipes techniques basées à Casablanca',
        'sectors_label' => 'Marchés Cibles',
        'sectors_title_pre' => 'Quelle réponse pour votre ',
        'sectors_title_hl' => "Secteur d'Activité ?",
        'sectors_desc' => 'Transport routier, logistique, BTP ou distribution : chaque métier a ses contraintes, nos modules télématiques et alertes sont configurés pour y répondre.',
        'partners_line' => 'Boîtiers compatibles avec toutes les marques de camions et utilitaires',
        'faq_label' => 'Questions Fréquentes',
        'faq_title_pre' => "Besoin d'un ",
        'faq_title_hl' => 'Éclaircissement ?',
        'faq_desc' => 'Retrouvez les réponses aux questions les plus courantes sur notre technologie et nos services d’accompagnement au Maroc.',
        'faq_1_q' => 'Comment optimiser la consommation de carburant de sa flotte au Maroc ?',
        'faq_1_a' => "CarrierWeb installe des sondes capacitives calibrées de précision dans les réservoirs de vos camions. Reliées à l'ordinateur de bord, elles comparent la consommation théorique à la consommation réelle, détectent instantanément les siphonnages (alertes SMS en moins de 30s) et tracent les heures de ralenti moteur excessif.",
        'faq_2_q' => "Quel est le coût d'un système de géolocalisation GPS pour camion au Maroc ?",
        'faq_2_a' => "Le tarif est basé sur un abonnement mensuel par véhicule connecté qui comprend la location du matériel, l'accès à la plateforme en ligne illimitée, les alertes et le support technique. L'installation physique est réalisée par nos équipes à Casablanca ou directement dans vos dépôts.",
        'faq_3_q' => 'CarrierWeb est-il compatible avec les règles sanitaires de l’ONSSA ?',
        'faq_3_a' => 'Oui, notre boîtier ReeferMate™ est homologué et certifié conforme EN 12830 / HACCP. Il transmet en continu la température de vos remorques et génère des graphiques PDF certifiés infalsifiables, parfaits pour les audits réglementaires et l’exportation.',
        'faq_4_q' => 'Comment fonctionne le support technique et local au Maroc ?',
        'faq_4_a' => 'Notre service client et nos techniciens d’installation sont basés à Casablanca. Nous offrons une assistance téléphonique disponible 24h/24 et 7j/7 pour les urgences d’exploitation ainsi qu’une garantie matérielle totale avec remplacement sous 24 heures.',
        'cta_label' => 'Prêt à démarrer ?',
        'cta_title_pre' => 'Optimisez les opérations de votre flotte ',
        'cta_title_hl' => 'Dès Aujourd’hui',
        'cta_desc' => 'Rejoignez les leaders du transport et de la logistique au Maroc qui font confiance à CarrierWeb pour piloter, sécuriser et rentabiliser leur parc.',
        'cta_button' => 'Demander une Démo Gratuite',
    ]);

    // Nettoyage ponctuel : la page Gutenberg « pourquoi-carrierweb » d'un
    // essai précédent n'est plus lue par le front → corbeille (récupérable).
    if (!get_option('cw_cleanup_gutenberg_page')) {
        $old = get_page_by_path('pourquoi-carrierweb', OBJECT, 'page');
        if ($old) wp_trash_post($old->ID);
        update_option('cw_cleanup_gutenberg_page', 1);
    }
});
