<?php

namespace Eftersom\Larafeed\Interfaces;

interface EntryInterface
{
    /**
     * Get entry title.
     *
     * @return string
     */
    public function title();

    /**
     * Get specific link provided by the entry.
     *
     * @param int $index
     * @return string
     */
    public function link(int $index = 0);

    /**
     * Get all links associated with this entry.
     *
     * @return array
     */
    public function allLinks();

    /**
     * Get content of this entry, or a description of summary content.
     *
     * @return string
     */
    public function description();

    /**
     * @return string
     */
    public function datePublished();
}
