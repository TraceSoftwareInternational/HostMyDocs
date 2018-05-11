<?php

namespace HostMyDocs\Tests;

class AddProjectTest extends BaseTestCase
{
    /**
     * @var array credentials to be inserted into environment
     */
    private $serverCredentials = 'beep:bep';

    private $wrongCredentials = 'plop:plop';

    /**
     * @dataProvider arrayOfParametersProvider
     */
    public function testAddProject($parameters, $credentialsArray, $statusCode)
    {
        $files = [];
        $credentials = null;

        if (array_key_exists('archive', $parameters)) {
            $files = ['archive' => $parameters['archive']];
        }

        if (array_key_exists('serverCredentials', $credentialsArray)) {
            putenv('CREDENTIALS='.$credentialsArray['serverCredentials']);
        }

        if (array_key_exists('userCredentials', $credentialsArray)) {
            $credentials = $credentialsArray['userCredentials'];
        }

        $response = $this->runApp('POST', '/addProject', $parameters, $files, $credentials);
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
            'name + language + version' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => '6.6.6',
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'name + language + invalid version' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => 'v2017',
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'name + language + version + html file' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => '6.6.6',
                    'archive' => $this->createFile(),
                ],
                [
                    'serverCredentials' => $this->serverCredentials,
                    'userCredentials' => $this->serverCredentials
                ],
                'statusCode' => 400
            ],
            'invalid zip file' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => '6.6.6',
                    'archive' => $this->createInvalidZipFile(),
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
                    'version' => '6.6.6',
                    'archive' => $this->createZipFile(),
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
