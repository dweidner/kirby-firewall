<?php

$domain = 'fields.' . FirewallField::FIELDNAME . '.';

l::set([
  $domain . 'label' => 'Public',
  $domain . 'users' => 'Users',
  $domain . 'roles' => 'Roles',

  $domain . 'type.public' => 'Public',
  $domain . 'type.users'  => 'Users',
  $domain . 'type.roles'  => 'Roles',
]);
