# Local setup

## Starting point

Use Local to run WordPress, PHP and MySQL on a development computer. An existing site export includes the database and uploaded media. A fresh installation needs the theme and modules from this repository, the editor plugins and the site's content.

## Requirements

| Requirement | Local baseline checked on 26 October 2026 |
| --- | --- |
| WordPress | 7.1.3 |
| PHP | 8.5.12 |
| Database | MySQL 8.4.0 |
| Theme | CarrierWeb Maroc 0.3.2 |
| Required plugins | ACF Free and Polylang |

The table lists the versions used for the recorded local tests.

The existing installation also contains Custom Post Type UI, WPGraphQL and WPGraphQL for ACF. The five MU plugins register the site's content types, and the PHP templates render the public pages directly. GraphQL is not required for that rendering. They are listed in the [verification record](verification.md).

## Open a site in Local

Select the WordPress site and click **Start site**. Use **Open site** for the public pages and **WP Admin** for the dashboard. The domain is shown in Local's Overview screen. These addresses are available on the computer running Local.

## Install on a fresh WordPress site

### 1. Create the WordPress installation

Create a site in Local, choose the PHP version, and let Local install WordPress and the database. Choose a site name and an administrator account during setup.

Start the site and confirm that the standard WordPress homepage and dashboard open before adding the project.

### 2. Copy the theme

Download or clone this repository and extract it if it is a ZIP. In Local, select the new site and click **Site folder**. Open `app/public/wp-content/themes`.

Copy the complete `carrierweb` directory from this repository's `wp-content/themes` into that directory. The result should be:

```text
app/public/wp-content/themes/carrierweb/style.css
app/public/wp-content/themes/carrierweb/functions.php
app/public/wp-content/themes/carrierweb/assets/dist/app.css
app/public/wp-content/themes/carrierweb/assets/dist/app.js
```

Avoid an extra nested directory such as `carrierweb/carrierweb`. WordPress needs `style.css` directly inside the theme folder. Keep the compiled assets, fonts, images and video with the theme; no asset build is needed for this initial installation.

### 3. Copy the five MU plugins

Open `app/public/wp-content`. Create a folder named `mu-plugins` if it does not exist. Copy these files directly into it:

```text
mu-plugins/
  cw-cpts.php
  cw-articles.php
  cw-temoignages.php
  cw-reglages.php
  cw-pages-fixes.php
```

WordPress loads these files automatically. Open **Plugins > Installed Plugins > Must-Use** to check that all five appear. There is no Activate button for MU plugins. Keep any unrelated MU plugins already used by the installation.

The dashboard's Must-Use tab is a list of installed files. Copy the files through the site folder.

### 4. Install the editor plugins and activate the theme

1. Install and activate **Advanced Custom Fields**, using the free version.
2. Install and activate **Polylang**.
3. Set up French, Arabic and English in Polylang. Use French as the default language for this project's current content structure.
4. Open **Appearance > Themes** and activate **CarrierWeb Maroc**.
5. Check **ACF > Field Groups**. If the project groups are missing, open ACF's import tool and import `config/acf-field-groups.json`. Compare existing field groups before replacing any that have been changed on the destination site.

The editor field export is configuration. It does not supply every field value, page, uploaded image or translated record.

### 5. Bring in the content

For a migration, transfer the database and uploaded media through the agreed private migration method. Preserve the translated records, Polylang relationships, menu assignments, ACF values and `cw_editor_*` options. Use a tool that understands serialized WordPress data when replacing the domain.

For a fresh build, create the solutions, sectors, articles and testimonials in their dashboard menus. Add their translations using Polylang and complete the sections under **CarrierWeb**. Some modules provide initial records, but these are not a substitute for the full site database.

Check the site's homepage setting under **Settings > Reading**, translated page relationships and menu locations under **Appearance > Menus > Manage Locations**.

### 6. Check URLs and menus

Open the homepage and follow a solution, sector and resource link. If an expected archive or detail URL returns 404, open **Settings > Permalinks** and save once.

Test the language switcher using the site's own links. Translated detail records can have `-ar` or `-en` in their slugs; typing a French slug after `/ar` does not necessarily reach its translation.

Verify each primary and footer menu assignment for FR, AR and EN. Edit a label or link through **Appearance > Menus**, then check the result on the public site.

## Use VS Code for file changes

Click **Site folder** in Local and open the site's `app/public/wp-content/themes/carrierweb` directory in VS Code. This is the installed theme that the browser is using. A separate Git clone is another copy of the files; it is not synchronized with the installed theme automatically.

Local must remain running while testing the pages. VS Code's Live Server extension serves static files and cannot provide the PHP and database runtime required by WordPress. The theme's `npm run dev` command watches CSS changes only; it does not start WordPress.

Follow the [development guide](development.md) when editing source assets. For routine copy and media changes, use the [WordPress content guide](content.md).

## Check the finished installation

Open the homepage, a solution, a sector, a guide and Contact in all three languages. Check the desktop navigation, mobile menu, FAQs, theme toggle, images, related links and Arabic right-to-left layout.

In the dashboard, change a small piece of text, save it, confirm that it appears on the public page, then restore it if it was only a test. Open the Media Library picker and the content-type editors. Local's Mailpit tool displays captured contact-form messages.

Keep the environment set to `local` and discourage search engine indexing in WordPress's Reading settings. Never commit `wp-config.php`, database exports, mail credentials or private backups to this repository.
