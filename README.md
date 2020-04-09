# CreateRawVision

WordPress Blog about raw food and related things.

## Project Structure

```
/
|-- .scripts            For managing environments (pulling from server and deploying changes)
|-- deployment_data     Data used for deploying changes. Neccessary because we dropped most of WP
|-- public_html         Stripped down WordPress
    |-- wp-content
        |-- themes
        |-- deploy.php  For deploying database changes. Only with WP-CLI!
|-- tests               WPBrowser/Codeception test without generated data
```

## Scripts

Configuration for scripts is found in the files itself.

### `.scripts/pull.php`

`wp eval-file ../.scripts/pull.php [skip-db, skip-files]`

Syncs all files and database from the configured host via SSH. Then pulls files from GitHub.  
See file for more details.

### `.scripts/deploy.php`

`wp eval-file ../.scripts/deploy.php`

Deploys changes from GitHub to host by running `public_html/wp-content/deploy.php`.  
See file for more details.

## Production Server Requirement

In order to keep the repo small in size, I'm ignoring most of WordPress - everything except the **`themes`** folder (at the moment of writing).  
So the production has to come with **WordPress and all Plugins installed**.  
The root WP folder has to be **`public_html`**.  
**WP-CLI** has to be installed for scripts to run.
