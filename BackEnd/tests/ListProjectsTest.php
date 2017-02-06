<?php

namespace HostMyDocs\Tests;

class ListProjectsTest extends BaseTestCase
{
    public function testListProject()
    {
        $response = $this->runApp('GET', '/projects');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson((string) $response->getBody());
    }
}
