<?php

declare(strict_types=1);

namespace Sputnik\Logging;

use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;
use Sputnik\Exceptions\BaseException;

class LogFormatter implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        if ($record['level'] > Logger::INFO) {
            return '';
        }

        $exception = $record['context']['exception'] ?? null;
        $context = $exception instanceof BaseException
            ? $exception->getContext()
            : $record['context'];

        return "\n" . json_encode([
                'time' => now()->toIso8601ZuluString(),
                'type' => 'info',
                'message' => $record['message'] . (!empty($context) ? ' ' . json_encode($context) : ''),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }

}