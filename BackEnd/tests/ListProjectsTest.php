<?php

namespace HostMyDocs\Tests;

class ListProjectsTest extends BaseTestCase
{
    public function testListProject()
    {
        $response = $this->runApp('GET', '/listProjects');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string) $response->getBody());
    }
}
