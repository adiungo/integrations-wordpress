<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Traits;

use Adiungo\Integrations\WordPress\Traits\With_Fallback_Id;
use Adiungo\Tests\Test_Case;
use Adiungo\Tests\Traits\With_Inaccessible_Properties;
use Generator;
use Mockery;

class With_Fallback_Id_Test extends Test_Case
{
    use With_Inaccessible_Properties;

    /**
     * @covers       \Adiungo\Integrations\WordPress\Traits\With_Fallback_Id::get_id
     *
     * @param string $expected
     * @param string|null $id
     * @param int|null $fallback_id
     * @return void
     * @throws \ReflectionException
     * @dataProvider provider_can_get_id
     */
    public function test_can_get_id(string $expected, ?string $id, ?int $fallback_id): void
    {
        $instance = Mockery::mock(With_Fallback_Id::class);

        if ($id) {
            $this->set_protected_property($instance, 'id', $id);
        }

        if ($fallback_id) {
            $this->set_protected_property($instance, 'remote_id', $fallback_id);
        }

        $this->assertSame($expected, $instance->get_id());
    }

    public function provider_can_get_id(): Generator
    {
        yield 'No id gets fallback' => ['123', null, 123];
        yield 'With id gets id' => ['foo', 'foo', 345];
    }
}
