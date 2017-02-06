<?php

namespace HostMyDocs\Tests;

class AddProjectTest extends BaseTestCase
{
    /**
     * @dataProvider arrayOfParametersProvider
     */
    public function testAddProject($parameters, $statusCode)
    {
        $files = [];

        if (array_key_exists('archive', $parameters)) {
            $files = ['archive' => $parameters['archive']];
        }

        $response = $this->runApp('POST', '/projects', $parameters, $files);
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Providing parameters for requests
     */
    public function arrayOfParametersProvider() {
        return [
            'no params' => [
                [
                    []
                ],
                'statusCode' => 400

            ],
            'name' => [
                [
                    'name' => 'SomeProject'
                ],
                'statusCode' => 400
            ],
            'name + version' => [
                [
                    'name' => 'AnotherProject',
                    'version' => '6.6.6',
                ],
                'statusCode' => 400
            ],
            'name + language' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian'
                ],
                'statusCode' => 400
            ],
            'name + language + version' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => '6.6.6'
                ],
                'statusCode' => 400
            ],
            'name + language + invalid version' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => 'v2017'
                ],
                'statusCode' => 400
            ],
            'name + language + version + html file' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => '6.6.6',
                    'archive' => $this->createFile()
                ],
                'statusCode' => 400
            ],
            'valid parameters' => [
                [
                    'name' => 'AnotherProject',
                    'language' => 'R\'lyehian',
                    'version' => '6.6.6',
                    'archive' => $this->createZipFile()
                ],
                'statusCode' => 200
            ]
        ];
    }
}
