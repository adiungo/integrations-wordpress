<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Builders;


use Adiungo\Integrations\WordPress\Builders\Batch_Builder;
use Adiungo\Tests\Test_Case;
use Adiungo\Tests\Traits\With_Inaccessible_Methods;
use Mockery;
use ReflectionException;
use Underpin\Enums\Types;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Exceptions\Unknown_Registry_Item;
use Underpin\Factories\Registry_Items\Param;

class Batch_Builder_Test extends Test_Case
{
    use With_Inaccessible_Methods;

    /**
     * @covers \Adiungo\Integrations\WordPress\Builders\Batch_Builder::set_page
     * @return void
     * @throws Operation_Failed
     */
    public function test_can_set_page(): void
    {
        $page = 20;
        $instance = Mockery::mock(Batch_Builder::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $param = Mockery::mock(Param::class);

        $param->allows('set_value')->with($page)->andReturns($param);

        $instance->allows('get_page_param')->andReturns($param);
        $instance->expects('get_request->set_param')->with($param);

        $this->assertSame($instance, $instance->set_page($page));
    }

    /**
     * @covers \Adiungo\Integrations\WordPress\Builders\Batch_Builder::get_page_param
     * @throws ReflectionException
     */
    public function test_can_get_page_param(): void
    {
        $param = $this->call_inaccessible_method((new Batch_Builder()), 'get_page_param');

        $this->assertEquals(new Param('page', Types::Integer), $param);
    }

    /**
     * @covers \Adiungo\Integrations\WordPress\Builders\Batch_Builder::get_page
     * @throws Unknown_Registry_Item
     */
    public function test_can_get_page(): void
    {
        $page = 14;

        $param = Mockery::mock(Param::class);
        $param->allows('get_value')->andReturns($page);

        $instance = Mockery::mock(Batch_Builder::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $instance->allows('get_request->get_param')->with('page')->andReturn($param);

        $this->assertSame($page, $instance->get_page());
    }

}