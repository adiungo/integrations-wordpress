<?php

namespace Adiungo\Integrations\WordPress\Tests\Unit\Models;


use Adiungo\Integrations\WordPress\Models\Post;
use Adiungo\Tests\Test_Case;

class Post_Test extends Test_Case {

    public function test_can_access_model(): void
    {
        $this->assertInstanceOf(Post::class, new Post());
    }

}