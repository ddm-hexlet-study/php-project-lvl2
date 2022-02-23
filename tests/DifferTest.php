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
    public function setUpYaml(): void
    {
          $this->expected = file_get_contents('tests/fixtures/differYaml');
    }
    public function testDiffJson(): void
    {
        $this->assertEquals($this->expected, genDiff('fixtures/json/file1.json', 'fixtures/json/file2.json'));
    }
    public function testDiffYaml(): void
    {
        $this->assertEquals($this->expected, genDiff('fixtures/json/file1.json', 'fixtures/json/file2.json'));
    }
}
