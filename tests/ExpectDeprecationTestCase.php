<?php

declare(strict_types=1);

namespace ptlis\DiffParser\Test;

use PHPUnit\Framework\TestCase;

class ExpectDeprecationTestCase extends TestCase
{
    /** @var array<string> */
    private array $errors = [];

    /**
     * Setup custom error handler, reset errors array.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->errors = [];
        set_error_handler($this, E_USER_DEPRECATED);
    }

    /**
     * Restore previous error handler.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        restore_error_handler();
    }

    /**
     * Error handling function.
     */
    public function __invoke(int $errorType, string $errorMessage, string $errorFile, int $errorLine): bool
    {
        $this->errors[] = $errorMessage;
        return true;
    }

    public function expectDeprecationNotice(): void
    {
        $this->assertGreaterThan(0, count($this->errors), 'Expected a deprecation notice but none was encountered');
    }
}
