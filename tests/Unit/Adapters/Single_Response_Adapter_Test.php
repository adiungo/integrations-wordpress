<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Adapters;


use Adiungo\Integrations\WordPress\Adapters\Batch_Response_Adapter;
use Adiungo\Integrations\WordPress\Adapters\Single_Response_Adapter;
use Adiungo\Tests\Test_Case;
use Generator;
use Mockery;

class Single_Response_Adapter_Test extends Test_Case {

    /**
     * @covers       \Adiungo\Integrations\WordPress\Adapters\Batch_Response_Adapter::to_array()
     * @dataProvider provider_convert_to_array
     * @param mixed[] $expected
     * @param string $batch_response
     * @return void
     */
    public function test_can_convert_to_array(array $expected, string $batch_response): void
    {
        $instance = (new Single_Response_Adapter())->set_response($batch_response);

        $this->assertEquals($expected, $instance->to_array());
    }

    /** @see test_can_convert_to_array */
    protected function provider_convert_to_array(): Generator
    {
        yield 'Returns item when the response contains an item' => [['foo'], '{"posts": "foo"}'];
        yield 'returns empty when the response contains no item' => [[], '{}'];
        yield 'returns first item when contains multiple items' => [['foo'], '{"posts": [["foo"],["bar"]]}'];
    }
}