# Adiungo WordPress Integration

The WordPress integration makes it possible to index content on a WordPress website onto your own site.

<mark>NOTE: THIS IS STILL IN DEVELOPMENT. THE DOCUMENTATION BELOW SHOWS HOW THIS IS EXPECTED TO BE USED.</mark>

* [Repository](https://github.com/adiungo/integrations-wordpress)

## Installation

```php
composer require adiungo/integrations/wordpress
```

## Usage

This integration provides factory class that you can use on your platform to index the content on the site. Let's assume
that you're creating a WordPress plugin that can fetch posts from _other_ WordPress websites.

First, we must give the REST integration a way to actually _fetch_ the data. Adiungo does not come with a way to
actually make REST requests. This is the platform's responsibility, and as a result you would need to have
an `Http_Strategy` class similar to this in your own plugin. We'll use this one later.

```php
use Adiungo\Core\Abstracts\Http_Strategy;

// This is needed for REST, so it knows how to make requests.
class WordPress_Http_Strategy extends Http_Strategy
{

    /**
     * Uses the provided request to make a wp_remote_* request.
     * Returns the response body, as a string.
     *
     * @return string
     */
    public function to_string(): string
    {
        // This would probably use the WordPress Requests class to get the body. https://developer.wordpress.org/reference/classes/requests/
    }

    public function __toString()
    {
        return $this->to_string();
    }
}
```

Okay, now that we have created a class that describes how to actually fetch data via REST, let's go ahead and use
the [WordPress integration](https://docs.getadiungo.com/integrations/wordpress). In this case, we can use the WordPress
integration's `WordPress_Rest_Strategy_Factory` class to do build most of the rest strategy for us. All we need to do is
provide it with our HTTP strategy, and it's ready to go.

```php
$factory = (new WordPress_Rest_Strategy_Factory())->set_http_strategy(new WordPress_Http_Strategy());
```

Once that's done, you can use your factory to build as many [index strategies](https://docs.getadiungo.com/index-strategies) as you need. Each
strategy is associated with a different URL used to make the REST request,

```php
use Underpin\Factories\Url;

// First, Create our actual Index Strategy.
$factory = (new WordPress_Rest_Strategy_Factory())->set_http_strategy(new WordPress_Http_Strategy());

// Now, specify the REST URL. This should include any filters that provides the necessary specificity to ensure you don't get content that isn't yours.
$url = Url::from('https://blog.example.org/wp-json/wp/v2/posts?author=1');

// Use that URL in your strategy.
$strategy = $factory->build($url, new DateTime());
```

You now have a fully-formed Index Strategy, and as long as you have your model save events registered, can do things
like this:

```php
// Index all the things. This would continually fetch the data from the strategy, and index it until there's nothing left to fetch.
while ($strategy->get_data_source()->has_more()) {
    $strategy->index_data();
}

// Index a specific record.
$strategy->index_item(123);
```