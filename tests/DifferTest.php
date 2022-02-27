<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private $expected;
    private $filePath1;
    private $filePath2;

    public function testDiffJson(): void
    {
        $this->filePath1 = 'fixtures/json/complex/file1.json';
        $this->filePath2 = 'fixtures/json/complex/file2.json';
        $this->expected = file_get_contents('tests/fixtures/differPlainJson');
        $this->assertEquals($this->expected, genDiff($this->filePath1, $this->filePath2, 'plain'));
        $this->expected = file_get_contents('tests/fixtures/differComplexJson');
        $this->assertEquals($this->expected, genDiff($this->filePath1, $this->filePath2));
    }

    public function testDiffYaml(): void
    {
        $this->filePath1 = 'fixtures/yaml/complex/file1.yaml';
        $this->filePath2 = 'fixtures/yaml/complex/file2.yml';
        $this->expected = file_get_contents('tests/fixtures/differPlainYaml');
        $this->assertEquals($this->expected, genDiff($this->filePath1, $this->filePath2, 'plain'));
        $this->expected = file_get_contents('tests/fixtures/differComplexYaml');
        $this->assertEquals($this->expected, genDiff($this->filePath1, $this->filePath2));
    }
}
