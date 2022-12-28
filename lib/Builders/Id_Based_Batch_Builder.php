<?php

namespace Adiungo\Integrations\WordPress\Builders;

use Adiungo\Core\Interfaces\Has_Paginated_Request;
use Adiungo\Integrations\WordPress\Has_Ids;
use Underpin\Enums\Types;
use Underpin\Exceptions\Operation_Failed;
use Underpin\Exceptions\Unknown_Registry_Item;
use Underpin\Exceptions\Validation_Failed;
use Underpin\Factories\Registry_Items\Param;
use Underpin\Factories\Request;

class Id_Based_Batch_Builder implements Has_Ids, Has_Paginated_Request
{


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

    public function get_next(): static
    {
        // TODO: Implement get_next() method.
    }

    function get_request(): Request
    {
        // TODO: Implement get_request() method.
    }

    function set_request(Request $request): static
    {
        // TODO: Implement set_request() method.
    }

    public function get_ids(): array
    {
        return $this->get_request()->get_param('include');
    }

    public function set_ids(int|string|null $id, array ...$ids): static
    {
        $this->get_request()->get_url()->add_param((new Param('include', Types::Array))->set_value($this->get_ids()));

        return $this;
    }
}
