<?php

namespace Adiungo\Integrations\WordPress\Factories;

use Adiungo\Core\Factories\Tag;
use Adiungo\Core\Interfaces\Has_Remote_Id;
use Adiungo\Core\Traits\With_Remote_Id;
use Adiungo\Integrations\WordPress\Traits\With_Fallback_Id;

class WordPress_Tag extends Tag implements Has_Remote_Id
{
    use With_Remote_Id;
    use With_Fallback_Id;
}
