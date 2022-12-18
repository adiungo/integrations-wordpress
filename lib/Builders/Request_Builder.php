<?php

namespace Adiungo\Integrations\WordPress\Builders;

use Adiungo\Core\Abstracts\Int_Id_Based_Request_Builder;

class Request_Builder extends Int_Id_Based_Request_Builder
{
    /**
     * {@inheritDoc}
     */
    public function get_id(): ?int
    {
        return null;
    }

    /**
     * Sets the ID.
     * @param ?int $id The ID to set
     *
     * return $this
     */
    public function set_id(?int $id): static
    {
        return $this;
    }
}
