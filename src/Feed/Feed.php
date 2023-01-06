<?php

namespace Eftersom\Larafeed\Feed;

use Eftersom\Larafeed\Interfaces\FeedInterface;
use \DOMDocument;
use \DOMXPath;

abstract class Feed implements FeedInterface
{
    /**
     * @var DOMDocument
     */
    private $domDocument;

    /**
     * @var string
     */
    private $type;

    /**
     * @var DOMXPath
     */
    public $xpath;

    /**
     * @var array
     */
    public $output = [];

    /**
     * Feed constructor.
     * @param string $type
     * @param DOMDocument $domDocument
     */
    public function __construct(string $type, DOMDocument $domDocument)
    {
        $this->type        = $type;
        $this->domDocument = $domDocument;
        $this->xpath       = new DOMXPath($this->domDocument);
    }

    /**
     * Return the type of my feed.
     *
     * @return string
     */
    public function feedType(): string
    {
        return $this->type;
    }
}
