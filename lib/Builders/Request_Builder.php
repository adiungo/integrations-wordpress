<?php

namespace Adiungo\Integrations\WordPress\Builders;

use Adiungo\Core\Abstracts\Int_Id_Based_Request_Builder;
use Underpin\Enums\Types;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Exceptions\Unknown_Registry_Item;
use Underpin\Factories\Registry_Items\Param;

class Request_Builder extends Int_Id_Based_Request_Builder
{
    /**
     * {@inheritDoc}
     */
    public function get_id(): ?int
    {
        try {
            $result = $this->get_request()->get_url()->get_params()->get('id')->get_value();

            if (!is_int($result)) {
                return null;
            }

            return $result;
        } catch (Unknown_Registry_Item $exception) {
            return null;
        }
    }

    /**
     * Sets the ID.
     * @param ?int $id The ID to set
     *
     * return $this
     * @return static
     */
    public function set_id(?int $id): static
    {
        if (!$id) {
            try {
                $this->get_request()->get_url()->remove_param('id');
            } catch (Operation_Failed $e) {
                return $this;
            }
        } else {
            $this->get_request()->set_param((new Param('id', Types::Integer))->set_value($id));
        }

        return $this;
    }
}
