<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Adapters;


use Adiungo\Integrations\WordPress\Adapters\Batch_Response_Adapter;
use Adiungo\Tests\Test_Case;
use Generator;
use Mockery;

class Batch_Response_Adapter_Test extends Test_Case
{

    /**
     * @covers       \Adiungo\Integrations\WordPress\Adapters\Batch_Response_Adapter::to_array()
     * @dataProvider provider_convert_to_array
     * @param mixed[] $expected
     * @param string $response
     * @return void
     */
    public function test_can_convert_to_array(array $expected, string $response): void
    {
        $instance = Mockery::mock(Batch_Response_Adapter::class)->makePartial();

        $instance->allows('get_response')->andReturn($response);

        $this->assertEquals($expected, $instance->to_array());
    }

    /** @see test_can_convert_to_array */
    protected function provider_convert_to_array(): Generator
    {
        yield 'returns array when posts are set' => [['foo'], '{"posts": ["foo"]}'];
        yield 'returns empty when posts are not set' => [[], '{"somethingElse": ["foo"]}'];
        yield 'returns array when posts is not an array' => [['invalid'], '{"posts": "invalid"}'];
    }
}