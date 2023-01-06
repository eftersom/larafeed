<?php

namespace Eftersom\Larafeed\Http\Controllers;

use Auth;
use Config;
use Eftersom\Larafeed\Http\Requests\FeedSearchRequest;
use Eftersom\Larafeed\Http\Requests\FeedUserDetachRequest;
use Eftersom\Larafeed\Models\User;
use Eftersom\Larafeed\Repositories\FeedRepository;
use Eftersom\Larafeed\Services\ReaderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Helpers;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use \Exception;

class FeedController extends Controller
{
    /**
     * @var ReaderService
     */
    private $reader;

    /**
     * @var FeedRepository
     */
    private $feedRepository;

    /**
     * FeedController constructor.
     * @param ReaderService $reader
     * @param FeedRepository $feedRepository
     */
    public function __construct(
        ReaderService $reader,
        FeedRepository $feedRepository
    )
    {
        $this->reader         = $reader;
        $this->feedRepository = $feedRepository;
    }

    /**
     * @return View
     */
    public function home(): View
    {
        return view(
            'larafeed::welcome',
            [
                'page'     => 'home',
                'previous' => route('feed-home'),
                'level'    => 1,
            ]
        );
    }

    /**
     * @return View
     */
    public function showAll(): View
    {
        return view(
            'larafeed::feed.show-all',
            [
                'feeds'    => Auth::check() ? Auth::user()->feeds()->paginate(Config::get('larafeed.pagination')) : collect(),
                'user'     => Auth::user()->id,
                'page'     => 'feed',
                'previous' => route('feed-home'),
                'level'    => 2,
            ]
        );
    }
    
    /**
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        return view(
            'larafeed::feed.show',
            [
                'feed'     => $this->feedShow($slug) ?? null,
                'page'     => 'all',
                'previous' => route('feed-show-all'),
                'level'    => 3,
            ]
        );
    }

    /**
     * @return View
     */
    public function showUserFeedAll(User $user): View
    {
        
        return view(
            'larafeed::feed.show-all',
            [
                'feeds'    => isset($user->id) ? $user->feeds()->paginate(Config::get('larafeed.pagination')) : collect(),
                'user'     => isset($user->id) ? $user->id : null,
                'page'     => 'feed',
                'previous' => route('feed-home'),
                'level'    => 2,
            ]
        );
    }

    /**
     * @param User $user
     * @return View
     */
    public function showUserFeed(User $user, string $slug): View
    {
        return view(
            'larafeed::feed.show',
            [
                'feed'     => $this->feedShow($slug) ?? null,
                'page'     => 'all',
                'previous' => route('feed-user', ['user' => $user]),
                'level'    => 3,

            ]
        );
    }
    
    /**
     * @param string $slug
     * @return array|null
     */
    private function feedShow(string $slug): ?array
    {
        $error       = null;

        try {
            $feed       = $this->feedRepository->feedBySlug($slug);
            $feedOutput = $this->reader->processURL($feed->link, 'show');
        } catch(Exception $e) {
            $error = $e->getMessage();
            Session::flash('danger', 'Something went wrong or you do not have a feed with this name.');
        }

        return $feedOutput ?? null;
    }

    /**
     * @param FeedSearchRequest $request
     * @return RedirectResponse
     */
    public function feedSearch(FeedSearchRequest $request): RedirectResponse
    {
        $error       = null;
        $feedOutput  = null;
        $input       = $request->all();

        try {
            $feedOutput = $this->reader->processURL($input['url'], 'new');
            $request->session()->flash('success', 'Feed added for ' . $feedOutput['title']);
        } catch(Exception $e) {
            $error = $e->getMessage();
            $request->session()->flash('danger', $error);
        }

        return Redirect(route('feed-show-all'));
    }

    /**
     * @param FeedUserDetachRequest $request
     * @return RedirectResponse
     */
    public function detatchUserFeed(FeedUserDetachRequest $request): RedirectResponse
    {   
        $input = $request->all();

        try {
            $feed = Auth::user()->feeds()->where('id', $input['id'])->first();

            $title = isset($feed->id) ? $feed->title : null;

            Auth::user()->feeds()->detach($input['id']);

            $request->session()->flash('success', 'Feed deleted with title ' . $title);
        } catch(Exception $e) {
            $error = $e->getMessage();
            $request->session()->flash('danger', $error);
        }
        
        return Redirect(route('feed-show-all'));
    }
}
