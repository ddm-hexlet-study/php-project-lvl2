<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private $expected;

    public function setUp(): void
    {
          $this->expected = "{\n  - follow: false\n host: hexlet.io\n  - proxy: 123.234.53.22\n  - timeout: 50\n  + timeout: 20\n  + verbose: true\n}\n";
    }
    public function testDiff(): void
    {
        $this->assertEquals($this->expected, genDiff('fixtures/file1.json', 'fixtures/file2.json'));
    }
}
