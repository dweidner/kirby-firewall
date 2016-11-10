<?php

/**
 * @file
 * Plugin core
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.0
 */

/**  Extending Kirby’s core objects. */
include __DIR__ . DS . 'extensions' . DS . 'page-methods.php';
include __DIR__ . DS . 'extensions' . DS . 'pages-methods.php';

/** Register custom request routes. */
include __DIR__ . DS . 'routes.php';

/** Register custom panel fields. */
$kirby->set('field',  'users', __DIR__ . DS . 'fields' . DS . 'users');
$kirby->set('field',  'roles', __DIR__ . DS . 'fields' . DS . 'roles');
$kirby->set('field',  'access', __DIR__ . DS . 'fields' . DS . 'access');
