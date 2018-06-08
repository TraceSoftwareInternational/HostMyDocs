<?php

namespace HostMyDocs\Models;

use Psr\Log\LoggerInterface;

/**
 * Base model, need to be extended by other models
 *
 * @uses \JsonSerializable
 */
abstract class BaseModel implements \JsonSerializable
{
    /**
     * @var LoggerInterface Logger used by all sub models
     */
    protected $logger;

    /**
     * save the logger for models
     *
     * @param LoggerInterface $logger Logger used by all sub models
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
