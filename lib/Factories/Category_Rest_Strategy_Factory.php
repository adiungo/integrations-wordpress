<?php

namespace Adiungo\Integrations\WordPress\Factories;


use Adiungo\Core\Factories\Adapters\Data_Source_Adapter;
use Adiungo\Core\Factories\Data_Sources\Rest;
use Adiungo\Core\Factories\Updated_Date_Strategy;
use Adiungo\Core\Interfaces\Has_Content_Model_Collection;
use Adiungo\Core\Interfaces\Has_Http_Strategy;
use Adiungo\Core\Interfaces\Has_Index_Strategy;
use Adiungo\Core\Traits\With_Batch_Response_Adapter;
use Adiungo\Core\Traits\With_Content_Model_Collection;
use Adiungo\Core\Traits\With_Http_Strategy;
use Adiungo\Core\Traits\With_Index_Strategy;
use Adiungo\Integrations\WordPress\Adapters\Batch_Response_Adapter;
use Adiungo\Integrations\WordPress\Adapters\Single_Response_Adapter;
use Adiungo\Integrations\WordPress\Builders\Batch_Builder;
use Adiungo\Integrations\WordPress\Builders\Id_Based_Batch_Builder;
use Adiungo\Integrations\WordPress\Builders\Request_Builder;
use Adiungo\Integrations\WordPress\Models\Post;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Factories\Url;
use Underpin\Interfaces\Identifiable_String;
use Underpin\Traits\With_Object_Cache;
use Underpin\Traits\With_String_Identity;

class Category_Rest_Strategy_Factory implements Has_Http_Strategy, Has_Index_Strategy, Has_Content_Model_Collection, Identifiable_String
{
    use With_Http_Strategy;
    use With_Index_Strategy;
    use With_Content_Model_Collection;
    use With_String_Identity;
    use With_Object_Cache;

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

    public function build(Url $base): static
    {
        return clone $this->load_from_cache('template', function () {

            // Create the data source.
            return (new Rest())
                ->set_single_request_builder(new Request_Builder())
                ->set_batch_request_builder(new Id_Based_Batch_Builder())
                ->set_content_model_instance(Post::class)
                ->set_data_source_adapter($this->get_data_source_adapter())
                ->set_http_strategy($this->get_http_strategy())
                ->set_has_more_strategy(new Id_Based_Has_More_Strategy())
                ->set_single_response_adapter(new Single_Response_Adapter())
                ->set_batch_response_adapter(new Batch_Response_Adapter());
        });
        // TODO: IMPLEMENT build
        // This should use Id_Based_Batch_Builder for the builder.
    }

    /**
     * Builds the instance.
     * Note that this does NOT implement set the HTTP strategy. That is up to the platform to set.
     */
    protected function build_data_source(Url $base): Rest
    {
        //TODO: IMPLEMENT build_data_source
    }
}