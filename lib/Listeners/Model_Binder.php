<?php

namespace Adiungo\Integrations\WordPress\Listeners;

use Adiungo\Core\Abstracts\Content_Model;
use Adiungo\Core\Events\Content_Model_Event;
use Adiungo\Core\Events\Providers\Content_Model_Provider;
use Adiungo\Core\Events\Queue_Index_Event;
use Adiungo\Core\Interfaces\Has_Content_Model_Instance;
use Adiungo\Core\Interfaces\Has_Index_Strategy;
use Adiungo\Core\Traits\With_Content_Model_Instance;
use Adiungo\Integrations\WordPress\Models\Post;

abstract class Model_Binder implements Has_Content_Model_Instance
{
    use With_Content_Model_Instance;

    /**
     * @var class-string<Content_Model>
     */
    protected string $bound_model;

    public function __construct(protected Has_Index_Strategy $strategy)
    {

    }

    public static function listen(string $instance, Has_Index_Strategy $strategy): void
    {
        (new static($strategy))
            ->set_content_model_instance($instance)
            ->add_hooks();
    }

    public function add_hooks()
    {
        Content_Model_Event::instance()->attach($this->get_content_model_instance(), 'save', [$this, 'index']);
    }

    /**
     * Fetches the list of IDs to use in this binder.
     * @return int[]
     */
    abstract protected function get_ids(): array;

    /**
     * @param Content_Model_Provider $provider
     * @return void
     */
    protected function index(Content_Model_Provider $provider): void
    {
        /* @var Post $model */
        $model = $provider->get_model();

        // First, loop through and create empty records.
        // We will circle back and fill this in once we have all the data.
        foreach($this->get_ids() as $id){
            //TODO: THIS WILL NEED TO BE AN ADAPTER to convert the ID into the model.
            (new $this->bound_model());
            Content_Model_Event::instance()->attach($this->bound_model, 'create', [$this, 'index']);
        }
        $this->get_ids()->each(fn(Content_Model $category) => $category->save());

        // Queue the event to actually fetch the data and store it in the database.
        Queue_Index_Event::instance()->broadcast($this->strategy);
    }
}