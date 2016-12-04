# Kirby Firewall Plugin

![Version](https://img.shields.io/badge/version-1.0.0-orange.svg) ![License](https://img.shields.io/badge/license-MIT-green.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-2.4%2B-red.svg)

Protect your pages and files from unauthorized access. Simply select the users and roles that should be able to view your content via a custom field.

![Firewall Field](http://dweidner.github.io/kirby-firewall/images/firewall-field.gif)

## Requirements

- [**Kirby**](https://getkirby.com/) 2.4+
- PHP 5.4.0+

## Installation

Use one of the alternatives below.

### 1. Kirby CLI

If you are using the [Kirby CLI](https://github.com/getkirby/cli) you can install this plugin by running the following commands in your shell:

```
$ cd path/to/project
$ kirby plugin:install dweidner/kirby-firewall
```

### 2. Clone or download

1. [Clone](https://github.com/dweidner/kirby-firewall.git) or [download](https://github.com/dweidner/kirby-firewall/archive/master.zip)  this repository.
2. Unzip the archive if needed and rename the folder to `firewall`.

**Make sure that the plugin folder structure looks like this:**

```
site/plugins/firewall/
```

### 3. Git Submodule

If you know your way around Git, you can download this plugin as a submodule:

```
$ cd path/to/project
$ git submodule add https://github.com/dweidner/kirby-firewall site/plugins/firewall
```

## Setup

### 1. Firewall Field (optional)

To use the access control field within your blueprint use the following:

```
fields:
  firewall:
    label: Access Control
    type: firewall
```

You can exclude both users as well as roles from the corresponding input list:

```
fields:
  firewall:
    label: Access Control
    type: firewall
    exclude:
      role:
        - guest
```

Have a lot of users? You might want to increase the number of columns:

```
fields:
  firewall:
    label: Access Control
    type: firewall
    columns: 3
```

### 2. Asset Firewall (optional)

In order for the asset firewall to work, you have to customize your `.htaccess` file in the project root. Change the following line:

```
RewriteRule ^content/(.*)\.(txt|md|mdown)$ index.php [L]
```

to

```
RewriteRule ^content/(.*)$ index.php [L]
```

It allows our custom route to control the access to all your files within the content folder.

## Usage

Once you have completed the setup you can limit access to a page and its contents via a custom field. In order to only allow users of the role `Editor` to access the page `http://example.com/submissions` you need to edit the corresponding content file `content/05-submissions/submissions.md` as follows:

```
Title: Downloads

----

Firewall:
  roles:
    - editor
```

You can also combine role ids with usernames:

```
Title: Downloads

----

Firewall:
  roles:
    - editor
  users:
    - dweidner
```

If you don't like to edit your content files by hand you can install the [Kirby Panel](https://github.com/getkirby/panel). Once the Panel is running on your server our custom field will help you out with that process. Hava a look into the section [Firewall Field](#1-firewall-field-optional) for further setup instructions.

## Options

The following options can be set in your `/site/config/config.php` file:

```php
c::set('plugin.firewall.fieldname', 'firewall');
c::set('plugin.firewall.redirect', false);
c::set('plugin.firewall.pages', '(.*)');
c::set('plugin.firewall.content', 'content/(.*)');

c::set('field.users.template', '{username} ({role})');
c::set('field.roles.template', '{id} ({name})');
```

### plugin.firewall.fieldname

Name of the field that is controlling the access to your pages or asset files (default: `firewall`).

### plugin.firewall.redirect

Set a custom redirect uri for users with insufficient user privileges. By default a simple "Access denied" page with corresponding "403 Forbidden" response header is returned. If you prefer to redirect the user to a specific page (e.g. `http://yourdomain.com/auth/login`) simply set this option to the desired uri (e.g. `auth/login`).

### plugin.firewall.pages

Allows you to customize the uri pattern of the route which is protecting access to your pages. By default all of your pages which use the Firewall field are protected. You can change the uri pattern if you want to protect a specific subdirectory of your site only (e.g. `/staff/(.*)`). Addionally you can disable the route entirely by setting the option to `false`.

### plugin.firewall.content

Allows you to customize the uri pattern of the route which is protecting access to your content files. By default all of your files are protected which belong to a page using the Firewall field. You can change the uri pattern if you want to protect access to specific files of your site only (e.g. `content/downloads/(.*)`). Addionally you can disable protection of content files  entirely by setting the option to `false`.

### field.users.template

This option allows you to customize the way a user is displayed in the panel (default: `{username} ({role})`).

Available placeholders:

- username
- email
- role
- language
- avatar
- gravatar

### field.roles.template

This option allows you to customize the way a role is displayed in the panel (default: `{id} ({name})`).

Available placeholders:

- id
- name

## Credits

- [Kirby Cookbook](https://getkirby.com/docs/cookbook/asset-firewall) The core of this plugin is heavily based on the suggestions made in this recipe.
