<?php

namespace App\Logging;

class CustomizeFormatter
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFilePermission(0777); // 👈 force permission
        }
    }
}
