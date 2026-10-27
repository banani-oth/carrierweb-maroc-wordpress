# Editing the site in WordPress

The team can change the site's text, media, menus and published content from the WordPress dashboard. The theme keeps the page layout in place. Everyday content editing does not require a code editor or a GitHub connection.

The **CarrierWeb** screen is available to accounts with permission to edit other pages, including the standard Editor and Administrator roles. Media selection also needs upload permission. Menu assignments and plugin settings require administrator permissions.

## Choose the right editor

| Change | Where to go |
| --- | --- |
| Homepage heading, introduction or button text | **CarrierWeb → Textes et médias → Accueil — présentation** |
| Homepage photograph, site logo or demonstration video | **CarrierWeb → Textes et médias → Médias, liens et logos partenaires** |
| Solution card title, summary or icon | **Solutions**, then open the relevant record |
| Sector card title or summary | **Secteurs**, then open the relevant record |
| Resource guide | **Articles** |
| Client quote | **Témoignages** |
| Telephone, email or address | **CarrierWeb → Textes et médias → Coordonnées et présentation de l’entreprise** |
| Navigation links and their order | **Appearance → Menus** |
| Legal or privacy document | **Pages** |

The dashboard may show English WordPress commands such as **Update**, **Publish** and **Add New** alongside the project's French field labels. The section names below match the CarrierWeb screen.

## Change a heading, paragraph or button label

1. Open **CarrierWeb → Textes et médias**.
2. Select **Français**, **English** or **العربية**.
3. Select the section to edit.
4. Change the relevant fields and click **Enregistrer les modifications**.
5. Open the public page and refresh it to check the result.

Save before changing language or section. Each section is saved separately for the selected language.

For the large homepage heading, open **Accueil — présentation**. Its fields split the title into the lines used by the design:

| Field | Part of the heading |
| --- | --- |
| **Titre principal — ligne 1** | First line, for example “Pilotez votre flotte” |
| **Titre principal — ligne 2, début** | Text before the coloured words, for example “en” |
| **Titre principal — ligne 2, partie en couleur** | Coloured words, for example “temps réel” |
| **Titre principal — ligne 3** | Final line, for example “Réduisez vos coûts” |
| **Sous-titre** | Introductory paragraph below the heading |
| **Bouton principal — texte** | Main button label |
| **Bouton secondaire — texte** | Secondary button label |

The theme controls capital letters, colours and line breaks. Keep the wording short enough to fit the design, then check it on a narrow screen. Button destinations are edited separately under **Médias, liens et logos partenaires → Destinations des liens**.

A blank text field hides that text. Where list controls are available, use **Ajouter un élément**, **Retirer**, and the up/down buttons to change the rows and their order. Lists on this screen accept up to 30 items. An intentionally empty list stays empty after saving.

**Revenir au contenu d’origine** removes the saved overrides for the current section and language. It is a reset, not an undo history. On the shared media section, it resets the shared media, links and partner settings instead.

## Find a page section

The section list separates page wording from the content records that supply cards and detail pages.

| Section | What it edits |
| --- | --- |
| **Coordonnées et présentation de l’entreprise** | Contact details and company introduction |
| **Accueil — présentation** | Homepage heading, introduction, button labels and reassurance points |
| **Accueil — engagements** | Short reassurance statements below the hero |
| **Accueil — vidéo** | Video section heading and description |
| **Accueil — solutions** | Introduction above the solution cards; the cards themselves come from **Solutions** |
| **Accueil — avantages** | Homepage benefits and figures |
| **Accueil — secteurs** | Introduction above the sector cards; the cards themselves come from **Secteurs** |
| **Accueil — aperçu de la plateforme** | Platform preview labels, demonstration figures and explanatory text |
| **Accueil — compatibilité** | Introduction for the compatible brands strip |
| **Accueil — témoignages** | Testimonial section introduction; the quotes come from **Témoignages** |
| **Accueil — questions fréquentes** | Homepage FAQ questions and answers |
| **Accueil — appel à contact** | Closing homepage message and button label |
| **En-tête et navigation** | Header labels and accessibility text; menu links are managed under **Appearance → Menus** |
| **Pied de page** | Footer introduction, headings and labels |
| **Listes des solutions et secteurs** | Solution and sector archive introductions |
| **Fiches solutions — libellés** / **Fiches secteurs — libellés** | Shared labels around the detail-page content |
| **Page Pourquoi CarrierWeb** / **Page Références** / **Page Ressources** | Text and lists on those pages |
| **Articles — libellés** | Labels used around resource guides |
| **Page Contact** | Contact page headings, form labels and feedback text |
| **Page Espace Client** / **Connexion et demande d’accès** | Client access page wording |
| **Libellés communs et accessibilité** | Reused wording such as “En savoir plus” and accessibility labels |
| **Titres et descriptions pour les moteurs de recherche** | Site-level search and social sharing titles and descriptions |

The platform preview contains demonstration figures. Changing those values does not connect the website to live fleet data.

## Replace the homepage photograph, logo or video

1. Open **CarrierWeb → Textes et médias**.
2. Select **Médias, liens et logos partenaires**.
3. Find **Photo principale de l’accueil**, **Logo du site**, **Vidéo de démonstration** or **Image de partage sur les réseaux sociaux**.
4. Click **Choisir dans la médiathèque** beside that field.
5. Choose an existing file or upload a replacement through WordPress, then click **Utiliser ce média**.
6. Click **Enregistrer les modifications** and check the public page.

Uploading a file through **Media** alone does not replace the current photo or logo. The file also has to be selected in its CarrierWeb field. An account needs permission to upload files to use the picker.

The homepage photograph is cropped to fill its frame. Keep the subject away from the edges and check the result on desktop and mobile. Use a web-friendly MP4 for the demonstration video. **Utiliser le média par défaut** switches the field back to the bundled theme asset after saving; it does not delete the uploaded file.

These media selections are shared across FR, AR and EN. The same section also holds external link destinations and compatible brand/partner names and logos. Use **Ajouter une marque** and the row controls to add, remove or reorder that strip. A brand name is displayed when its row has no logo.

### Image descriptions

Give a replacement image useful alternative text in the Media Library. The text should describe the image rather than repeat a filename.

For the homepage photo, a non-empty **Alternative Text** value on the selected Media Library image takes priority over **Description de l’image pour l’accessibilité** in **Accueil — présentation**. The section field is the fallback and can be edited separately in each language. Because the selected attachment is shared, its Media Library alternative text is shared too. Check the displayed image description when reviewing translations.

## Change link destinations

Under **Médias, liens et logos partenaires → Destinations des liens**, edit the destination for the homepage buttons, demonstration buttons, client portal, WhatsApp, map or other listed links.

Use an internal path such as `/contact` for a page within this site. The theme resolves that path for the visitor's language. Use a complete address for an external service, including `https://`, or the appropriate `mailto:` or `tel:` address. Save and follow the link from each language where it appears.

The client portal link opens CarrierWeb's existing external service. This website does not create fleet accounts or authenticate portal users.

## Edit a solution card

1. Open **Solutions** and choose the record in the language to change.
2. Edit the main WordPress **Title** to change the card heading.
3. Edit **Short Description** to change the paragraph on the card.
4. Choose **Icon** to change the symbol above the heading.
5. Set **Order** in **Page Attributes** to change listing order. Smaller numbers appear first.
6. Click **Update**, then check both the homepage and the solutions listing.

For example, the “Gestion de Flotte” heading comes from the solution's title, and its short paragraph comes from **Short Description**. The longer **Description** belongs to the detail page. Changing it does not replace the short card summary.

The icon selector includes choices such as **Map Pin**, **Fuel**, **Thermometer**, **Bar Chart**, **Truck** and **Container**. These cards use icons rather than an uploaded card photograph.

On the solutions listing, the “En savoir plus” label is shared wording under **CarrierWeb → Textes et médias → Libellés communs et accessibilité**. The homepage card label is under **Accueil — solutions**. Both links lead to the solution's own page.

To add a solution, use **Add New**, complete the title and fields, assign the correct language, set its order and publish it. Published solution records supply the cards for that language. Menu links are managed separately, so add a navigation link if the new item should appear in a menu.

## Edit sectors, articles and testimonials

| Dashboard menu | Main fields and use |
| --- | --- |
| **Secteurs** | Title, **Icon**, **Listing Description**, detail-page **Description**, use cases and recommended solutions |
| **Articles** | Title, category, excerpt, publication date, reading time, introduction, numbered sections, paragraphs and bullet points |
| **Témoignages** | **Citation**, author/function, sector/activity and company/city fields |

Open an existing record to edit it, or use **Add New** to create one. Save an unfinished item as a draft. Check its language and publish status before expecting it on the public site.

Some detail-page fields use a fixed number of rows because the project uses ACF Free. To remove an optional numbered row, clear all its text fields. Adding more rows than the available fields or changing the detail-page structure requires theme development.

## Manage French, Arabic and English

There are two editing workflows:

- **CarrierWeb → Textes et médias:** select the language tab and save that language's section.
- **Solutions, Secteurs, Articles, Témoignages and Pages:** use Polylang's language and translation controls on the record.

For a record, the pencil in Polylang's language column opens an existing translation. The plus sign creates a linked translation. When the translated record already exists, select it in the record's **Translations** controls rather than creating a duplicate. Confirm that the French, Arabic and English versions are linked to one another.

Each translation needs its own title, fields and published status. Saving the French version does not update the other languages. Keep the original meaning, check technical terms and review the Arabic page in its right-to-left layout.

Translated URLs can have their own slugs, including `-ar` or `-en`. Use the site's language switcher to check the relationships; do not construct a translated address by adding a language prefix to the French URL.

Some record screens include an optional translation-provider control. It needs separate service configuration and is not required for manual editing. It does not run when French content is saved, and any text it produces still needs review. The **CarrierWeb** section settings are edited directly through their language tabs.

## Change the navigation and footer menus

1. With an administrator account, open **Appearance → Menus**.
2. Select the intended menu and language.
3. Add or remove links and drag the items into the required order. Indent an item beneath another item to make it a submenu.
4. Click **Save Menu**.
5. Open **Manage Locations** and confirm the language assignment.
6. Check the public desktop navigation, compact menu and footer in that language.

There are six assignments to maintain: a **Navigation principale** menu and a **Pied de page** menu for each of FR, AR and EN. The seeded menu names follow the pattern **CarrierWeb — Navigation — FR** and **CarrierWeb — Pied de page — FR**, with equivalent AR and EN menus.

A menu label is independent of a page title. Renaming a page does not always rename an existing menu item. Remove links to unpublished content and add links to new records when needed.

All languages share the same desktop threshold. Below 1440 CSS pixels at the browser's default text size, navigation links appear behind the menu button. At wider sizes, unusually long labels can also trigger the compact menu if the links do not fit. Arabic keeps its right-to-left reading direction.

## Edit legal and privacy documents

Open **Pages**, then edit **Mentions légales** or **Politique de confidentialité** in the required language. These pages use the normal WordPress title and body editor. Use headings, paragraphs, lists and links for the document.

The supplied French copy is imported once as a starting document. Arabic and English pages retain a French-source notice until their body is changed. Once a page has been saved, the saved body is used, including an intentionally empty body.

## Check a saved change

Open the public page after saving and refresh it. Read the text, follow the changed links and use the language switcher. For images, menus and longer titles, also check a narrow mobile window. If a hosting cache is enabled and old content remains visible, clear that cache and refresh again.

If a new record is missing, check its published status and language. If the language switcher opens the wrong record, review the Polylang relationship. If a menu link has the wrong wording or order, review the menu itself rather than only the target page.

The contact form sends through WordPress mail. Its success message means the configured mail service accepted the message, rather than confirming inbox delivery. Local captures messages in Mailpit.

## Changes that need development

The WordPress editors cover text, button labels and destinations, media, contact details, lists, records, translations and menu contents. They do not change the overall layout, introduce a new type of section or add an integration. Those changes belong in the theme.

Content saved in WordPress is stored in its database; it is not automatically committed to GitHub.
