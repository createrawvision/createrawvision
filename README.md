# CreateRawVision

WordPress Blog about raw food and related things.

## Project Structure

```
/
|-- .scripts              For managing environments (pulling files and database from server)
|-- public_html           Stripped down WordPress
    |-- wp-content
        |-- languages     Only some custom translations
        |-- mu-plugins
        |-- plugins       Only wp-bookmarks plugin (it had to be customized, since it was outdated)
        |-- themes        Most customization is done here
|-- tests                 WPBrowser/Codeception test without generated data
```

## Scripts

Configuration for scripts is found in the files itself.

### `.scripts/pull.php`

`wp eval-file .scripts/pull.php [skip-db, skip-files, local, deactivate-plugins]`

Syncs all files and database from the configured host via SSH or locally.
Then pulls files from GitHub and deactivates some plugins.  
See file for more details.

## Production Server Requirement

In order to keep the repo small in size, I'm ignoring most of WordPress.  
So the production has to come with **WordPress and all Plugins installed**.  
The root WP folder has to be **`public_html`**.  
**WP-CLI** has to be installed for scripts to run (`echo 'path: public_html' > wp-cli.yml` to allow execution from project root folder).

## Plugins & Themes

This list is *not* comprehensive, it just shows the central dependencies to other code.

- Parent Theme: Genesis ([Developer Docs](https://studiopress.github.io/genesis/))
- Child Theme: Daily Dish Pro ([Setup Instructions](https://my.studiopress.com/documentation/daily-dish-pro-theme/))
- Membership Plugin: Restrict Content Pro ([Documentation](https://docs.restrictcontentpro.com/))
- Recipes Plugin: WP Recipe Maker ([Documentation](https://help.bootstrapped.ventures/collection/1-wp-recipe-maker))

## Setup

1. Set up a local development environment ready to use WordPress.
2. Clone this repo.
3. Run the pull script to get the current state from the server (see [`.scripts/pull.php`](#scriptspullphp)).
4. If you want to run tests, execute `composer init` and save a database dump to `./tests/_data/dump.sql`.
5. Now you're good to go.

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
