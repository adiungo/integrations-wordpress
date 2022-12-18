<?php

namespace Adiungo\Integrations\WordPress\Adapters;

use Adiungo\Core\Abstracts\Single_Response_Adapter as Core_Single_Response_Adapter;
use Underpin\Helpers\Array_Helper;

class Single_Response_Adapter extends Core_Single_Response_Adapter
{
    /**
     * Converts the response to an array of data.
     *
     * @return mixed[]
     */
    public function to_array(): array
    {
        $response = (new Batch_Response_Adapter())->set_response($this->get_response())->to_array();

        return empty($response) ? [] : Array_Helper::wrap($response[0]);
    }

}