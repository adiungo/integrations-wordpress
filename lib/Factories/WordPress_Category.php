<?php

namespace Adiungo\Integrations\WordPress\Factories;

use Adiungo\Core\Factories\Category;
use Adiungo\Core\Interfaces\Has_Remote_Id;
use Adiungo\Core\Traits\With_Remote_Id;
use Adiungo\Integrations\WordPress\Traits\With_Fallback_Id;

class WordPress_Category extends Category implements Has_Remote_Id
{
    use With_Remote_Id;
    use With_Fallback_Id;
}
