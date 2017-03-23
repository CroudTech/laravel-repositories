<?php return array (
  'providers' =>
  array (
    0 => 'Illuminate\\Cache\\CacheServiceProvider',
    1 => 'CroudTech\\Repositories\\Providers\\RepositoryServiceProvider',
  ),
  'eager' =>
  array (
    0 => 'CroudTech\\Repositories\\Providers\\RepositoryServiceProvider',
  ),
  'deferred' =>
  array (
    'cache' => 'Illuminate\\Cache\\CacheServiceProvider',
    'cache.store' => 'Illuminate\\Cache\\CacheServiceProvider',
    'memcached.connector' => 'Illuminate\\Cache\\CacheServiceProvider',
  ),
  'when' =>
  array (
    'Illuminate\\Cache\\CacheServiceProvider' =>
    array (
    ),
  ),
);
