<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Factories;

use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory;
use Adiungo\Tests\Test_Case;
use DateTime;
use Mockery;
use Underpin\Factories\Url;

class Post_Rest_Strategy_Factory_Test extends Test_Case
{
    /**
     * @covers \Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory::get_instance_template
     * @return void
     */
    public function test_can_get_instance_template(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory::build
     * @return void
     */
    public function test_can_build(): void
    {
        $base = new Url();
        $last_requested = new DateTime();
        $batch_query_params = null;
        $data_source = new Rest();
        $instance = Mockery::mock(Post_Rest_Strategy_Factory::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $instance->expects('build_data_source')->with($base, $last_requested, $batch_query_params)->andReturn($data_source);

        $this->assertSame($data_source, $instance->build($base, $last_requested, $batch_query_params)->get_data_source());
    }

    /**
     * @covers \Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory::build_data_source
     * @return void
     */
    public function test_can_build_data_source(): void
    {
        $this->markTestIncomplete();
    }
}
