<?php

namespace Adiungo\Integrations\WordPress\Adapters;

use Adiungo\Core\Abstracts\Batch_Response_Adapter as Core_Batch_Response_Adapter;
use Underpin\Helpers\Array_Helper;

class Batch_Response_Adapter extends Core_Batch_Response_Adapter
{
    /**
     * @return mixed[][]
     */
    public function to_array(): array
    {
        $response = Array_Helper::wrap(json_decode($this->get_response(), true));

        return Array_Helper::wrap($response['posts'] ?? []);
    }
}
