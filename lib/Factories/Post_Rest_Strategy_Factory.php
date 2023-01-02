<?php

namespace Adiungo\Integrations\WordPress\Factories;

use Adiungo\Core\Factories\Adapters\Data_Source_Adapter;
use Adiungo\Core\Factories\Category;
use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Factories\Index_Strategy;
use Adiungo\Core\Factories\Tag;
use Adiungo\Core\Factories\Updated_Date_Strategy;
use Adiungo\Core\Interfaces\Has_Http_Strategy;
use Adiungo\Core\Interfaces\Has_Index_Strategy;
use Adiungo\Core\Traits\With_Http_Strategy;
use Adiungo\Core\Traits\With_Index_Strategy;
use Adiungo\Integrations\WordPress\Adapters\Batch_Response_Adapter;
use Adiungo\Integrations\WordPress\Adapters\Single_Response_Adapter;
use Adiungo\Integrations\WordPress\Models\Post;
use DateTime;
use Underpin\Enums\Types;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Exceptions\Unknown_Registry_Item;
use Underpin\Exceptions\Validation_Failed;
use Underpin\Factories\Registry_Items\Param;
use Underpin\Factories\Request;
use Underpin\Factories\Url;
use Underpin\Helpers\Array_Helper;
use Underpin\Helpers\String_Helper;
use Underpin\Registries\Param_Collection;
use Underpin\Traits\With_Object_Cache;

class Post_Rest_Strategy_Factory implements Has_Http_Strategy, Has_Index_Strategy
{
    use With_Http_Strategy;
    use With_Index_Strategy;
    use With_Object_Cache;

    /**
     * Fetches an instance of the rest class.
     *
     * @return Rest
     */
    protected function get_rest_instance(): Rest
    {
        return new Rest();
    }

    /**
     * Assembles the items specific to this integration that are always the same.
     *
     * @return Rest
     */
    protected function get_instance_template(): Rest
    {
        return clone $this->load_from_cache('template', function () {
            return $this->get_rest_instance()
                ->set_content_model_instance(Post::class)
                ->set_data_source_adapter($this->build_data_source_adapter())
                ->set_single_response_adapter(new Single_Response_Adapter())
                ->set_batch_response_adapter(new Batch_Response_Adapter());
        });
    }

    /**
     * Builds the data source adapter for posts.
     *
     * @return Data_Source_Adapter
     * @throws Operation_Failed
     */
    protected function build_data_source_adapter(): Data_Source_Adapter
    {
        return (new Data_Source_Adapter())
            ->set_content_model_instance(Post::class)
            ->map_field('id', 'set_id', Types::Integer)
            ->map_field('link', 'set_origin', fn (string $origin) => Url::from($origin))
            ->map_field('content.rendered', 'set_content', Types::String)
            ->map_field('excerpt.rendered', 'set_excerpt', Types::String)
            ->map_field('title.rendered', 'set_name', Types::String)
            ->map_field('modified_gmt', 'set_updated_date', fn (string $value) => $this->adapt_date($value))
            ->map_field('categories', 'add_categories', fn (array $categories) => Array_Helper::map($categories, fn (int $id) => (new Category())->set_id($id)))
            ->map_field('tags', 'add_tags', fn (array $tags) => Array_Helper::map($tags, fn (int $id) => (new Tag())->set_id($id)))
            ->map_field('date_gmt', 'set_published_date', fn (string $value) => $this->adapt_date($value));
    }

    /**
     * Adapts the date into the specified format.
     *
     * @param string $value
     * @return DateTime
     * @throws Operation_Failed
     */
    protected function adapt_date(string $value): DateTime
    {
        // If the string doesn't include a timezone, assume GMT.
        if (!str_contains($value, '+')) {
            $value = String_Helper::append($value, '+00:00');
        }

        $result = DateTime::createFromFormat(DATE_ATOM, $value);

        return $result ?: throw new Operation_Failed('Could not create date');
    }

    /**
     * Builds the index strategy
     *
     * @param Url $base The base URL to use to fetch content. Usually this is the REST
     *                                                  endpoint used to fetch bulk content.
     * @param DateTime $last_requested The date/time this content was last requested. Use GMT timezone.
     * @param Param_Collection|null $batch_query_params Any custom query params that need to be included on batch
     *                                                  requests.
     *
     * @return Index_Strategy
     * @throws Operation_Failed
     * @throws Validation_Failed
     */
    public function build(Url $base, DateTime $last_requested, ?Param_Collection $batch_query_params = null): Index_Strategy
    {
        return (new Index_Strategy())
            ->set_data_source($this->build_data_source($base, $last_requested, $batch_query_params));
    }

    /**
     * Builds the has more strategy using the provided date.
     *
     * @param DateTime $last_requested The last requested date. Use GMT.
     *
     * @return Updated_Date_Strategy
     */
    protected function build_has_more_strategy(DateTime $last_requested): Updated_Date_Strategy
    {
        return (new Updated_Date_Strategy())->set_updated_date($last_requested);
    }

    /**
     * Attempts to set a param on the URL, but does not set if it already exists.
     *
     * @param Url $base
     * @param Param $param
     * @return Url
     * @throws Operation_Failed
     */
    protected function maybe_set_param(Url $base, Param $param): Url
    {
        try {
            $base->get_params()->get($param->get_id());
        } catch (Unknown_Registry_Item $e) {
            $base->add_param($param);
        }

        return $base;
    }

    /**
     * Builds the batch request.
     *
     * @param Url $base
     * @param Param_Collection|null $batch_query_params
     *
     * @return Request
     * @throws Operation_Failed
     * @throws Validation_Failed
     */
    protected function build_batch_request(Url $base, ?Param_Collection $batch_query_params = null): Request
    {
        if ($batch_query_params instanceof Param_Collection) {
            $batch_query_params->each(fn (Param $param) => $this->maybe_set_param($base, $param));
        }

        // Set the order and order by params.
        $this->maybe_set_param($base, (new Param('orderby', Types::String))->set_value('modified'));
        $this->maybe_set_param($base, (new Param('order', Types::String))->set_value('desc'));

        return (new Request())->set_url($base);
    }

    /**
     * Builds the single request.
     *
     * @param Url $base
     *
     * @return Request
     */
    protected function build_single_request(Url $base): Request
    {
        return (new Request())->set_url($base);
    }

    /**
     * Builds the instance.
     * Note that this does NOT implement set the HTTP strategy. That is up to the platform to set.
     *
     * @throws Operation_Failed|Validation_Failed
     */
    protected function build_data_source(Url $base, DateTime $last_requested, ?Param_Collection $batch_query_params = null): Rest
    {
        $result = $this->get_instance_template();
        $result->get_single_request_builder()->set_request($this->build_single_request($base));
        $result->get_batch_request_builder()->set_request($this->build_batch_request($base, $batch_query_params));
        $result->set_has_more_strategy($this->build_has_more_strategy($last_requested));

        return $result;
    }
}
