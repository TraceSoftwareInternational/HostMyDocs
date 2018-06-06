<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
    ],

    'storageRoot' => '/var/www/html/data/docs',
    'archiveRoot' => '/var/www/html/data/archives',
    'shouldSecure' => !getenv('SHOULD_SECURE'),
    'authorizedUser' => function () {
        $credentials = getenv('CREDENTIALS');

        if ($credentials !== false) {
            $splittedCredentials = explode(':', $credentials);

            return [
                $splittedCredentials[0] => $splittedCredentials[1]
            ];
        }

        return [];
    }
];
