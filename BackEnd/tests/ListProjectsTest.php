<?php

namespace HostMyDocs\Tests;

class ListProjectsTest extends BaseTestCase
{
    public function setUp()
    {
        $parameters = [
            'name' => 'AnotherProject',
            'language' => 'R\'lyehian',
            'version' => '6.6.6'
        ];
        $files = ['archive' => $this->createZipFile()];
        $credentials = 'beep:bep';
        $this->runApp('POST', '/addProject', $parameters, $files, $credentials);
    }

    public function tearDown()
    {
        $parameters = [
            'name' => 'AnotherProject',
            'language' => 'R\'lyehian',
            'version' => '6.6.6'
        ];
        $credentials = 'beep:bep';
        $this->runApp('DELETE', '/deleteProject', $parameters, null, $credentials);
    }

    public function testListProject()
    {
        $response = $this->runApp('GET', '/listProjects');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string) $response->getBody());
    }
}
