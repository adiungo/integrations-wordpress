<?php

namespace Adiungo\Integrations\WordPress\Strategies;


use Adiungo\Core\Collections\Content_Model_Collection;
use Adiungo\Core\Events\Queue_Index_Event;
use Adiungo\Core\Factories\Index_Strategy;
use Adiungo\Core\Factories\Index_Strategy as Index_Strategy_Core;
use Adiungo\Integrations\WordPress\Factories\Author_Rest_Strategy_Factory;
use Adiungo\Integrations\WordPress\Factories\Category_Rest_Strategy_Factory;
use Adiungo\Integrations\WordPress\Factories\Tag_Rest_Strategy_Factory;
use Underpin\Enums\Types;
use Underpin\Factories\Url;
use Underpin\Helpers\Array_Helper;

class Post_Index_Strategy extends Index_Strategy_Core
{

    public function __construct(protected Url $authors_base, protected Url $categories_base, protected Url $tags_base, protected Url $images_base)
    {

    }

    public function index_data(): void
    {
        $data = $this->get_data_source()->get_data();

        $this->index_categories($data);
        $this->index_authors($data);
        $this->index_tags($data);
        $this->index_images($data);

        parent::index_data();
    }

    public function index_item(int|string $id): void
    {
        $item = (new Content_Model_Collection())->seed([$this->get_data_source()->get_item($id)]);

        $this->index_categories($item);
        $this->index_authors($item);
        $this->index_tags($item);
        $this->index_images($item);

        parent::index_item($id);
    }

    protected function index_authors(Content_Model_Collection $posts): void
    {
        $authors = $this->pluck_ids($posts->pluck('author', null));

        if (empty($authors)) {
            return;
        }

        Queue_Index_Event::instance()->broadcast((new Author_Rest_Strategy_Factory())->build($this->authors_base, $authors)->set_content_model_collection($posts));
    }

    protected function index_categories(Content_Model_Collection $posts): void
    {
        $categories = $this->get_taxonomy_ids($posts, 'categories');

        if (empty($categories)) {
            return;
        }

        Queue_Index_Event::instance()->broadcast((new Category_Rest_Strategy_Factory())->build($this->categories_base, $categories)->set_content_model_collection($posts));
    }

    /**
     * Queues an index for tags
     *
     * @param Content_Model_Collection $posts
     * @return void
     */
    protected function index_tags(Content_Model_Collection $posts): void
    {
        $tags = $this->get_taxonomy_ids($posts, 'tags');

        if (empty($tags)) {
            return;
        }

        Queue_Index_Event::instance()->broadcast((new Tag_Rest_Strategy_Factory())->build($this->tags_base, $tags)->set_content_model_collection($posts));
    }

    /**
     * Queues an index to download images
     *
     * @param Content_Model_Collection $posts
     * @return void
     */
    protected function index_images(Content_Model_Collection $posts)
    {
        $featured = Array_Helper::where_not_null($posts->pluck('featured_media', null));

    }

    /**
     * Plucks IDs from a list of
     *
     * @param array $items
     * @return array
     */
    protected function pluck_ids(array $items): array
    {
        return Array_Helper::process($items)
            ->where_not_null()
            ->cast('integer')
            ->unique()
            ->to_array();
    }

    /**
     * Fetches unique taxonomy IDs from a collection of posts.
     *
     * @param Content_Model_Collection $posts
     * @param string $key
     * @return array
     */
    protected function get_taxonomy_ids(Content_Model_Collection $posts, string $key): array
    {
        $items = Array_Helper::where_not_null($posts->pluck($key, null));

        if (empty($items)) {
            return [];
        }

        return Array_Helper::process($items)
            ->cast(Types::Array->value)
            ->reduce(fn(array $acc, array $item) => array_merge($acc, $item ?? []), [])
            ->cast(Types::Integer->value)
            ->to_array();
    }
}