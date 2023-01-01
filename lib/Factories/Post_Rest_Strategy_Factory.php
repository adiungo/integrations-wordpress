<?php

namespace Adiungo\Integrations\WordPress\Factories;

use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Factories\Index_Strategy;
use Adiungo\Core\Factories\Updated_Date_Strategy;
use Adiungo\Core\Interfaces\Has_Http_Strategy;
use Adiungo\Core\Interfaces\Has_Index_Strategy;
use Adiungo\Core\Traits\With_Http_Strategy;
use Adiungo\Core\Traits\With_Index_Strategy;
use DateTime;
use Underpin\Enums\Types;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Exceptions\Unknown_Registry_Item;
use Underpin\Exceptions\Validation_Failed;
use Underpin\Factories\Registry_Items\Param;
use Underpin\Factories\Request;
use Underpin\Factories\Url;
use Underpin\Registries\Param_Collection;

class Post_Rest_Strategy_Factory implements Has_Http_Strategy, Has_Index_Strategy
{
    use With_Http_Strategy;
    use With_Index_Strategy;

    /**
     * Assembles the items specific to this integration that are always the same.
     *
     * @return Rest
     */
    protected function get_instance_template(): Rest
    {
        return new Rest();
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
            $batch_query_params->each(fn(Param $param) => $this->maybe_set_param($base, $param));
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
