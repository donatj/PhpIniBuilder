<?php

require __DIR__ . '/../vendor/autoload.php';

$data = [
	'environment' => 'production',
	'application' => [
		'name'            => 'inventory-api',
		'port'            => 8080,
		'log_path'        => '/var/log/inventory-api',
		'trusted_proxies' => [
			'10.0.0.10',
			'10.0.0.11',
		],
	],
	'database'    => [
		'host'    => 'db.internal',
		'port'    => 3306,
		'options' => [
			'timeout'    => 5,
			'persistent' => false,
		],
	],
];

$builder = new \donatj\Ini\Builder;

echo $builder->generate($data);
