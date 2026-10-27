# Development

## Open the right files

Use the installed theme folder inside the Local site's `app/public/wp-content/themes/carrierweb` directory when checking a change immediately in the browser. The repository and the installed theme are separate copies unless a development workflow has explicitly linked them. Copy reviewed changes between them before committing or testing. Keep Local running; VS Code is the editor, while Local runs PHP and the database.

For a tour of the individual files, use the [architecture guide](architecture.md). For changing copy and images without code, use the [content guide](content.md).

## Structure

The theme is a classic PHP theme. Page and archive templates call shared parts for the header, footer, cards and homepage sections.

The `inc` directory holds the data readers, routing, Polylang integration, contact handler, settings and metadata. The must-use plugins register the content types and field definitions. Keep that split when adding a feature: stored content should remain available if the active theme changes.

Styles are in `assets/src/app.css`. Browser scripts are in `assets/src/js`; their numbered filenames set the build order. Local fonts, images and video are under `assets`.

The `style.css` file in the theme root contains the WordPress theme header. It is not the frontend stylesheet.

## Build assets

From the repository root:

```sh
cd wp-content/themes/carrierweb
npm ci
npm run build
```

For CSS changes while working:

```sh
npm run dev
```

`npm run build:css` compiles Tailwind. `npm run build:js` combines the browser scripts into `assets/dist/app.js`. There is no React bundle or frontend application server. The CSS watcher does not serve pages; view the WordPress address supplied by Local.

Commit the source changes and the rebuilt files in `assets/dist`. New Tailwind classes added to a PHP template may require a CSS rebuild even when the stylesheet itself has not changed.

## Content and URLs

Templates read WordPress metadata directly. Use the existing data helpers rather than reading fields separately in each template.

Use `cw_url()` for internal links. It resolves translated records through Polylang, including records whose slugs end in `-ar` or `-en`. Building a translated detail URL by adding `/ar` or `/en` to a French path can point to the wrong record.

Use the current installation's URL and theme asset URL helpers for canonical links, structured data and media. Do not put a local hostname or a developer's filesystem path in a template.

The fixed Page wrappers carry the URLs and templates. Historical `page_*` and `reglage` records supply the initial field values. The **CarrierWeb** screen stores section overrides in `cw_editor_copy_fr`, `cw_editor_copy_en` and `cw_editor_copy_ar`. A saved section takes precedence over the historical fields. Empty strings and empty lists are intentional edits and must not restore sample content. The old page-copy menus are hidden to keep one editing workflow.

`inc/editor.php` handles the admin forms, validation and shared attachment/link settings (`cw_editor_shared`). `inc/editor-compat.php` supplies existing field values when a section is first opened. `inc/editor-legal.php` imports supplied legal copy once into the normal Page editor. Menus are initialized once and belong to WordPress afterwards. A database lock prevents concurrent first requests from initializing the same menu twice. Identical sibling links are rendered once while their submenu entries remain available.

Include these option records, media attachments, menus and Polylang menu assignments when migrating the database. Copying theme files does not move those settings.

## Check a change

Run PHP lint on changed PHP files and rebuild assets after editing styles, browser scripts or Tailwind classes. Test the affected route in all three languages, including the language switcher and related links.

For menu or form changes, check keyboard use, a narrow viewport and failure feedback. For mail changes, use a test transport or Local's Mailpit rather than sending messages to the sales inbox.

Keep the contact form's normal POST route working. The JavaScript adds inline feedback; it should not be required to submit the form. A failed or ambiguous response must not trigger an automatic second submission.

The local browser and runtime checks are recorded in [Verification](verification.md).
