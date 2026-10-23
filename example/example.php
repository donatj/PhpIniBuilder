<?php

require __DIR__ . '/../vendor/autoload.php';

$data = [
	'server' => [
		'host'    => 'localhost',
		'port'    => 8080,
		'enabled' => true,
	],
];

$builder = new \donatj\Ini\Builder;

echo $builder->generate($data);
