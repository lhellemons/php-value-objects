<?php

namespace Test\SolidPhp\ValueObjects\Type;

use PHPUnit\Framework\TestCase;

class PsalmTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/..';
    private const PSALM_BINARY = 'vendor/bin/psalm';

    public function testPsalmPasses(): void
    {
        $this->expectNotToPerformAssertions();

        $projectRoot = realpath(self::PROJECT_ROOT);
        $psalmBinaryPath = sprintf('%s/%s', $projectRoot,self::PSALM_BINARY);

        if (!is_file($psalmBinaryPath)) {
            $this->markTestSkipped(sprintf('psalm not available at "%s"', $psalmBinaryPath));

            return;
        }

        $command = sprintf('%s', self::PSALM_BINARY);
        $output = [];
        $exitCode = 0;

        fwrite(STDOUT, sprintf("executing psalm: %s\n", $command));
        $previousCwd = getcwd();
        chdir($projectRoot);
        exec($psalmBinaryPath, $output, $exitCode);
        if ($previousCwd) {
            chdir($previousCwd);
        }

        if ($exitCode !== 0) {
            fwrite(STDERR, implode("\n", $output) . "\n");

            $this->fail(sprintf('psalm found errors'));
        }
    }
}
