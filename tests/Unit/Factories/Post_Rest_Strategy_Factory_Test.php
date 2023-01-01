<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Factories;

use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Factories\Updated_Date_Strategy;
use Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory;
use Adiungo\Tests\Test_Case;
use Adiungo\Tests\Traits\With_Inaccessible_Methods;
use DateTime;
use Mockery;
use Underpin\Factories\Request;
use Underpin\Factories\Url;
use Underpin\Registries\Param_Collection;

class Post_Rest_Strategy_Factory_Test extends Test_Case
{
    use With_Inaccessible_Methods;

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
        $instance = Mockery::mock(Post_Rest_Strategy_Factory::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $base = Url::from('baz.com');
        $batch_query_params = new Param_Collection();
        $last_requested = new DateTime('10 minutes ago');

        $expected = Mockery::mock(Rest::class);

        $single_request = (new Request())->set_url(Url::from('foo.com'));
        $batch_request = (new Request())->set_url(Url::from('bar.com'));
        $has_more_strategy = new Updated_Date_Strategy();

        $expected->expects('get_single_request_builder->set_request')->with($single_request);
        $expected->expects('get_batch_request_builder->set_request')->with($batch_request);
        $expected->expects('set_has_more_strategy')->with($has_more_strategy);

        $instance->allows('build_single_request')->andReturn($single_request);
        $instance->allows('build_batch_request')->with($base, $batch_query_params)->andReturn($batch_request);
        $instance->allows('build_has_more_strategy')->with($last_requested)->andReturn($has_more_strategy);
        $instance->allows('get_instance_template')->andReturn($expected);

        $result = $this->call_inaccessible_method($instance, 'build_data_source', $base, $last_requested, $batch_query_params);

        $this->assertSame($expected, $result);
    }
}
