# Project structure

This is a classic WordPress theme with five site-specific must-use plugins. WordPress serves the pages and reads the saved content from its own database. PHP templates render the HTML; compiled CSS and small browser scripts provide the design and interactions.

React and a Node application server are not part of the deployed site. Node is used to rebuild the theme assets during development. ACF Free supplies editing fields, and Polylang manages the language records, relationships and URLs.

## How a page is rendered

1. WordPress and the must-use plugins load. The plugins register the site's content types and supporting fields.
2. The active theme's `functions.php` loads the modules in `inc`.
3. WordPress selects a page, archive or single-record template.
4. The template reads the relevant settings and records through the theme's helpers, then includes shared template parts.
5. `inc/enqueue.php` loads the compiled styles and browser scripts.

The files in Git describe the application. Published content, attachment records, menu assignments and translations also depend on the WordPress database. Copying the theme alone does not recreate a complete installation.

## Files at the repository root

| File or directory | Purpose |
| --- | --- |
| `README.md` | Entry point, installation overview and current project status |
| `wp-content/themes/carrierweb/` | Theme templates, PHP helpers, styles, scripts and bundled media |
| `wp-content/mu-plugins/` | Five site modules loaded automatically by WordPress |
| `config/acf-field-groups.json` | Export of ACF field definitions; it does not contain the full site content |
| `docs/` | Setup, editor, development, verification and comparison notes |
| `THIRD_PARTY_NOTICES.md` and licence files | Attribution and licence information for included dependencies and assets |
| `.gitignore` / `.gitattributes` | Exclusions and repository text-file settings |

WordPress core, third-party plugin packages and private installation configuration are maintained outside this repository.

## Theme entry points and templates

All paths in this section are relative to `wp-content/themes/carrierweb/`.

| File | Responsibility |
| --- | --- |
| `functions.php` | Defines the theme version and paths, then loads the `inc` modules in a deliberate order |
| `style.css` | WordPress theme metadata; the public stylesheet is built separately |
| `header.php` | Document opening, shared header, language switcher, desktop navigation and compact menu |
| `footer.php` | Footer content, links and document closing |
| `front-page.php` | Homepage; includes the home sections in their fixed order |
| `archive-solution.php` / `archive-secteur.php` | Solution and sector listings |
| `single-solution.php` / `single-secteur.php` | Detail pages for a solution or sector record |
| `archive-article.php` / `single-article.php` | Resource listing and individual guide |
| `page-pourquoi-carrierweb.php` / `page-references.php` | Why CarrierWeb and references pages |
| `page-contact.php` | Contact page and enquiry form |
| `page-espace-client.php` / `page-connexion.php` / `page-inscription.php` | Client access and access-request pages that lead to the existing portal or contact route |
| `page-mentions-legales.php` / `page-confidentialite.php` | Legal and privacy page wrappers |
| `index.php` | WordPress template fallback |

These templates control layout. The editor can replace their content through the supported fields, but moving whole sections or adding a new layout requires a template change.

### Shared template parts

| Directory | Responsibility |
| --- | --- |
| `template-parts/home/` | Hero, trust strip, video, solutions, benefits, sectors, platform preview, brands, testimonials, FAQ and final contact prompt |
| `template-parts/cards/` | Reused solution, sector and article cards |
| `template-parts/nav/` | Logo, language switcher and theme-toggle markup |
| `template-parts/blocks/` | Breadcrumb hero, FAQ accordion, related-record grid, legal document body and client-access shell |

For example, a solution's title, icon and short description are read from its record. The card template determines their appearance, and the same records feed the homepage solution section.

## PHP modules in `inc`

### Content editing and data

| File | Responsibility |
| --- | --- |
| `editor.php` | Registers **CarrierWeb → Textes et médias**, renders its fields and media pickers, validates saves and stores the section, shared-media and link settings |
| `editor-compat.php` | Uses existing page fields as initial editor values and hides the older page-copy menus |
| `editor-legal.php` | Imports the supplied legal copy once into ordinary Pages and tracks saved legal content |
| `data.php` | Reads solution, sector, article and testimonial records; assembles numbered fields, related records and translated fallback data for templates |
| `settings.php` | Reads the historical site-settings record and resolves current editor overrides |
| `fixed-pages.php` | Resolves text and numbered lists from historical fixed-page fields, editor overrides and dictionary defaults |
| `pages.php` | Creates or connects the WordPress Pages that carry the theme's fixed page URLs and templates |
| `content/en.php` / `content/ar.php` | Bundled translated content used as fallback data where needed |
| `legal-data.php` | Supplied source text for legal and privacy documents |

The main section overrides are stored as `cw_editor_copy_fr`, `cw_editor_copy_en` and `cw_editor_copy_ar` in WordPress options. Shared attachments, external destinations and partner rows are stored in `cw_editor_shared`. Saved empty values remain intentional edits rather than being replaced with sample content.

Solutions, sectors, articles and testimonials use WordPress records and metadata. Menus use WordPress menu records and location assignments. Legal documents use ordinary Page content. All of these need to be included in a content migration.

### Languages, URLs and navigation

| File | Responsibility |
| --- | --- |
| `i18n.php` | Locale helpers, dictionary access, translated text and internal URL helpers |
| `dictionaries/fr.php`, `dictionaries/en.php`, `dictionaries/ar.php` | Default interface and page-section wording for the three languages |
| `polylang.php` | Integrates the FR/AR/EN languages, linked content records and translated URLs with Polylang |
| `routing.php` | Language routing fallback when Polylang is unavailable |
| `nav.php` | Reads the primary and footer menus, resolves their language assignments, initializes missing menus once and consolidates identical sibling links when rendering |
| `translate.php` | Optional, explicitly requested translation of linked record fields through a configured DeepL or Azure service; it is not background synchronization |

Use the existing URL helpers when adding links. Translated records can have different slugs, so adding `/ar` or `/en` to a French detail-page address is not a reliable translation lookup.

### Site behaviour and metadata

| File | Responsibility |
| --- | --- |
| `setup.php` | Theme supports, navigation locations and base WordPress presentation settings |
| `enqueue.php` | Loads compiled CSS, scripts and fonts; removes unnecessary block assets for these PHP templates |
| `icons.php` / `icon-data.php` | SVG icon helper and icon definitions |
| `contact-form.php` | Validates enquiry submissions, handles feedback and sends through WordPress mail or the configured SMTP transport |
| `seo.php` | Page titles, descriptions, canonical URLs, language alternatives, social metadata and local/staging search visibility |
| `schema.php` | JSON-LD for the organization, business, website, services, FAQs and breadcrumbs |
| `sitemap.php` | Custom sitemap with language alternatives and robots-related output |

The contact form uses `wp_mail()`. The transport result controls the form feedback; inbox delivery depends on the installation's mail service.

## The five must-use plugins

Place these five PHP files directly in `wp-content/mu-plugins/`. WordPress loads them automatically; they do not have an activation button. Keep them installed when replacing or updating the theme so the site-specific content types remain registered.

| File | Responsibility |
| --- | --- |
| `cw-cpts.php` | Registers solutions and sectors, sets their public archive/detail URLs, adjusts the resource archive routing and keeps data-only content types from becoming empty public pages |
| `cw-articles.php` | Registers resource articles, defines their ACF Free fields and supplies initial article records |
| `cw-temoignages.php` | Registers testimonials, defines the quote/author fields and supplies initial testimonial records |
| `cw-reglages.php` | Registers the historical site-settings record, its fields and initial values |
| `cw-pages-fixes.php` | Registers the historical fixed-page data records, defines their fields and supplies initial page copy while preserving existing values |

Some modules supply starting records, but they do not rebuild every solution, sector, attachment or Polylang relationship from an empty database. See the [setup guide](setup.md) for the content needed by an installation.

## Styles, scripts and media

| Path | Responsibility |
| --- | --- |
| `assets/src/app.css` | Tailwind input and the theme's layout, typography, colour and component rules |
| `assets/src/js/01-theme.js` | Dark/light theme behaviour |
| `assets/src/js/02-header.js` | Responsive navigation, dropdowns, compact menu and header-fit checks |
| `assets/src/js/03-reveal.js` | Content reveal interactions |
| `assets/src/js/04-scroll-progress.js` | Page scroll-progress indicator |
| `assets/src/js/05-video.js` | Video-section behaviour |
| `assets/src/js/06-faq.js` | FAQ accordion behaviour |
| `assets/src/js/07-carousel.js` | Testimonial carousel behaviour |
| `assets/src/js/08-contact.js` | Contact form submission feedback while preserving the normal POST route |
| `assets/src/js/09-auth.js` | Reserved build-order entry; the access pages use portal and contact links |
| `assets/dist/app.css` / `assets/dist/app.js` | Compiled assets loaded by WordPress |
| `assets/fonts/`, `assets/icons/`, `assets/img/`, `assets/video/` | Bundled assets and default media |
| `tools/build-js.mjs` | Combines browser scripts in their numbered order |
| `package.json` / `package-lock.json` | Build commands and pinned dependency resolution |

Edit source files and rebuild the compiled assets before committing a style or script change. `npm run dev` watches the CSS build; it does not start WordPress. See [development](development.md) for the commands.

The default media bundled with the theme is different from files uploaded through the WordPress Media Library. The latter are installation content and need their own migration.

## Documentation

- [Local setup](setup.md) covers installing the project and bringing over content.
- [Editing content](content.md) covers the dashboard workflow.
- [Development](development.md) covers builds and code changes.
- [WordPress and Next.js comparison](comparison.md) explains the tradeoffs between the two implementations.
