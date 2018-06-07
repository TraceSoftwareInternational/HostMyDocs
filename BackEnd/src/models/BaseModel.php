<?php

namespace HostMyDocs\Models;

use Monolog\Logger;

abstract class BaseModel implements \JsonSerializable
{
    protected $logger = null;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
}
