<?php

namespace Adiungo\Integrations\WordPress\Adapters;

use Adiungo\Core\Collections\Attachment_Collection;
use Adiungo\Core\Factories\Attachments\Audio;
use Adiungo\Core\Factories\Attachments\Image;
use Adiungo\Core\Factories\Attachments\Video;
use Adiungo\Core\Interfaces\Attachment;
use Adiungo\Core\Interfaces\Has_Content;
use Adiungo\Core\Traits\With_Content;
use Underpin\Factories\Url;
use Underpin\Helpers\Array_Helper;
use Underpin\Helpers\String_Helper;

class Content_To_Attachment_Collection_Adapter implements Has_Content
{
    use With_Content;

    protected function locate_attachments(): array
    {
        preg_match_all("/src=?\\[\'\"](.+?)?\\[\'\"]/gm", $this->get_content(), $matches);

        return Array_Helper::unique($matches[0]) ?? [];
    }

    protected function build_attachment(string $url): Attachment
    {
        $prepared = Url::from($url);
        //TODO: NEED TO DETERMINE ALL MIME TYPES TO CHECK.
        return match (String_Helper::after('.', $prepared->get_path())) {
            'mp3', 'wav', 'flac' => (new Audio())->set_origin($prepared)->set_id($url),
            'mp4', 'mov' => (new Video())->set_origin($prepared)->set_id($url),
            'default' => (new Image())->set_origin($prepared)->set_id($url),
        };
    }

    public function to_collection(): Attachment_Collection
    {
        return (new Attachment_Collection())->seed(Array_Helper::process($this->locate_attachments())->map([$this, 'build_attachment']));
    }
}