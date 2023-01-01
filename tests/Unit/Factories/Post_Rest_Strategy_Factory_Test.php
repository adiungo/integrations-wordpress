<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Factories;

use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Factories\Updated_Date_Strategy;
use Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory;
use Adiungo\Tests\Test_Case;
use Adiungo\Tests\Traits\With_Inaccessible_Methods;
use DateTime;
use Generator;
use Mockery;
use ReflectionException;
use Underpin\Enums\Types;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Exceptions\Unknown_Registry_Item;
use Underpin\Exceptions\Url_Exception;
use Underpin\Exceptions\Validation_Failed;
use Underpin\Factories\Registry_Items\Param;
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
     * @throws Operation_Failed
     * @throws Validation_Failed
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
     * @throws Url_Exception
     * @throws ReflectionException
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

    /**
     * @covers \Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory::build_single_request
     * @return void
     * @throws ReflectionException|Url_Exception
     */
    public function test_can_build_single_request(): void
    {
        $instance = Mockery::mock(Post_Rest_Strategy_Factory::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $base = Url::from('foo.com');

        $result = $this->call_inaccessible_method($instance, 'build_single_request', $base);

        $this->assertSame($result->get_url(), $base);
    }

    /**
     * @covers \Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory::maybe_set_param()
     *
     * @return void
     * @throws ReflectionException
     * @throws Validation_Failed
     */
    public function test_maybe_set_param_adds_param_when_it_does_not_exist(): void
    {
        $instance = Mockery::mock(Post_Rest_Strategy_Factory::class)->makePartial();

        $base = Mockery::mock(Url::class);
        $param = (new Param('foo', Types::Integer))->set_value(1);

        $base->allows('get_params->get')->with('foo')->andThrow(Unknown_Registry_Item::class);
        $base->expects('add_param')->with($param)->once();

        $result = $this->call_inaccessible_method($instance, 'maybe_set_param', $base, $param);

        $this->assertSame($base, $result);
    }

    /**
     * @covers \Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory::maybe_set_param()
     *
     * @return void
     * @throws ReflectionException
     * @throws Validation_Failed
     */
    public function test_maybe_set_param_does_not_add_param_when_it_exists(): void
    {
        $instance = Mockery::mock(Post_Rest_Strategy_Factory::class)->makePartial();

        $base = Mockery::mock(Url::class);
        $param = (new Param('foo', Types::Integer))->set_value(1);

        $base->allows('get_params->get')->with('foo')->andReturn($param);
        $base->expects('add_param')->never();

        $result = $this->call_inaccessible_method($instance, 'maybe_set_param', $base, $param);

        $this->assertSame($base, $result);
    }


    /**
     * @covers       \Adiungo\Integrations\WordPress\Factories\Post_Rest_Strategy_Factory::build_batch_request
     * @param Url $expected
     * @param Url $base
     * @param Param_Collection|null $batch_query_params
     * @return void
     * @throws ReflectionException
     * @dataProvider provider_can_build_batch_request
     */
    public function test_can_build_batch_request(Url $expected, Url $base, ?Param_Collection $batch_query_params): void
    {
        $instance = Mockery::mock(Post_Rest_Strategy_Factory::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $result = $this->call_inaccessible_method($instance, 'build_batch_request', $base, $batch_query_params);

        $url = $result->get_url();

        $this->assertSame($result->get_url(), $base);
        $this->assertEquals($expected, $url);
    }

    /**
     * @return Generator
     * @throws Operation_Failed
     * @throws Validation_Failed
     * @see test_can_build_batch_request
     */
    public function provider_can_build_batch_request(): Generator
    {
        yield 'It sets batch URL params' => [
            (new Url())
                ->add_param((new Param('foo', Types::String))->set_value('bar'))
                ->add_param((new Param('orderby', Types::String))->set_value('modified'))
                ->add_param((new Param('order', Types::String))->set_value('desc')),
            new Url(),
            (new Param_Collection())->add('foo', (new Param('foo', Types::String))->set_value('bar'))
        ];
        yield 'It sets orderby and order by default' => [
            (new Url())
                ->add_param((new Param('orderby', Types::String))->set_value('modified'))
                ->add_param((new Param('order', Types::String))->set_value('desc')),
            new Url(),
            null
        ];
    }
}
