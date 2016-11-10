<?php

$domain = 'fields.' . AccessField::FIELDNAME . '.';

l::set([
  $domain . 'users' => 'Users',
  $domain . 'roles' => 'Roles',

  $domain . 'type.public' => 'Public',
  $domain . 'type.users'  => 'Users',
  $domain . 'type.roles'  => 'Roles',
]);
