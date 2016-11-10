<?php

$domain = 'fields.' . AccessField::FIELDNAME . '.';

l::set([
  $domain . 'users' => 'Benutzer',
  $domain . 'roles' => 'Rollen',

  $domain . 'type.public' => 'Öffentlich',
  $domain . 'type.users'  => 'Benutzer',
  $domain . 'type.roles'  => 'Rollen',
]);
