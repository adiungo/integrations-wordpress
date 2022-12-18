<?php

namespace Adiungo\Integrations\WordPress\Factories;

use Adiungo\Core\Factories\Adapters\Data_Source_Adapter;
use Adiungo\Core\Factories\Data_Sources\CSV;
use Adiungo\Core\Factories\Index_Strategy;

class CSV_Strategy_Factory
{
    /**
     * @param string $csv
     * @param Data_Source_Adapter $adapter
     * @return Index_Strategy
     */
    public function build(string $csv, Data_Source_Adapter $adapter): Index_Strategy
    {
        return new Index_Strategy();
    }

    public function build_csv_instance(string $csv, Data_Source_Adapter $adapter): CSV
    {
        return new CSV();
    }
}
