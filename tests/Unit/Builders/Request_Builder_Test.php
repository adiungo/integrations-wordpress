<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Builders;

use Adiungo\Integrations\WordPress\Builders\Request_Builder;
use Adiungo\Tests\Test_Case;
use Adiungo\Tests\Traits\With_Simple_Setter_Getter_Tests;
use Generator;
use Underpin\Factories\Request;
use Underpin\Factories\Url;

class Request_Builder_Test extends Test_Case
{
    use With_Simple_Setter_Getter_Tests;

    protected object $instance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = (new Request_Builder())->set_request((new Request())->set_url(Url::from('https://www.example.com')));
    }

    protected function get_setters_and_getters(): Generator
    {
        yield 'id int' => ['set_id', 'get_id', 123];
        yield 'id null' => ['set_id', 'get_id', null];
    }
}
