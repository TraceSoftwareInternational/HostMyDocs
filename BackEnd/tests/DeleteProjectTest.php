<?php

namespace HostMyDocs\Tests;

class DeleteProjectTest extends BaseTestCase
{
    /**
     * @var array credentials to be inserted into environment
     */
    private $serverCredentials = 'beep:bep';

    private $wrongCredentials = 'plop:plop';

    private static $isInitialized = false;

    public function setUp()
    {
        // allow setUp to be done once
        if (self::$isInitialized) {
            return;
        }
        self::$isInitialized = true;

        // create project for delete using every params
        $parameters = [
            'name' => 'AnotherProject',
            'language' => 'R\'lyehian',
            'version' => '6.6.6'
        ];
        $files = ['archive' => $this->createZipFile()];
        $credentials = $this->serverCredentials;

        $this->runApp('POST', '/addProject', $parameters, $files, $credentials);

        // create project for multiple delete
        $parameters = [
            'name' => 'AThirdProject',
            'language' => 'ALanguage',
            'version' => '6.6.6'
        ];
        $files = ['archive' => $this->createZipFile()];
        $credentials = $this->serverCredentials;
        $this->runApp('POST', '/addProject', $parameters, $files, $credentials);

        $parameters = [
            'name' => 'AThirdProject',
            'language' => 'ASecondLanguage',
            'version' => '6.6.6'
        ];
        $files = ['archive' => $this->createZipFile()];
        $credentials = $this->serverCredentials;
        $this->runApp('POST', '/addProject', $parameters, $files, $credentials);

        $parameters = [
            'name' => 'AThirdProject',
            'language' => 'ALanguage',
            'version' => '4.2'
        ];
        $files = ['archive' => $this->createZipFile()];
        $credentials = $this->serverCredentials;
        $this->runApp('POST', '/addProject', $parameters, $files, $credentials);

        $parameters = [
            'name' => 'AThirdProject',
            'language' => 'ALanguage',
            'version' => '0'
        ];
        $files = ['archive' => $this->createZipFile()];
        $credentials = $this->serverCredentials;
        $this->runApp('POST', '/addProject', $parameters, $files, $credentials);
    }

    /**
     * @dataProvider arrayOfParametersProvider
     */
    public function testDeleteProject($parameters, $credentialsArray, $statusCode)
    {
        $credentials = null;

        if (array_key_exists('serverCredentials', $credentialsArray)) {
            putenv('CREDENTIALS='.$credentialsArray['serverCredentials']);
        }

        if (array_key_exists('userCredentials', $credentialsArray)) {
            $credentials = $credentialsArray['userCredentials'];
        }

        $response = $this->runApp('DELETE', '/deleteProject', $parameters, null, $credentials);
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Providing parameters for requests
     */
    public function arrayOfParametersProvider()
    {
        return [
            'no credentials' => [
                [],
                [],
                'statusCode' => 401
            ],
            'good credentials but no params' => [
                [],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'bad credentials' => [
                [],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->wrongCredentials
                ],
                'statusCode' => 401
            ],
            'name' => [
                [
                    'name' => 'SomeProject'
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'name + version' => [
                [
                    'name' => 'AnotherProject',
                    'version' => '6.6.6',
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'name + language' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'name + language + empty version' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => ''
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'valid parameters project not existing' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => 'v2017'
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'valid parameters' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => '6.6.6'
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 200
            ],
            'valid empty language parameters' => [
                [
                    'name' => 'AThirdProject',
                    'language' => '',
                    'version' => '6.6.6'
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 200
            ],
            'valid empty language + empty version parameters' => [
                [
                    'name' => 'AThirdProject',
                    'language' => '',
                    'version' => ''
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 200
            ]
        ];
    }
}
