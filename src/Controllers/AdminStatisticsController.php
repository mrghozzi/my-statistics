<?php

namespace MyAds\Plugins\MyStatistics\Src\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatisticsController extends Controller
{
    public function index()
    {
        // General Stats
        $todayHits = DB::table('my_statistics_hits')->whereDate('created_at', Carbon::today())->count();
        $todayVisitors = DB::table('my_statistics_hits')->whereDate('created_at', Carbon::today())->distinct('visitor_hash')->count('visitor_hash');
        
        $yesterdayHits = DB::table('my_statistics_hits')->whereDate('created_at', Carbon::yesterday())->count();
        $yesterdayVisitors = DB::table('my_statistics_hits')->whereDate('created_at', Carbon::yesterday())->distinct('visitor_hash')->count('visitor_hash');
        
        $last7DaysHits = DB::table('my_statistics_hits')->where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $last7DaysVisitors = DB::table('my_statistics_hits')->where('created_at', '>=', Carbon::now()->subDays(7))->distinct('visitor_hash')->count('visitor_hash');

        $last28DaysHits = DB::table('my_statistics_hits')->where('created_at', '>=', Carbon::now()->subDays(28))->count();
        $last28DaysVisitors = DB::table('my_statistics_hits')->where('created_at', '>=', Carbon::now()->subDays(28))->distinct('visitor_hash')->count('visitor_hash');

        $totalHits = DB::table('my_statistics_hits')->count();
        $totalVisitors = DB::table('my_statistics_hits')->distinct('visitor_hash')->count('visitor_hash');

        // Traffic Trend (Last 30 Days)
        $trendData = DB::table('my_statistics_hits')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as views'), DB::raw('count(distinct visitor_hash) as visitors'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartDates = [];
        $chartViews = [];
        $chartVisitors = [];
        foreach ($trendData as $row) {
            $chartDates[] = $row->date;
            $chartViews[] = $row->views;
            $chartVisitors[] = $row->visitors;
        }

        // Top Pages
        $topPages = DB::table('my_statistics_hits')
            ->select('url', 'title', DB::raw('count(*) as views'))
            ->whereNotNull('url')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('url', 'title')
            ->orderBy('views', 'DESC')
            ->limit(10)
            ->get();

        // Top Browsers
        $topBrowsers = DB::table('my_statistics_hits')
            ->select('browser', DB::raw('count(*) as views'))
            ->whereNotNull('browser')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('browser')
            ->orderBy('views', 'DESC')
            ->limit(5)
            ->get();

        // Top OS
        $topOS = DB::table('my_statistics_hits')
            ->select('os', DB::raw('count(*) as views'))
            ->whereNotNull('os')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('os')
            ->orderBy('views', 'DESC')
            ->limit(5)
            ->get();

        // Device Usage Breakdown
        $deviceUsage = DB::table('my_statistics_hits')
            ->select('device', DB::raw('count(*) as views'))
            ->whereNotNull('device')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('device')
            ->orderBy('views', 'DESC')
            ->get();

        // Top Countries
        $topCountries = DB::table('my_statistics_hits')
            ->select('country', DB::raw('count(*) as views'))
            ->whereNotNull('country')
            ->where('country', '!=', 'XX')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('country')
            ->orderBy('views', 'DESC')
            ->limit(6)
            ->get();

        // Referrals from Search Engines
        $searchEngines = DB::table('my_statistics_hits')
            ->select('search_engine', DB::raw('count(*) as views'))
            ->whereNotNull('search_engine')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('search_engine')
            ->orderBy('views', 'DESC')
            ->limit(5)
            ->get();

        // Latest Visitors
        $latestVisitors = DB::table('my_statistics_hits')
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        // Top Referring Domains
        $topReferrers = DB::table('my_statistics_hits')
            ->select(DB::raw('SUBSTRING_INDEX(REPLACE(REPLACE(referrer, "https://", ""), "http://", ""), "/", 1) as domain'), DB::raw('count(*) as views'))
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('domain')
            ->orderBy('views', 'DESC')
            ->limit(5)
            ->get();

        return view('my_statistics::admin.dashboard', compact(
            'todayHits', 'todayVisitors',
            'yesterdayHits', 'yesterdayVisitors',
            'last7DaysHits', 'last7DaysVisitors',
            'last28DaysHits', 'last28DaysVisitors',
            'totalHits', 'totalVisitors',
            'chartDates', 'chartViews', 'chartVisitors',
            'topPages', 'topBrowsers', 'topOS', 'deviceUsage', 'topCountries',
            'searchEngines', 'latestVisitors', 'topReferrers'
        ));
    }

    public function settings()
    {
        $samplingRate = \App\Models\Option::where('name', 'my_statistics_sampling_rate')->value('o_valuer') ?? 100;
        $retentionDays = \App\Models\Option::where('name', 'my_statistics_retention_days')->value('o_valuer') ?? 0;

        return view('my_statistics::admin.settings', compact('samplingRate', 'retentionDays'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'sampling_rate' => 'required|integer|min:1|max:100',
            'retention_days' => 'required|integer|min:0',
        ]);

        \App\Models\Option::updateOrCreate(
            ['name' => 'my_statistics_sampling_rate'],
            ['o_valuer' => $request->sampling_rate]
        );

        \App\Models\Option::updateOrCreate(
            ['name' => 'my_statistics_retention_days'],
            ['o_valuer' => $request->retention_days]
        );

        return redirect()->route('admin.my_statistics.settings')->with('success', __('my_statistics::messages.settings_updated'));
    }
}
