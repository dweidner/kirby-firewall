<?php

$domain = 'fields.' . FirewallField::FIELDNAME . '.';

l::set([
  $domain . 'label' => 'Öffentlich',
  $domain . 'users' => 'Benutzer',
  $domain . 'roles' => 'Rollen',

  $domain . 'type.public' => 'Öffentlich',
  $domain . 'type.users'  => 'Benutzer',
  $domain . 'type.roles'  => 'Rollen',
]);
