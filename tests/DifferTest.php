<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private $expected;

    public function setUp(): void
    {
          $this->expected = file_get_contents('tests/fixtures/differJson');
    }
    public function testDiff(): void
    {
        $this->assertEquals($this->expected, genDiff('fixtures/file1.json', 'fixtures/file2.json'));
    }
}
