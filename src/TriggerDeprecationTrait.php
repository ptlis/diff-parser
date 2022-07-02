<?php

declare(strict_types=1);

namespace ptlis\DiffParser;

trait TriggerDeprecationTrait
{
    protected function triggerDeprecationWarning(string $method, string $propertyName): void
    {
        \trigger_error(
            'Replace calls to `' . $method . '()` with direct property access like `'
            . __CLASS__ . '::' . $propertyName . '`',
            E_USER_DEPRECATED
        );
    }
}
