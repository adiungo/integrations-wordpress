<?php

namespace Adiungo\Integrations\WordPress\Factories;


use Adiungo\Core\Factories\Adapters\Data_Source_Adapter;
use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Interfaces\Has_Content_Model_Collection;
use Adiungo\Core\Interfaces\Has_Http_Strategy;
use Adiungo\Core\Interfaces\Has_Index_Strategy;
use Adiungo\Core\Traits\With_Content_Model_Collection;
use Adiungo\Core\Traits\With_Http_Strategy;
use Adiungo\Core\Traits\With_Index_Strategy;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Factories\Url;

class Author_Rest_Strategy_Factory implements Has_Http_Strategy, Has_Index_Strategy, Has_Content_Model_Collection
{
    use With_Http_Strategy;
    use With_Index_Strategy;
    use With_Content_Model_Collection;

    /**
     * Assembles the items specific to this integration that are always the same.
     *
     * @return Rest
     * @throws Operation_Failed
     */
    protected function get_instance_template(): Rest
    {
        //TODO: IMPLEMENT get_instance_template
    }

    protected function get_data_source_adapter(): Data_Source_Adapter
    {
        //TODO: IMPLEMENT get_data_source_adapter
    }

    public function build(Url $authors_base, array $ids): static
    {
        // TODO: IMPLEMENT build
    }

    /**
     * Builds the instance.
     * Note that this does NOT implement set the HTTP strategy. That is up to the platform to set.
     */
    protected function build_data_source(Url $base, array $ids): Rest
    {
        //TODO: IMPLEMENT build_data_source
    }
}