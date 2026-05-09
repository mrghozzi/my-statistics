<?php

namespace MyAds\Plugins\MyStatistics\Src\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MyAds\Plugins\MyStatistics\Src\TrackerService;

class TrackerController extends Controller
{
    protected TrackerService $tracker;

    public function __construct(TrackerService $tracker)
    {
        $this->tracker = $tracker;
    }

    /**
     * Handle the tracking hit from the client.
     */
    public function track(Request $request)
    {
        try {
            $this->tracker->track($request);
            
            // Return 1x1 transparent GIF if requested via image tag
            if ($request->query('image') === '1') {
                return response(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='))
                    ->header('Content-Type', 'image/gif')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            // Silently fail to not break the frontend
            return response()->json(['status' => 'error'], 500);
        }
    }
}
