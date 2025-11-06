<h1 align="center">YouTube Embed Formatter Module</h1>

<p align="center">
    <strong>Module provides a field formatter to display a YouTube video from a url or video ID.</strong> The project was created solely for the purpose of presenting my skills.
</p>

<p align="center">
    <img src="https://img.shields.io/badge/php->=8.4-blue?colorB=%238892BF" alt="PHP Version">  
    <img src="https://img.shields.io/badge/release-v1.0.0-blue" alt="Release Version">
    <img src="https://img.shields.io/badge/license-MIT-blue?style=flat-square&colorB=darkcyan" alt="Read License">
</p>

# Table of Contents
- [Run demo with Docker](#run-demo-with-Docker)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage and Configuration](#usage-and-configuration)
- [Development with Docker](#development-with-docker)


## Run demo with Docker
1.  Clone this repository, navigate to its root directory and run:

    ```shell
    docker compose up -d
    ```

2.  Visit http://localhost:8080/node/1

    ![Configuration](docs/assets/demo.png "Demo")

---

## Requirements

*   Drupal 11
*   PHP 8.4 or higher

---

## Installation

1.  **Add repository to your composer.json Drupal project**

    Add the following repository to your `composer.json` file:

    ```
      "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/grzegorz-jamroz/drupal-youtube-embed-formatter-module"
        }
      ],
    ```

2.  **Install module running following command:**

    ```bash
    composer require grzegorz-jamroz/drupal-youtube-embed-formatter-module
    ```

3.  **Enable the Module**

    **Method A** - using Drush (Recommended)
    ```bash
    # If using DDEV
    ddev drush en youtube_embed_formatter -y

    # Or if using a standalone Drush
    drush en youtube_embed_formatter -y
    ```

    **Method B** - using the Drupal UI

    *   Log in to your Drupal site as an administrator.
    *   Navigate to the "Extend" page (`/admin/modules`).
    *   Find "YouTube Embed Formatter" in the list and check the box.
    *   Scroll to the bottom and click "Install".

---

## Usage and Configuration

Detailed usage and configuration instructions with screenshots can be found [here](docs/USAGE_AND_CONFIGURATION.md)

---

# Development with Docker

### Build and run the containers:
```shell
docker compose up -d
```

### Clear cache

```shell
docker compose exec app drush cr
```

### Copy vendor and composer.lock from container to host

```shell
docker compose cp app:/var/www/html/vendor ./vendor
```

### Enable xdebug

```shell
docker compose exec app xdebug on
```

### Disable xdebug

```shell
docker compose exec app xdebug off
```
