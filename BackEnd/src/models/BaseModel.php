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
     * @var LoggerInterface Object implementing the psr-3 loggerInterface
     */
    protected $logger;

    /**
     * save the logger for models
     *
     * @param LoggerInterface $logger Object implementing the psr-3 loggerInterface
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
