<?php

namespace Eftersom\Larafeed\Services;

use Eftersom\Larafeed\Feed\Entry\RssEntry;
use Eftersom\Larafeed\Feed\Rss;
use Eftersom\Larafeed\Repositories\FeedRepository;
use Config;
use \DOMDocument;
use \DOMElement;
use \DOMXPath;
use Exception;
use Illuminate\Support\Facades\Cache;
use \Validator;
use Session;
use Stevebauman\Purify\Facades\Purify;

class ReaderService
{
    /**
     * @var array
     */
    private $acceptedTypes;

    /**
     * @var FeedRepository
     */
    private $feedRepository;

    /**
     * @var array
     */
    private $formattedFeed;

    /**
     * @var string
     */
    private $feedType;

    /**
     * ReaderService constructor.
     * @param FeedRepository $feedRepository
     */
    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
        $this->acceptedTypes  = Config::get('larafeed.accepted_types');
    }

    /**
     * @param string $url
     * @param string $mode
     * @return array
     * @throws Exception
     */
    public function processURL(string $url, string $mode = 'new'): array
    {
        $output       = null;
        $cachedOutput = cache($url . 'cached');
        $cachedFeed   = cache($url);

        $output = $this->feedData($url);

        if ((md5($output) === $cachedOutput) && $cachedFeed && $mode !== 'new') {
            return $cachedFeed;
        }

        $internalErrors = libxml_use_internal_errors(true);

        $domDocument    = new DOMDocument();
        $domDocument->loadXML($output);
        $xpath          = new DOMXPath($domDocument);

        libxml_use_internal_errors($internalErrors);

        $type           = $this->feedType($xpath);
        $this->feedType = $this->acceptedTypes[$type]['type'];
        $importedFeed   = $this->importFeedByTypeKey($type, $domDocument);
        $this->formatRSSFeed($importedFeed);
        $this->formatEntries($importedFeed);
        $this->validateFormattedFeed();

        if ($mode === 'new') {
            $this->cacheAndSave($url, $output);
        }

        return $this->formattedFeed;
    }

    /**
     * @param string $url
     * @param string $output
     * @throws Exception
     */
    private function cacheAndSave(string $url, string $output)
    {
        Cache::add($url .'cached', md5($output), 60);
        Cache::add($url, $this->formattedFeed, 60);

        $feed = $this->formattedFeed;

        unset($feed['entries']);
        $feed = $this->feedRepository->saveFeed($url, $feed);
       
        if ($feed === null) {
            throw new Exception('This feed already exists in your lists. Or was unable to be saved.');
        }
        $this->formattedFeed['id'] = $feed->id ?? null;
    }

    /**
     * @throws Exception
     */
    private function validateFormattedFeed()
    {
        $validator = Validator::make($this->formattedFeed, [
            'title'                    => 'required|string|max:255',
            'link'                     => 'required|url',
            'description'              => 'nullable|string|max:3000',
            'language'                 => 'nullable|string|max:255',
            'image'                    => 'nullable|url',
            'entries'                  => 'nullable',
            'entries.*.title'          => 'required|string|max:255',
            'entries.*.description'    => 'required|string|max:3000',
            'entries.*.published_date' => 'nullable|string|max:255',
            'entries.*.link'           => 'nullable|url',
            'entries.*.image'          => 'nullable|url',
        ]);

        if ($validator->fails()) {
            throw new Exception('The server for your rss feed responded with an invalid dataset.');
        }
    }

    /**
     * @param ?string $url
     * @return null|string
     * @throws Exception
     */
    private function feedData(?string $url, $downloadType = 'feed'): ?string
    {
        try {
            $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:104.0) Gecko/20100101 Firefox/104.0';    

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($downloadType === 'website') {  
                curl_setopt($ch, CURLOPT_RANGE, '0-5000');
            }
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['SERVER_NAME']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_ENCODING , 'identity');
            $file = curl_exec($ch);

            curl_close($ch);
        } catch (Exception $e) {
            $error = $e->getMessage();

            throw new Exception($error);
        }

        return $file ?? null;
    }

    /**
     * Format the feed into an easily readable array with feed entries.
     *
     * @param $feed
     * @throws Exception
     */
    private function formatRSSFeed($feed)
    {
        $this->formattedFeed['title']       = Purify::clean($feed->title());
        $this->formattedFeed['link']        = Purify::clean($feed->link());
        $this->formattedFeed['description'] = Purify::clean($feed->description());
        $this->formattedFeed['language']    = Purify::clean($feed->language());
        $this->formattedFeed['image']       = Purify::clean($this->imageFallbackCheck($feed->image()));
    }

    /**
     * @param $feed
     * @throws Exception
     */
    public function formatEntries($feed)
    {
        $entries = $feed->entries();

        if (!count($entries)) {
            throw new Exception('There are no entries for this feed or the feed XML is invalid.');
        }

        foreach($entries as $key => $entry) {
            if ($entry) {
                $this->formattedFeed['entries'][$key] = $this->formatRSSFeedEntry($key, $entry);
            }
        }
    }

    /**
     * @param string $key
     * @param DOMElement $entry
     * @return array|null
     * @throws Exception
     */
    private function formatRSSFeedEntry(string $key, DOMElement $entry): ?array
    {
        $entryType =  'Eftersom\\Larafeed\\Feed\\Entry\\' . ucfirst($this->feedType) . 'Entry';
        
        $entry = new $entryType($key, $entry);

        if (!$entry) {
            throw new Exception('The XML for entry with key ' . $key . ' is invalid or cannot be processed.');
        }

        $description =  substr($entry->description(), 0, 3000);
        
        if (strlen($entry->description()) > 3000) {
            $descripton = $description . '...';
        }

        $feedEntry['title']          = Purify::clean($entry->title());
        $feedEntry['description']    = Purify::clean($description);
        $feedEntry['published_date'] = Purify::clean($entry->datePublished());
        $feedEntry['link']           = Purify::clean($entry->link());
        $feedEntry['image']          = Purify::clean($entry->image() ?? $this->entryFallbackImage($entry->link()));
        
        return $feedEntry ?? null;
    }

    /**
     * @param string $typeKey
     * @param DOMDocument $domDocument
     * @throws Exception
     */
    private function importFeedByTypeKey(string $typeKey, DOMDocument $domDocument)
    {
        $modelType = 'Eftersom\\Larafeed\\Feed\\' .  ucfirst($this->acceptedTypes[$typeKey]['type']);
        
        $output = new $modelType($typeKey, $domDocument);

        return $output ?? null;
    }

    /**
     * Get the type of my feed.
     *
     * @param DOMXPath $xpath
     * @return string|null
     * @throws Exception
     */
    private function feedType(DOMXPath $xpath): ?string
    {
        foreach ($this->acceptedTypes as $typeKey => $acceptedType) {
            $xpath->registerNamespace('namespace', $acceptedType['namespace']); 

            if ($xpath->query($acceptedType['query'])->length) {
                if ($acceptedType['type'] === 'rss') {
                    $rssVersion = $xpath->evaluate('string(/' . $acceptedType['type'] . '/@version)');

                    if ($rssVersion === $acceptedType['version']) {
                        return $typeKey;
                    }
                } else {
                    return $acceptedType['type'];
                }
            }
        }
        
        throw new Exception('The version of the feed you are loading is not currently supported.');
    }

    /**
     * @param string|null $imageLink
     * @return string|null
     * @throws Exception
     */
    private function imageFallbackCheck(?string $imageLink): ?string
    {
        if (!$imageLink && $this->formattedFeed['link']) {
            $imageLink = $this->metaTagImageLink();
        }

        return $imageLink;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    private function metaTagImageLink(): ?string
    {
        $image          = null;
        $finalValidator = null;
        $website        = $this->feedData($this->formattedFeed['link'], 'website');

        $internalErrors = libxml_use_internal_errors(true);

        $domDocument = new DOMDocument();
        $domDocument->loadHtml($website);

        libxml_use_internal_errors($internalErrors);

        $metaTags = $domDocument->getElementsByTagName('meta');

        if ($metaTags) {
            foreach ($metaTags as $metaTag) {
                if ($metaTag->getAttribute('property') === 'twitter:image' ||
                    $metaTag->getAttribute('name') === 'twitter:image'
                ) {
                    $image['link'] = $metaTag->getAttribute('content');
                }
            }
        }

        return $this->imageLinkValid($image) ?? null;
    }

    /**
     * @param $link
     * @return string|null
     * @throws Exception
    */
    private function entryFallbackImage(?string $link)
    {
        $image = [];
        $website = $this->feedData($link, 'website');

        $internalErrors = libxml_use_internal_errors(true);

        $domDocument = new DOMDocument();
        $domDocument->loadHtml($website);
        
        $xpath = new DOMXPath($domDocument);

        libxml_use_internal_errors($internalErrors);
        
        foreach ($domDocument->getElementsByTagName('img') as $key => $img) {
            //Avoid the first image found which can often be a logo or headline banner image.
            if ($key === 1) {
                $image['link'] = $img->getAttribute('src');

                return $this->imageLinkValid($image);
            }
        }

        return null;
    }

    /**
     * @param $image
     * @return string|null
     * @throws Exception
     */
    private function imageLinkValid(?array $image) 
    {
        if ($image) {
            $validator = $this->validateImageUrl($image);

            if ($validator->fails($image)) {
                $urlRoot = $this->ensureURLIsHost();
                $image['link'] = $urlRoot . $image['link'];

                $finalValidator = $this->validateImageUrl($image);
                if ($finalValidator->fails()) {
                    $image = null;
                }
            }

            if ($image['link']) {
                $image = $image['link'];
            }
        }
        return $image ?? null;
    }

    /**
     * @param $image
     * @return Illuminate\Validation\Validator
     */
    private function validateImageUrl($image)
    {
        $validator = Validator::make($image, [
            'link' => 'required|url',
        ]);

        return $validator;
    }

    /**
     * @return string
     */
    private function ensureURLIsHost(): string
    {
        $urlParts = parse_url($this->formattedFeed['link']);
        
        return $urlParts['scheme'] . '://' . $urlParts['host'];
    }
}
