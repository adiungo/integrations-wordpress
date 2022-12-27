<?php

namespace Adiungo\Integrations\WordPress\Listeners;

use Adiungo\Core\Events\Content_Model_Event;
use Adiungo\Core\Events\Providers\Content_Model_Provider;
use Adiungo\Core\Events\Queue_Index_Event;
use Adiungo\Core\Factories\Category;
use Adiungo\Integrations\WordPress\Factories\Category_Rest_Strategy_Factory;
use Adiungo\Integrations\WordPress\Models\Post;

final class Index_Post_Categories_Listener
{

    public function __construct(protected string $strategy_id)
    {

    }

    public function listen()
    {
        Content_Model_Event::instance()->attach(Post::class, 'save', [$this, 'index_categories']);
    }

    protected function index_categories(Content_Model_Provider $provider): void
    {
        /* @var Post $model */
        $model = $provider->get_model();

        // First, loop through and create empty records for categories.
        // We will circle back and fill this in once we have all the data.
        $model->get_categories()->each(fn(Category $category) => $category->save());

        // Build the factory
        $factory = (new Category_Rest_Strategy_Factory())->set_id($this->strategy_id . '_categories')->build(/*...*/);

        // Queue the event to actually fetch the category data and store it in the database.
        Queue_Index_Event::instance()->broadcast($factory);
    }
}