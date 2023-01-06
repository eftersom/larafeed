<?php

namespace Eftersom\Larafeed\Feed;

use Config;
use DOMDocument;

class Rss extends Feed
{
    const RSS_CHANNEL_TITLE          = 'string(/rss/channel/title)';
    const RSS_CHANNEL_LINK           = 'string(/rss/channel/link)';
    const RSS_CHANNEL_DESCRIPTION    = 'string(/rss/channel/description)';
    const RSS_CHANNEL_LANGUAGE       = 'string(/rss/channel/language)';
    const RSS_CHANNEL_IMAGE          = 'string(/rss/channel/image)';
    const RSS_CHANNEL_ENCLOSURE      = 'string(/rss/channel/enclosure)';
    const RSS_ENTRIES                = '//link';

    public $output;
    public $entries;

    /**
     * Rss constructor.
     * @param string $type
     * @param DOMDocument $domDocument
     */
    public function __construct(string $type, DOMDocument $domDocument)
    {
        parent::__construct($type, $domDocument);
    }

    /**
     * Get feed title.
     *
     * @return string
     */
    public function title(): string
    {
        $title = $this->xpath->evaluate(self::RSS_CHANNEL_TITLE);

        return $title;
    }

    /**
     * Get link to feed.
     *
     * @return string
     */
    public function link(): string
    {
        $link = $this->xpath->evaluate(self::RSS_CHANNEL_LINK);

        return $link;
    }

    /**
     * Get feed content via description signifier.
     *
     * @return string
     */
    public function description(): string
    {
        $description = $this->xpath->evaluate(self::RSS_CHANNEL_DESCRIPTION);

        return $description;
    }

    /**
     * Get the language of the feed.
     *
     * @return string
     */
    public function language(): string
    {
        $language = $this->xpath->evaluate(self::RSS_CHANNEL_LANGUAGE);

        return $language;
    }

    /**
     * Get image if available.
     *
     * @return string|null
     */
    public function image(): ?string
    {
        $items = $this->xpath->evaluate('/rss/channel/image');

        if ($items->length) {
            $imageData = $items->item(0);

            $imageData = explode(" ", trim($imageData->nodeValue));
            $imageData = array_filter($imageData);

            $image     = preg_grep('/(\.jpeg|\.jpg|\.png|\.gif)$/i', $imageData);
        }

        return isset($image[0]) ? rtrim($image[0]) : null;
    }

    /**
     * Return all entries in the provided feed xml.
     *
     * @param int $start
     * @param int $finish
     * @return array|null
     */
    public function entries(int $start = 0, int $finish = 5): ?array
    {
        $entryList = $this->xpath->evaluate(self::RSS_ENTRIES);

        if (!count($entryList)) {
            return null;
        }

        $entryCount = Config::get('larafeed.entry_count');
    
        if ($finish > $entryCount) {
            $finish = $entryCount;
        } 
        
        for ($index = $start; $index < $finish; $index++) {
            $entries[$index] = $entryList[$index];
        }

        return $entries ?? null;
    }
}
