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
