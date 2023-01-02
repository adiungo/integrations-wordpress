<?php

namespace Adiungo\Integrations\WordPress\Traits;

trait With_Fallback_Id
{
    protected string $id;
    protected int $remote_id;

    /**
     * Updates get_id to fall back to remote ID when default ID is not set.
     *
     * This is necessary in some cases because the ID is expected when adding the item to the registry.
     * Some remote calls only provide the ID of a category/tag, but we need to construct a category to store in a transient
     * and fetch later. This allows that to happen.
     *
     * @return string
     */
    public function get_id(): string
    {
        if (isset($this->remote_id) && !isset($this->id)) {
            return (string)$this->remote_id;
        }

        return $this->id;
    }
}
