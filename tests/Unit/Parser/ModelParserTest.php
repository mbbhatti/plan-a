<?php

namespace App\Tests\Unit\Parser;

use App\Parsers\ModelParser;
use PHPUnit\Framework\TestCase;

class ModelParserTest extends TestCase
{
    public function testGetTemplateContent()
    {
        $parser = new ModelParser();
        $content = $parser->getTemplate();
        $path = "App/Parsers/template.php";

        $this->assertEquals(file_get_contents($path), $content);
    }

    public function testEmptyManageCollection()
    {
        $collection = [];
        $parser = new ModelParser();
        $content = $parser->manageCollection($collection);

        $this->assertEmpty($content);

        $collection = [
            'name' => 'meeting-rooms',
        ];
        $content = $parser->manageCollection($collection);
        $this->assertEquals('meeting-rooms', $content['table_name']);
    }

    public function testMatchTableName()
    {
        $collection = [
            'name' => 'meeting-rooms',
        ];

        $parser = new ModelParser();
        $content = $parser->manageCollection($collection);
        $this->assertEquals('meeting-rooms', $content['table_name']);
    }

    public function testValidManageCollection()
    {
        $collection = [
            'scope' => [
                'indirect-emissions–owned',
                'electricity',
            ],
            'name' => 'meeting-rooms',
        ];
        $parser = new ModelParser();
        $content = $parser->manageCollection($collection);

        $this->assertCount(3, $content);
        $this->assertArrayHasKey('namespace', $content);
    }

    public function testFailParse()
    {
        $collection = [];

        $parser = new ModelParser();
        $content = $parser->parse($collection);

        $this->assertNotTrue($content);
    }

    public function testSuccessParse()
    {
        $collection = [
            'scope' => [
                'indirect-emissions–owned',
                'electricity',
            ],
            'name' => 'meeting-rooms',
        ];

        $parser = new ModelParser();
        $content = $parser->parse($collection);

        $this->assertTrue($content);
    }

    public function testIncompleteParseData()
    {
        $collection = [
            'name' => 'meeting-rooms',
        ];

        $parser = new ModelParser();
        $content = $parser->parse($collection);

        $this->assertNotTrue($content);
    }
}

