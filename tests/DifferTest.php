<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
     /**
     * Returns full path of the file
     *
     * @param string $filename Name of the file
     * @return string
     */
    public function getPath(string $filename): string
    {
        return __DIR__ . "/fixtures/" . $filename;
    }

    /**
     * Array of data for tests
     *
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [
                'differPlain',
                'file1.json',
                'file2.json',
                'plain'
            ],
            [
                'differStylish',
                'file1.json',
                'file2.json',
                'stylish'
            ],
            [
                'differJson',
                'file1.json',
                'file2.json',
                'json'
            ],
            [
                'differPlain',
                'file1.yaml',
                'file2.yml',
                'plain'
            ],
            [
                'differStylish',
                'file1.yaml',
                'file2.yml',
                'stylish'
            ],
            [
                'differJson',
                'file1.yaml',
                'file2.yml',
                'json'
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $expectedFile
     * @param string $firstFile
     * @param string $secondFile
     * @param string $style
     * @return void
     */
    public function testDiff(string $expectedFile, string $firstFile, string $secondFile, string $style): void
    {
        $expectedData = file_get_contents($this->getPath($expectedFile));
        $firstFilePath = $this->getPath($firstFile);
        $secondFilePath = $this->getPath($secondFile);
        $this->assertEquals($expectedData, genDiff($firstFilePath, $secondFilePath, $style));
    }
}
