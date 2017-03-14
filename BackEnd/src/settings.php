<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
    ],

    'storageRoot' => '/var/www/html/data/docs',
    'archiveRoot' => '/var/www/html/data/archives',
    'shouldSecure' => function () {
        if (getenv('SHOULD_SECURE') === false) {
            return true;
        } else {
            return false;
        }
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
    }
];
