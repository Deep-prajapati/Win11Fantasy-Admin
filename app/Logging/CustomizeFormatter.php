<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

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
            $handler->setFormatter(new LineFormatter(
                null,
                null,
                true,
                true
            ));
        }
    }
}
