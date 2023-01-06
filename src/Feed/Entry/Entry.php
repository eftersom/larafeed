<?php

namespace Eftersom\Larafeed\Feed\Entry;

use Eftersom\Larafeed\Interfaces\EntryInterface;
use \DOMDocument;
use \DOMElement;
use \DOMXPath;

abstract class Entry implements EntryInterface
{
    /**
     * @var string
     */
    public $entryKey;

    /**
     * @var DOMElement
     */
    public $domElement;

    /**
     * @var DOMDocument|null
     */
    public $domDocument;

    /**
     * @var string
     */
    public $entryXPath;

    /**
     * @var DOMXPath
     */
    public $xpath;

    /**
     * Entry constructor.
     * @param string $key
     * @param DOMElement $domElement
     */
    public function __construct(string $key, DOMElement $domElement)
    {
        $this->domElement  = $domElement;
        $this->domDocument = $domElement->ownerDocument;
        $this->xpath       = new DOMXPath($this->domDocument);

        $this->entryKey    = $key;
        $this->entryXPath  = '//item[' . ($key + 1) . ']';
    }

    /**
     * Get content of this entry, or a description of summary content.
     *
     * @return null|string
     */
    public function description(): ?string
    {
        $description = $this->xpath->evaluate('string(' .  $this->entryXPath . '/description)');
       
        // $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i'; 
        $description = preg_replace("/<img[^>]+\>/i", "", $description);

        return $description ?? null;
    }
}
