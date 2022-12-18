<?php

namespace Adiungo\Integrations\WordPress\Builders;

use Adiungo\Core\Abstracts\Page_Based_Batch_Builder;
use Underpin\Enums\Types;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Exceptions\Unknown_Registry_Item;
use Underpin\Exceptions\Validation_Failed;
use Underpin\Factories\Registry_Items\Param;

class Batch_Builder extends Page_Based_Batch_Builder
{
    /**
     * Sets the page
     *
     * @throws Operation_Failed
     */
    public function set_page(int $page): static
    {
        try {
            $this->get_request()->set_param($this->get_page_param()->set_value($page));
        } catch (Validation_Failed $e) {
            throw new Operation_Failed('Could not set page.', previous: $e);
        }

        return $this;
    }

    /**
     * Constructs a page param.
     *
     * @return Param
     */
    protected function get_page_param(): Param
    {
        return new Param('page', Types::Integer);
    }

    /**
     * Gets the page from teh request
     *
     * @return int
     * @throws Unknown_Registry_Item
     */
    public function get_page(): int
    {
        $page = $this->get_request()->get_param('page')->get_value();

        if (is_int($page)) {
            return $page;
        }

        throw new Unknown_Registry_Item('could not fetch page.', $this->get_request()->get_url()->get_params()::class);
    }
}
