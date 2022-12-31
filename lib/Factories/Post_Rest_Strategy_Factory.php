<?php

namespace Adiungo\Integrations\WordPress\Factories;

use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Factories\Index_Strategy;
use Adiungo\Core\Interfaces\Has_Http_Strategy;
use Adiungo\Core\Interfaces\Has_Index_Strategy;
use Adiungo\Core\Traits\With_Http_Strategy;
use Adiungo\Core\Traits\With_Index_Strategy;
use DateTime;
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
     * @param Url $base The base URL to use to fetch content. Usually this is the REST endpoint used to fetch bulk content.
     * @param DateTime $last_requested The date/time this content was last requested. Use GMT timezone.
     * @param Param_Collection|null $batch_query_params Any custom query params that need to be included on batch requests.
     * @return Index_Strategy
     */
    public function build(Url $base, DateTime $last_requested, ?Param_Collection $batch_query_params = null): Index_Strategy
    {
        return (new Index_Strategy())
            ->set_data_source($this->build_data_source($base, $last_requested, $batch_query_params));
    }

    /**
     * Builds the instance.
     * Note that this does NOT implement set the HTTP strategy. That is up to the platform to set.
     */
    protected function build_data_source(Url $base, DateTime $last_requested, ?Param_Collection $batch_query_params): Rest
    {
        return new Rest();
    }
}
