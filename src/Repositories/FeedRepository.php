<?php

namespace Eftersom\Larafeed\Repositories;

use Eftersom\Larafeed\Models\Feed;
use Auth;

class FeedRepository
{
    /**
     * @param string $url
     * @param array $input
     * @return Feed|null
     */
    public function saveFeed(string $url, array $input): ?Feed
    {
        $input['link'] = $url;
        $first         = false;

        $feed = Feed::firstOrNew(['link' => $url]);

        $feed->fill($input);

        $userFeed = $this->userFeedById($feed->id);

        if (isset($userFeed->id)) {
            return null;
        }

        $feed->save();

        Auth::user()->feeds()->syncWithoutDetaching($feed);

        return isset($feed->title) ? $feed : null;
    }

    /**
     * @param ?string $id
     * @return Feed|null
     */
    public function feedById(?string $id): ?Feed
    {
        $feed = Feed::find($id);

        return isset($feed->id) ? $feed : null;
    }

    /**
     * @param ?string $id
     * @return Feed|null
     */
    public function userFeedById(?string $id): ?Feed
    {
        $feed = Auth::user()->feeds()->where('id', $id)->first();

        return isset($feed->id) ? $feed : null;
    }

    /**
     * @param string $slug
     * @return Feed|null
     */
    public function feedBySlug(string $slug): ?Feed
    {
        $feed = Feed::where('slug', $slug)->first();

        return isset($feed->title) ? $feed : null;
    }

    /**
     * @param string $link
     * @return Feed|null
     */
    public function feedByLink(string $link): ?Feed
    {
        $feed = Auth::user()->feeds()->get()->where('link', $link)->first();

        return isset($feed->title) ? $feed : null;
    }
}
