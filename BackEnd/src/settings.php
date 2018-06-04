<?php

$storageRoot = '/var/www/html/data/docs';
$archiveRoot = '/var/www/html/data/archives';

return [
	'settings' => [
		'displayErrorDetails' => true,
	],

	'storageRoot' => function () use ($storageRoot) {
		return $storageRoot;
	},
	'archiveRoot' => function () use ($archiveRoot) {
		return $archiveRoot;
	},
	'shouldSecure' => function () {
		return !getenv('SHOULD_SECURE');
	},
	'authorizedUser' => function () {
		$credentials = getenv('CREDENTIALS');

		if ($credentials !== false) {
			$splittedCredentials = explode(':', $credentials);

			return [
				$splittedCredentials[0] => $splittedCredentials[1]
			];
		}

		return [];
	},
	'cache' => function () {
		return new \Slim\HttpCache\CacheProvider();
	},
	'projectController' => function () use ($storageRoot, $archiveRoot) {
		return new \HostMyDocs\Controllers\ProjectController($storageRoot, $archiveRoot);
	}
];
