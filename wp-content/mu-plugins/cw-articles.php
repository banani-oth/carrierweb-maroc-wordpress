<?php
/**
 * Plugin Name: CarrierWeb — Articles (Ressources)
 */

if (!defined('ABSPATH')) exit;

// 1) CPT `article` exposé à WPGraphQL.
add_action('init', function () {
    register_post_type('article', [
        'label'               => 'Articles',
        'public'              => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-media-document',
        'supports'            => ['title', 'custom-fields', 'page-attributes'],
        'has_archive'         => false,
        'rewrite'             => ['slug' => 'article'],
        'show_in_graphql'     => true,
        'graphql_single_name' => 'article',
        'graphql_plural_name' => 'articles',
    ]);
});

// 2) Field group ACF Free exposé à WPGraphQL.
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $field = function ($key, $name, $label, $type = 'text', $extra = []) {
        return array_merge([
            'key'   => 'field_cwart_' . $key,
            'label' => $label,
            'name'  => $name,
            'type'  => $type,
            'show_in_graphql' => 1,
        ], $extra);
    };

    $fields = [
        $field('category',  'category',        'Catégorie (ex. « Conseils Utiles »)'),
        $field('excerpt',   'excerpt',         'Extrait (carte + partage)', 'textarea'),
        $field('metadesc',  'metaDescription', 'Meta description (SEO)', 'textarea'),
        $field('date',      'publishedDate',   'Date de publication (AAAA-MM-JJ)'),
        $field('minutes',   'readingMinutes',  'Durée de lecture (minutes)', 'number'),
        $field('intro',     'intro',           'Introduction (chapô)', 'textarea'),
    ];

    // Jusqu'à 6 sections.
    for ($i = 1; $i <= 6; $i++) {
        $fields[] = $field("s{$i}h", "section{$i}Heading",    "Section {$i} — titre");
        $fields[] = $field("s{$i}p", "section{$i}Paragraphs", "Section {$i} — paragraphes (séparés par une ligne vide)", 'textarea', ['rows' => 6]);
        $fields[] = $field("s{$i}b", "section{$i}Bullets",    "Section {$i} — puces (une par ligne, laisser vide si aucune)", 'textarea', ['rows' => 4]);
    }

    $fields[] = $field('takeaways', 'keyTakeaways', 'À retenir (une idée par ligne)', 'textarea', ['rows' => 6]);

    // Jusqu'à 3 solutions liées (titre + lien).
    for ($i = 1; $i <= 3; $i++) {
        $fields[] = $field("r{$i}t", "related{$i}Title", "Solution liée {$i} — titre");
        $fields[] = $field("r{$i}h", "related{$i}Href",  "Solution liée {$i} — lien (ex. /solutions/gestion-carburant)");
    }

    acf_add_local_field_group([
        'key'      => 'group_cw_articles',
        'title'    => 'Article',
        'fields'   => $fields,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'article',
        ]]],
        'show_in_graphql'     => 1,
        'graphql_field_name'  => 'articleFields',
        'map_graphql_types_from_location_rules' => 0,
    ]);
});

// 3) Auto-seed des 3 articles actuels. Sur `wp_loaded` (tout est enregistré,
//    wp_insert_post opérationnel). Idempotent : on ne seed QUE si aucun article.
add_action('wp_loaded', function () {
    if (!post_type_exists('article')) return;
    if (!function_exists('update_field')) return;

    $existing = get_posts([
        'post_type'   => 'article',
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);
    if (!empty($existing)) return; // déjà des articles → on ne touche à rien

    $articles = cw_articles_seed_data();

    foreach ($articles as $i => $a) {
        $id = wp_insert_post([
            'post_type'   => 'article',
            'post_status' => 'publish',
            'post_title'  => $a['title'],
            'post_name'   => $a['slug'],
            'menu_order'  => $i + 1,
        ]);
        if (!$id || is_wp_error($id)) continue;

        update_field('field_cwart_category', $a['category'], $id);
        update_field('field_cwart_excerpt',  $a['excerpt'], $id);
        update_field('field_cwart_metadesc', $a['metaDescription'], $id);
        update_field('field_cwart_date',     $a['publishedAt'], $id);
        update_field('field_cwart_minutes',  $a['readingMinutes'], $id);
        update_field('field_cwart_intro',    $a['intro'], $id);

        foreach ($a['sections'] as $s => $section) {
            $n = $s + 1;
            update_field("field_cwart_s{$n}h", $section['heading'], $id);
            update_field("field_cwart_s{$n}p", implode("\n\n", $section['paragraphs']), $id);
            update_field("field_cwart_s{$n}b", isset($section['bullets']) ? implode("\n", $section['bullets']) : '', $id);
        }

        update_field('field_cwart_takeaways', implode("\n", $a['keyTakeaways']), $id);

        foreach ($a['relatedSolutions'] as $r => $rel) {
            $n = $r + 1;
            update_field("field_cwart_r{$n}t", $rel['title'], $id);
            update_field("field_cwart_r{$n}h", $rel['href'], $id);
        }
    }
});


function cw_articles_seed_data() {
    return [
        [
            'slug'  => 'reduire-consommation-carburant-camions',
            'title' => 'Comment réduire la consommation de carburant de vos camions ?',
            'category' => 'Conseils Utiles',
            'excerpt' => <<<'TXT'
Le carburant est le premier poste de dépense des transporteurs au Maroc. Éco-conduite, détection des siphonnages, suivi du ralenti moteur : les leviers concrets d'économie.
TXT,
            'metaDescription' => <<<'TXT'
Guide pratique pour réduire le budget gazole d'une flotte de camions au Maroc : éco-conduite, sondes de carburant, lutte contre le siphonnage, ralenti moteur et optimisation des tournées.
TXT,
            'publishedAt' => '2026-06-10',
            'readingMinutes' => 7,
            'intro' => <<<'TXT'
Pour un transporteur routier marocain, le gazole représente souvent le premier poste de coût d'exploitation, devant les salaires et l'entretien. Or une grande partie de ce budget se perd dans des postes invisibles : ralenti moteur prolongé, styles de conduite énergivores, trajets non optimisés et, dans certains cas, vols de carburant. La bonne nouvelle : chacun de ces postes se mesure, et ce qui se mesure se corrige.
TXT,
            'sections' => [
                [
                    'heading' => "D'où viennent réellement les surconsommations ?",
                    'paragraphs' => [
                        <<<'TXT'
Avant d'agir, il faut savoir où part le gazole. Sur une flotte non équipée, le gestionnaire ne dispose que d'un chiffre global : les litres achetés en station. Impossible de distinguer ce qui relève du trajet lui-même, du comportement du conducteur, de l'état mécanique du véhicule ou d'une perte anormale.
TXT,
                        <<<'TXT'
L'expérience terrain fait ressortir quatre sources principales de surconsommation, très inégales d'un véhicule à l'autre — c'est précisément cette variabilité qui rend la mesure individuelle indispensable.
TXT,
                    ],
                    'bullets' => [
                        "Le ralenti moteur : un camion qui tourne à l'arrêt (attente à quai, pause climatisée, chargement) consomme plusieurs litres par heure sans parcourir un seul kilomètre.",
                        "Le style de conduite : accélérations brutales, freinages tardifs et sur-régime peuvent creuser un écart de 10 à 20 % entre deux conducteurs sur le même trajet.",
                        "Les trajets non optimisés : détours non autorisés, kilomètres à vide et tournées mal ordonnées gonflent le kilométrage total.",
                        "Les pertes anormales : siphonnage sur les parkings, pleins partiellement détournés en station, fuites mécaniques non détectées.",
                    ],
                ],
                [
                    'heading' => 'Mesurer d\'abord : la sonde de carburant',
                    'paragraphs' => [
                        <<<'TXT'
La première étape consiste à instrumenter le réservoir. Une sonde calibrée mesure le niveau réel en continu, à ±1 litre près, et transmet la courbe au tableau de bord de gestion de flotte. On sait alors exactement quand le réservoir se remplit, se vide normalement en roulant… ou baisse brutalement moteur éteint.
TXT,
                        <<<'TXT'
Cette simple visibilité change le rapport de force : en cas de baisse suspecte, une alerte SMS ou e-mail part en moins de 30 secondes vers le gestionnaire, avec la position GPS du véhicule. Le siphonnage nocturne sur un parking non gardé, difficile à prouver auparavant, devient un événement daté, localisé et documenté.
TXT,
                        <<<'TXT'
La sonde permet aussi de rapprocher les litres réellement entrés dans le réservoir des factures de station : les écarts récurrents sur certains pleins se repèrent en quelques semaines.
TXT,
                    ],
                ],
                [
                    'heading' => "Corriger ensuite : l'éco-conduite mesurée par conducteur",
                    'paragraphs' => [
                        <<<'TXT'
Une fois la mesure en place, le levier le plus rentable est humain. Le boîtier télématique enregistre les accélérations, freinages brusques, sur-régimes et temps de ralenti de chaque conducteur, puis en tire un score d'éco-conduite sur 100.
TXT,
                        <<<'TXT'
Ce score change la nature de la discussion avec les équipes : on ne reproche plus une impression (« tu consommes trop »), on partage un indicateur objectif et comparable. Les entreprises qui affichent un classement mensuel et valorisent les meilleurs conducteurs — prime, reconnaissance, formation ciblée pour les autres — constatent généralement une amélioration durable, sans conflit social.
TXT,
                        <<<'TXT'
Le ralenti moteur mérite un traitement à part : c'est souvent la surprise du premier mois d'équipement. Des heures cumulées de moteur tournant à l'arrêt apparaissent noir sur blanc dans les rapports, et une simple consigne d'exploitation (couper le moteur au-delà de quelques minutes d'attente) produit des économies immédiates.
TXT,
                    ],
                ],
                [
                    'heading' => "Optimiser enfin : les kilomètres qu'on ne parcourt pas",
                    'paragraphs' => [
                        <<<'TXT'
Le litre le moins cher est celui qu'on ne brûle pas. Le suivi GPS en temps réel permet de vérifier que les itinéraires prévus sont respectés, d'éliminer les détours non autorisés et de réordonner les tournées de livraison pour réduire le kilométrage total.
TXT,
                        <<<'TXT'
Sur les axes longue distance (Casablanca–Tanger Med, Agadir–Marrakech, liaisons TIR vers l'Europe), la comparaison des trajets réels entre conducteurs révèle vite les itinéraires les plus économes. Sur la distribution urbaine, c'est l'ordonnancement des points de livraison qui pèse le plus.
TXT,
                    ],
                ],
                [
                    'heading' => 'Quel retour sur investissement en attendre ?',
                    'paragraphs' => [
                        <<<'TXT'
Les gains cumulés de ces trois leviers — mesure, éco-conduite, optimisation — se situent typiquement entre 15 et 25 % du budget carburant selon l'état de départ de la flotte, l'essentiel étant atteint dans les six premiers mois. Pour une flotte de 20 camions parcourant chacun 100 000 km par an, même l'hypothèse basse représente un montant qui dépasse largement le coût d'équipement et d'abonnement télématique.
TXT,
                        <<<'TXT'
C'est la raison pour laquelle la gestion du carburant est presque toujours la première brique télématique installée par les transporteurs marocains : elle finance les suivantes.
TXT,
                    ],
                ],
            ],
            'keyTakeaways' => [
                "Sans mesure individuelle par véhicule, impossible de savoir où part le gazole : la sonde de carburant est le point de départ.",
                "Les alertes de baisse anormale (moins de 30 secondes) transforment le siphonnage en événement daté, localisé et documenté.",
                "Le score d'éco-conduite par conducteur est le levier le plus rentable : 10 à 20 % d'écart entre conducteurs sur un même trajet.",
                "Le ralenti moteur est la surconsommation la plus sous-estimée — et la plus simple à corriger par consigne d'exploitation.",
                "Gain total typique : 15 à 25 % du budget carburant, l'essentiel dès les six premiers mois.",
            ],
            'relatedSolutions' => [
                ['title' => 'Gestion du Carburant', 'href' => '/solutions/gestion-carburant'],
                ['title' => 'Suivi GPS Temps Réel', 'href' => '/solutions/suivi-gps'],
                ['title' => 'Gestion de Flotte', 'href' => '/solutions/gestion-flotte'],
            ],
        ],

        [
            'slug'  => 'transport-frigorifique-onssa-exigences-europeennes',
            'title' => 'Le transport frigorifique marocain face aux exigences ONSSA et européennes',
            'category' => 'Régulation',
            'excerpt' => <<<'TXT'
Garantir le respect de la chaîne du froid est capital pour l'exportation des fruits et légumes marocains. Comment le module ReeferMate™ assure une traçabilité totale.
TXT,
            'metaDescription' => <<<'TXT'
Chaîne du froid au Maroc : obligations ONSSA, norme EN 12830, HACCP et exigences des clients européens. Comment la télématique frigorifique certifie la température de vos livraisons.
TXT,
            'publishedAt' => '2026-06-24',
            'readingMinutes' => 8,
            'intro' => <<<'TXT'
Le Maroc est un acteur majeur de l'export agroalimentaire : tomates, agrumes, produits de la mer et primeurs partent chaque jour vers l'Europe depuis le Souss, l'Oriental et les plateformes de Casablanca et Tanger Med. Pour toute cette chaîne, une seule rupture de température peut signifier une cargaison refusée, un client perdu et un litige coûteux. La réglementation — marocaine comme européenne — ne se contente plus de la bonne foi du transporteur : elle exige des preuves.
TXT,
            'sections' => [
                [
                    'heading' => "Ce que l'ONSSA attend des transporteurs frigorifiques",
                    'paragraphs' => [
                        <<<'TXT'
Au Maroc, l'Office National de Sécurité Sanitaire des produits Alimentaires (ONSSA) encadre le transport des denrées périssables dans le cadre de la loi 28-07 sur la sécurité sanitaire des aliments. Les principes sont ceux de la maîtrise de la chaîne du froid de bout en bout : température adaptée au produit, moyens de transport agréés et aptes à maintenir cette température, et capacité à démontrer que les conditions ont été respectées pendant tout le trajet.
TXT,
                        <<<'TXT'
En pratique, lors d'un contrôle ou d'un agrément, le transporteur doit pouvoir présenter des enregistrements de température fiables et datés. Un relevé manuel au départ et à l'arrivée ne suffit plus dès lors qu'un doute existe sur ce qui s'est passé entre les deux.
TXT,
                    ],
                ],
                [
                    'heading' => 'Les exigences des clients européens : EN 12830 et HACCP',
                    'paragraphs' => [
                        <<<'TXT'
Pour les exportateurs, la barre est plus haute encore. Les importateurs et centrales d'achat européennes travaillent sous référentiels HACCP et exigent de leurs transporteurs des enregistreurs de température conformes à la norme européenne EN 12830, qui définit les exigences de précision, d'enregistrement et de conservation des données des instruments utilisés dans le transport sous température dirigée.
TXT,
                        <<<'TXT'
Concrètement, un exportateur marocain qui veut sécuriser ses contrats doit être capable de fournir, pour chaque expédition, une courbe de température horodatée, produite par un instrument conforme, couvrant l'intégralité du trajet — y compris les attentes portuaires et les passages de frontière, moments où les ruptures sont les plus fréquentes.
TXT,
                    ],
                ],
                [
                    'heading' => 'Pourquoi le thermomètre du groupe froid ne suffit pas',
                    'paragraphs' => [
                        <<<'TXT'
Beaucoup de flottes se reposent sur l'afficheur du groupe frigorifique. C'est une erreur classique : cet afficheur indique la consigne et la température de l'air soufflé au point de mesure du groupe, pas nécessairement la température réelle subie par la marchandise au fond de la caisse, près des portes ou dans une zone multi-température.
TXT,
                        <<<'TXT'
Une instrumentation sérieuse repose sur des sondes indépendantes et étalonnées, placées dans les zones réellement sensibles de la caisse — jusqu'à six zones distinctes pour les remorques compartimentées — avec un enregistrement continu, embarqué et infalsifiable.
TXT,
                    ],
                ],
                [
                    'heading' => "Ce qu'apporte la télématique frigorifique ReeferMate™",
                    'paragraphs' => [
                        <<<'TXT'
Le module ReeferMate™ de CarrierWeb connecte la remorque frigorifique à la plateforme de gestion de flotte : sondes étalonnées (précision ±0,5 °C), relevés automatiques toutes les 5 minutes, mémoire embarquée en cas de coupure réseau, et intégration directe avec les principaux groupes froid du marché (Carrier, Thermo King, Zanotti).
TXT,
                        <<<'TXT'
La différence avec un simple enregistreur : la donnée est exploitée en temps réel. Si la température sort des seuils définis pour la cargaison, l'exploitant reçoit une alerte en moins de 60 secondes — pendant qu'il est encore temps d'agir (contrôler le groupe, dérouter le véhicule, prévenir le client), et non à l'arrivée devant une marchandise perdue.
TXT,
                        <<<'TXT'
À chaque livraison, un rapport de température certifié, horodaté et signé numériquement peut être transmis automatiquement au client. Pour les litiges, c'est la fin des discussions sans preuve ; pour le commercial, c'est un argument de différenciation face aux transporteurs non équipés.
TXT,
                    ],
                ],
                [
                    'heading' => 'Mettre sa flotte en conformité : par où commencer ?',
                    'paragraphs' => [
                        <<<'TXT'
La mise à niveau d'une flotte frigorifique se fait généralement en trois temps : audit des remorques et des zones à instrumenter, installation des sondes et du boîtier (sans immobilisation prolongée des véhicules), puis paramétrage des seuils par type de cargaison et des destinataires d'alertes.
TXT,
                        <<<'TXT'
Le paramétrage mérite autant d'attention que le matériel : des seuils trop stricts noient l'exploitation sous les fausses alertes, des seuils trop lâches laissent passer les vraies ruptures. Les profils types par famille de produits (surgelés, frais, primeurs) constituent un bon point de départ, affinés ensuite sur les premières semaines de données réelles.
TXT,
                    ],
                ],
            ],
            'keyTakeaways' => [
                "ONSSA (loi 28-07) comme clients européens (HACCP, EN 12830) exigent des preuves de température continues, pas seulement des relevés au départ et à l'arrivée.",
                "L'afficheur du groupe froid ne reflète pas la température réelle de la marchandise : il faut des sondes indépendantes et étalonnées, multi-zones si nécessaire.",
                "L'alerte en temps réel (moins de 60 secondes) permet de sauver la cargaison, pas seulement de constater sa perte.",
                "Le rapport certifié transmis automatiquement à chaque livraison élimine les litiges sans preuve et devient un argument commercial.",
                "Commencer par l'audit des remorques, puis installer, puis paramétrer les seuils par famille de produits.",
            ],
            'relatedSolutions' => [
                ['title' => 'Contrôle de Température', 'href' => '/solutions/controle-temperature'],
                ['title' => 'Suivi GPS Temps Réel', 'href' => '/solutions/suivi-gps'],
                ['title' => 'Gestion de Flotte', 'href' => '/solutions/gestion-flotte'],
            ],
        ],

        [
            'slug'  => 'connecter-tms-donnees-gps-flotte',
            'title' => 'Pourquoi connecter votre TMS aux données GPS de votre flotte ?',
            'category' => 'Technologie',
            'excerpt' => <<<'TXT'
L'interfaçage de vos positions et capteurs en direct dans votre logiciel de gestion (ERP/TMS) élimine la saisie manuelle et automatise la facturation.
TXT,
            'metaDescription' => <<<'TXT'
Intégration télématique-TMS/ERP : pourquoi connecter les données GPS, carburant et température de votre flotte à vos logiciels de gestion — API REST, webhooks, cas d'usage et méthode.
TXT,
            'publishedAt' => '2026-07-08',
            'readingMinutes' => 6,
            'intro' => <<<'TXT'
Dans beaucoup d'entreprises de transport marocaines, deux mondes cohabitent sans se parler : d'un côté la plateforme télématique qui sait tout de la flotte (positions, kilomètres, carburant, températures), de l'autre le TMS ou l'ERP qui gère les ordres de transport, la facturation et la paie. Entre les deux : des ressaisies manuelles, des tableurs intermédiaires et des écarts inexpliqués. L'intégration des deux systèmes est le chantier au meilleur rapport effort/impact de la digitalisation logistique.
TXT,
            'sections' => [
                [
                    'heading' => 'Le coût caché de la double saisie',
                    'paragraphs' => [
                        <<<'TXT'
Tant que la télématique reste un écran consulté à part, chaque information utile doit être recopiée : kilométrages pour la paie des conducteurs, heures d'arrivée pour la preuve de livraison, litres consommés pour le contrôle de gestion. Cette ressaisie coûte du temps administratif, introduit des erreurs, et surtout retarde l'information — une facture émise à partir de données consolidées manuellement part souvent avec plusieurs jours de décalage.
TXT,
                        <<<'TXT'
L'écart entre les deux systèmes crée aussi des zones grises : quand le TMS affiche une livraison « faite » sans horodatage vérifiable, tout litige client se règle au désavantage du transporteur.
TXT,
                    ],
                ],
                [
                    'heading' => 'Ce que change une intégration en temps réel',
                    'paragraphs' => [
                        <<<'TXT'
Une fois la plateforme télématique connectée au TMS via API, les données coulent sans intervention humaine, dans les deux sens : le TMS envoie les missions, la télématique renvoie l'exécution réelle.
TXT,
                    ],
                    'bullets' => [
                        "Facturation automatisée : kilomètres réels, heures d'attente à quai et livraisons horodatées alimentent directement la facture, sans ressaisie.",
                        "Paie des conducteurs : heures de conduite et kilométrages certifiés par le boîtier remplacent les déclarations manuelles.",
                        "ETA client fiable : la position temps réel actualise l'heure d'arrivée estimée dans le portail client sans appel téléphonique.",
                        "Contrôle de gestion : consommations et coûts par véhicule remontent dans l'ERP pour un TCO calculé sur données réelles.",
                        "Preuve de service : chaque étape (arrivée sur site, ouverture de portes, température à la livraison) est horodatée et opposable.",
                    ],
                ],
                [
                    'heading' => 'Comment ça marche techniquement ?',
                    'paragraphs' => [
                        <<<'TXT'
L'intégration moderne repose sur deux mécanismes complémentaires. L'API REST permet à votre système d'interroger la plateforme à la demande : positions actuelles, historique d'un trajet, kilométrage d'une période, niveau de carburant. Les webhooks font l'inverse : la plateforme pousse les événements vers votre serveur dès qu'ils se produisent — véhicule arrivé en zone de livraison, alerte température, baisse de carburant suspecte.
TXT,
                        <<<'TXT'
L'API CarrierWeb est documentée en OpenAPI/Swagger, sécurisée par OAuth 2.0, avec un environnement de test (sandbox) gratuit et des connecteurs prêts à l'emploi pour les systèmes les plus répandus (SAP, Sage, Microsoft Dynamics et les principaux TMS). Une équipe technique interne ou un intégrateur local peut généralement mettre en place un premier flux en quelques jours.
TXT,
                    ],
                ],
                [
                    'heading' => 'Par quel flux commencer ?',
                    'paragraphs' => [
                        <<<'TXT'
Le piège classique est de vouloir tout intégrer d'un coup. La démarche qui fonctionne : choisir le flux qui fait le plus mal aujourd'hui — presque toujours la facturation ou la paie — le mettre en production, mesurer le gain, puis étendre.
TXT,
                        <<<'TXT'
Un premier flux « kilométrage réel → facturation » se met en place rapidement et son retour sur investissement est immédiatement visible par la direction. Les flux suivants (ETA client, preuve de livraison, contrôle carburant) bénéficient alors d'une infrastructure déjà en place et d'une confiance établie.
TXT,
                    ],
                ],
            ],
            'keyTakeaways' => [
                "La double saisie entre télématique et TMS coûte du temps, des erreurs et des jours de retard de facturation.",
                "L'intégration apporte des données opposables : livraisons horodatées, kilomètres certifiés, températures prouvées.",
                "API REST pour interroger, webhooks pour être notifié en temps réel : les deux mécanismes se complètent.",
                "Commencer par un seul flux à fort impact (facturation ou paie), mesurer, puis étendre.",
                "Sandbox gratuite et connecteurs SAP/Sage/Dynamics : un premier flux se monte en quelques jours.",
            ],
            'relatedSolutions' => [
                ['title' => 'Intégration Back-Office', 'href' => '/solutions/integration'],
                ['title' => 'Gestion de Flotte', 'href' => '/solutions/gestion-flotte'],
                ['title' => 'Suivi GPS Temps Réel', 'href' => '/solutions/suivi-gps'],
            ],
        ],
    ];
}
