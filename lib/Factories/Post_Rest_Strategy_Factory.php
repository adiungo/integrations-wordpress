<?php

namespace Adiungo\Integrations\WordPress\Factories;

use Adiungo\Core\Events\Providers\Index_Strategy_Provider;
use Adiungo\Core\Events\Queue_Index_Event;
use Adiungo\Core\Factories\Adapters\Data_Source_Adapter;
use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Factories\Index_Strategy;
use Adiungo\Core\Factories\Updated_Date_Strategy;
use Adiungo\Core\Interfaces\Has_Http_Strategy;
use Adiungo\Core\Interfaces\Has_Index_Strategy;
use Adiungo\Core\Traits\With_Http_Strategy;
use Adiungo\Core\Traits\With_Index_Strategy;
use Adiungo\Integrations\WordPress\Adapters\Batch_Response_Adapter;
use Adiungo\Integrations\WordPress\Adapters\Single_Response_Adapter;
use Adiungo\Integrations\WordPress\Builders\Batch_Builder;
use Adiungo\Integrations\WordPress\Builders\Request_Builder;
use Adiungo\Integrations\WordPress\Listeners\Model_Binder;
use Adiungo\Integrations\WordPress\Models\Post;
use DateTime;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Factories\Url;
use Underpin\Traits\With_Object_Cache;

class Post_Rest_Strategy_Factory implements Has_Http_Strategy, Has_Index_Strategy
{
    use With_Http_Strategy;
    use With_Object_Cache;
    use With_Index_Strategy;

    public function __construct(protected string $id)
    {
    }

    /**
     * Assembles the items specific to this integration that are always the same.
     *
     * @return Rest
     * @throws Operation_Failed
     */
    protected function get_instance_template(): Rest
    {
        return clone $this->load_from_cache('template', function () {

            // Create the data source.
            return (new Rest())
                ->set_single_request_builder(new Request_Builder())
                ->set_batch_request_builder((new Batch_Builder())->set_page(1))
                ->set_content_model_instance(Post::class)
                ->set_data_source_adapter($this->get_data_source_adapter())
                ->set_http_strategy($this->get_http_strategy())
                ->set_has_more_strategy((new Updated_Date_Strategy())->set_updated_date(new DateTime()))
                ->set_single_response_adapter(new Single_Response_Adapter())
                ->set_batch_response_adapter(new Batch_Response_Adapter());
        });
    }

    protected function get_data_source_adapter(): Data_Source_Adapter
    {
        return (new Data_Source_Adapter())
            ->map_field('post_title', 'set_name')
            ->map_field('content', 'set_content')
            ->map_field('link', 'set_origin')
            ->map_field('excerpt', 'set_excerpt')
            ->map_field('modified_date', 'set_modified_date')
            ->map_field('published_date', 'set_published_date')
            ->map_field('id', 'set_id')
            ->set_content_model_instance(Post::class);
    }

    public function build(Url $posts_base, Url $authors_base, Url $categories_base, Url $tags_base, DateTime $last_requested): static
    {
        $strategy = (new Index_Strategy())->set_data_source($this->build_data_source($posts_base, $last_requested));
        // SET UP Category Listener

        Model_Binder::listen(Post::class,
            (new Category_Rest_Strategy_Factory())
                ->set_id($this->id . '_categories')
                ->build($authors_base)
        );


        return clone $this->set_index_strategy($strategy);
    }

    /**
     * Builds the instance.
     * Note that this does NOT implement set the HTTP strategy. That is up to the platform to set.
     */
    protected function build_data_source(Url $base, DateTime $last_requested): Rest
    {
        return (new Rest())->set_single_request_builder();
    }
}
