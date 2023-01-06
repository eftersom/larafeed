<?php

namespace Eftersom\Larafeed\Interfaces;

interface FeedInterface
{
    /**
     * Get feed title.
     *
     * @return string
     */
    public function title();

    /**
     * Get link to feed.
     *
     * @return string
     */
    public function link();

    /**
     * Get feed content via description signifier.
     *
     * @return string
     */
    public function description();

    /**
     * Get the language of the feed.
     *
     * @return string
     */
    public function language();

    /**
     * Get feed logo or image.
     *
     * @return string
     */
    public function image();

    /**
     * Return all entries in the provided feed xml.
     *
     * @return array
     */
    public function entries();
}
