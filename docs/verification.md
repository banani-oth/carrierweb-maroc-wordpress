# Verification

Last checked: **26 October 2026**.

These results cover the local WordPress installation using PHP 8.5.12.

## Environment

| Component | Version |
| --- | --- |
| WordPress | 7.1.3 |
| PHP | 8.5.12 |
| MySQL | 8.4.0 |
| CarrierWeb theme | 0.3.2 |
| Advanced Custom Fields Free | 6.8.10 |
| Polylang | 3.9.1 |
| Custom Post Type UI | 1.19.3 |
| WPGraphQL | 2.25.0 |
| WPGraphQL for ACF | 3.0.0 |

The installation includes five standard plugins and five CarrierWeb MU plugins.

## Runtime checks

| Check | Result |
| --- | --- |
| PHP syntax | All 72 custom theme and MU plugin PHP files passed lint. |
| Public routes | 77 URLs returned HTTP 200, with no PHP error markers in the responses. |
| Sitemap | Returned HTTP 200 and parsed as valid XML. |
| GraphQL | A read query returned a published solution and its ACF summary without GraphQL errors. |
| PHP error log | No new entries appeared during the runtime checks. |

## Chrome and dashboard checks

The dashboard, normal plugin list, Must-Use list, CarrierWeb editor, ACF field groups, Polylang, CPT UI, GraphQL settings and Media Library opened without a critical error.

A French homepage title was changed through **CarrierWeb**, saved and checked on the public homepage. The original title was then restored and checked again. This verified the editor's save handler and the public page reading the saved value on the new PHP runtime.

The Media Library picker opened from the editor. WordPress reported `WP_Image_Editor_Imagick` as its active image editor, Imagick 3.8.1 and GD support. Uploading a new media file was not part of this check.

On the FR, EN and AR homepages, the menu opened and closed, the FAQ opened and closed, the hero image loaded and the page did not overflow horizontally at the tested Chrome viewport. French and English used left-to-right layout; Arabic used right-to-left layout.

Responsive navigation checks covered 390, 1280, 1366, 1440 and 1920 CSS pixels in all three languages. The shared desktop threshold is 90rem, normally 1440 CSS pixels, with an additional fit check for long menu labels.

## Site Health

Site Health reported **Good**, with 24 passed checks and no critical issues. The required/recommended modules, REST API and loopback checks passed.

One recommendation remained: remove unused inactive themes.

## Test scope

HTTP and browser checks cover the local installation. External inbox delivery and performance benchmarks are not included in these results.
