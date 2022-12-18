<?php

namespace Adiungo\Integrations\WordPress\Models;

use Adiungo\Core\Abstracts\Content_Model;
use Adiungo\Core\Interfaces\Has_Attachments;
use Adiungo\Core\Interfaces\Has_Author;
use Adiungo\Core\Interfaces\Has_Categories;
use Adiungo\Core\Interfaces\Has_Content;
use Adiungo\Core\Interfaces\Has_Excerpt;
use Adiungo\Core\Interfaces\Has_Name;
use Adiungo\Core\Interfaces\Has_Origin;
use Adiungo\Core\Interfaces\Has_Published_Date;
use Adiungo\Core\Interfaces\Has_Tags;
use Adiungo\Core\Interfaces\Has_Updated_Date;
use Adiungo\Core\Traits\With_Attachments;
use Adiungo\Core\Traits\With_Author;
use Adiungo\Core\Traits\With_Categories;
use Adiungo\Core\Traits\With_Content;
use Adiungo\Core\Traits\With_Excerpt;
use Adiungo\Core\Traits\With_Name;
use Adiungo\Core\Traits\With_Origin;
use Adiungo\Core\Traits\With_Published_Date;
use Adiungo\Core\Traits\With_Tags;
use Adiungo\Core\Traits\With_Updated_Date;
use Underpin\Interfaces\Identifiable_Int;
use Underpin\Traits\With_Int_Identity;

class Post extends Content_Model implements Identifiable_Int, Has_Tags, Has_Categories, Has_Content, Has_Name, Has_Attachments, Has_Origin, Has_Author, Has_Excerpt, Has_Published_Date, Has_Updated_Date
{
    use With_Tags;
    use With_Categories;
    use With_Content;
    use With_Name;
    use With_Attachments;
    use With_Origin;
    use With_Author;
    use With_Excerpt;
    use With_Published_Date;
    use With_Updated_Date;
    use With_Int_Identity;
}
