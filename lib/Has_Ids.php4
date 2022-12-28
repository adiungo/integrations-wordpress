<?php

namespace Adiungo\Integrations\WordPress;

interface Has_Ids
{
    public function get_ids(): array;

    public function set_ids(int|string|null $id, array ...$ids): static;
}