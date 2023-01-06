<?php

namespace Eftersom\Larafeed\Feed\Entry;

use Carbon\Carbon;
use DOMElement;

class RssEntry extends Entry
{
    /**
     * RssEntry constructor.
     * @param string $key
     * @param DOMElement $domElement
     */
    public function __construct(string $key, DOMElement $domElement)
    {
        parent::__construct($key, $domElement);
    }

    /**
     * Get entry title.
     *
     * @return string|null
     */
    public function title(): ?string
    {
        $title = $this->xpath->evaluate('string(' .  $this->entryXPath . '/title)');

        if (is_null($title)) {
            return null;
        }

        return $title;
    }

    /**
     * Get specific link provided by the entry.
     *
     * @param int $index
     * @return string|null
     */
    public function link(int $index = 0): ?string
    {
        $links = $this->allLinks();
        return $links[$index] ?? null;
    }

    /**
     * Get all links associated with this entry.
     *
     * @return array|null
     */
    public function allLinks(): ?array
    {
        $links      = [];
        $entryLinks = $this->xpath->query($this->entryXPath . '//link');

        foreach ($entryLinks as $link) {
            $links[] = $link->nodeValue;
        }

        return $links ?? null;
    }

    /**
     * Get publish date.
     *
     * @return string
     */
    public function datePublished(): string
    {
        $datePublished = $this->xpath->evaluate('string(' .  $this->entryXPath . '/pubDate)');

        $datePublished = Carbon::parse($datePublished)->toDayDateTimeString();

        return $datePublished;
    }

        /**
     * Get content of this entry, or a description of summary content.
     *
     * @return null|string
     */
    public function image(): ?string
    {
        $description = $this->xpath->evaluate('string(' .  $this->entryXPath . '/description)');

        if (preg_match('/src="(.*?)"/', $description, $matches)) {
            $src = $matches[1];
        }

        //if nothing is returned by the above script, perhaps an enclosure has been set.
        foreach ( $this->xpath->query("//enclosure") as $enclosure ) {
            $src = $enclosure->getAttribute("url");
        }
        
        return $src ?? null;
    }
}
