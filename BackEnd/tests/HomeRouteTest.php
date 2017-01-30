<?php

namespace Tests;

class HomeRouteTest extends BaseTestCase {

    public function testHomeRoute() {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('HostMyDocs', (string)$response->getBody());
    }
}
