# WordPress and Next.js comparison

This repository contains the native WordPress version: PHP templates read content from the same WordPress installation that serves the pages. The other implementation uses a Next.js/React frontend with WordPress as a headless CMS.

Both are marketing websites. Client access goes to CarrierWeb's existing portal; neither implementation contains the fleet-tracking backend.

## SWOT

| | Native WordPress | Next.js with headless WordPress |
| --- | --- | --- |
| Strengths | One application to publish and serve. Editors can change page copy, media, FAQs, navigation and translated records in WordPress. Deployment uses PHP hosting and the included compiled assets. | Existing React components and typed content models are useful for a more interactive frontend. Cached pages can reduce how often the frontend needs the CMS. |
| Weaknesses | Layout changes still need theme development; navigation uses WordPress menus. ACF, Polylang and the database remain part of the setup. Translated slugs can differ from the French ones. | Frontend and CMS deployments need separate attention. Arabic and English content in the reference project is maintained in code. Changes to the CMS schema must stay compatible with the frontend. |
| Opportunities | Add an editorial review workflow and page caching, while keeping the public site within the existing WordPress maintenance process. | Extend the frontend with more application behaviour, reuse the content across other clients, or move all translations into the CMS. |
| Threats | Plugin updates and uncached database queries can cause regressions. Content, mail delivery, backups and PHP updates need routine ownership. | Framework and CMS changes can drift apart. Cache settings can hide editorial changes, and two deployments need coordinated maintenance. |

## What this means for this site

Native WordPress is a reasonable fit while the main job is explaining products, publishing guides and collecting enquiries. It keeps editing and page delivery together. It does not make the fixed layout a drag-and-drop editor: the design still lives in PHP and CSS.

Next.js becomes more attractive if the public site develops substantial application behaviour and the team wants to maintain a separate frontend. It still needs WordPress operations in the current architecture.

No hosting costs or speed rankings are claimed here. A useful performance comparison needs the same content, similar hosting and equivalent caching.
