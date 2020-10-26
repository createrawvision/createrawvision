# CreateRawVision

WordPress Blog about raw food and related things.

## Project Structure

```
/
|-- .scripts            For managing environments (pulling files and database from server)
|-- public_html         Stripped down WordPress
    |-- wp-content
        |-- themes
|-- tests               WPBrowser/Codeception test without generated data
```

## Scripts

Configuration for scripts is found in the files itself.

### `.scripts/pull.php`

`wp eval-file .scripts/pull.php [skip-db, skip-files, local, deactivate-plugins]`

Syncs all files and database from the configured host via SSH or locally.
Then pulls files from GitHub and deactivates some plugins.  
See file for more details.

## Production Server Requirement

In order to keep the repo small in size, I'm ignoring most of WordPress - everything except the **`themes`** folder (at the moment of writing).  
So the production has to come with **WordPress and all Plugins installed**.  
The root WP folder has to be **`public_html`**.  
**WP-CLI** has to be installed for scripts to run (`echo 'path: public_html' > wp-cli.yml` to allow execution from project root folder).

### Plugins

<details>
<summary>Plugin List</summary>

- ad-inserter  
  For inserting banner ads on top and within the content.
- antispam-bee  
  For filtering spam comments.
- autoptimize  
  Minifying everything. Works better than SiteGround Plugin for now, but eventually removing it.
- classic-editor  
  Opt out of block editor.
- code-snippets  
  Custom PHP snippets. Gets removed.
- contact-form-7  
  Contact forms. Gets replaced.
- cookie-notice  
  Cookie notice popup. Gets removed.
- genesis-enews-extended  
  Subscription form.
- google-analytics-for-wordpress  
  User tracking.
- jetpack  
  Currently used for lazy-loading images, image CDN, automatic social media sharing, comment subscriptions and similar post suggestions
- jquery-pin-it-button-for-images  
  Pinterest Pin Buttons. Gets replaced.
- luckywp-table-of-contents  
  Table of contents.
- polylang  
  Multilingual Plugin. Deactivated for now.
- popup-maker  
  Show newsletter popup.
- redirection  
  Redirects for changing urls.
- relevanssi  
  Better search.
- sg-cachepress  
  SiteGround Optimizer plugin. Maybe use all features in the future.
- shared-counts  
  Fast sharing plugin
- shortcodes-ultimate  
  Some styling shortcodes. Gets replaced.
- slide-anything  
  Sliders.
- tablepress  
  Better tables for WP.
- tablepress-responsive-tables  
  Make tables responsive.
- tinymce-advanced  
  Classic Paragraph for Block editor.
- user-role-editor  
  Custom user roles with custom capabilites.
- widget-logic  
  Show widgets only on certain pages. Gets removed.
- widget-shortcode  
  Use widgets as a shortcode. Gets removed.
- wordfence  
  Making wordpress more secure. Gets removed.
- wp-gdpr-compliance  
  Making WordPress GDPR compliant. Gets removed.
- wp-recipe-maker  
  Managing recipes in a beatiful way.
- wp-recipe-maker-premium
- wp-user-avatar  
  Use custom user avatars.
- wordpress-seo  
  YoastSEO. Improved everything SEO.

</details>

## Setup

1. Set up a local development environment with WordPress installed.
2. Clone this repo.
3. For testing, run `composer init`.
4. Now you're good to go.

## Project Organization

We use an agile approach to organizing our project.

The complete **backlog** consists of all issues:

- **User Story** = issue  
  _"As a \<user> I want to \<do a action> so I can \<achieve a goal>."_
- **Priority** = labels `prio-low`, `prio-medium`, `prio-high`
- **Feature Estimation** = labels `cost-low`, `cost-medium`, `cost-high`
- **Epic** = milestone

A **sprint** is organized with a GitHub project: The sprint backlog gets put into the column "To do" and then the sprint starts.

Every morning we have our **daily stand-up meeting**, where we tell...

1. what we did yesterday,
2. what we're going to do today and
3. what blocks us from doing this.
