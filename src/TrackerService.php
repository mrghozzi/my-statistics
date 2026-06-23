<?php

namespace MyAds\Plugins\MyStatistics\Src;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TrackerService
{
    /**
     * Get or create today's salt.
     */
    protected function getDailySalt(): string
    {
        $date = date('Y-m-d');
        $key = 'my_statistics_salt_' . $date;

        return Cache::remember($key, now()->endOfDay(), function () {
            return Str::random(32);
        });
    }

    /**
     * Generate visitor hash based on IP, User-Agent, and daily salt.
     */
    protected function generateVisitorHash(Request $request): string
    {
        $ip = $request->ip();
        // Fallback for local testing or headers
        if (empty($ip)) {
            $ip = $request->header('X-Forwarded-For', $request->header('X-Real-IP', '127.0.0.1'));
        }

        $userAgent = $request->userAgent() ?? 'unknown';
        $salt = $this->getDailySalt();

        return hash('sha256', $ip . $userAgent . $salt);
    }

    /**
     * Determine Country Code.
     * Uses Cloudflare header if available, otherwise 'XX'.
     */
    protected function getCountry(Request $request): string
    {
        // Cloudflare IP Geolocation header
        if ($request->hasHeader('CF-IPCountry')) {
            return strtoupper($request->header('CF-IPCountry'));
        }
        
        // PHP geoip extension if available
        if (function_exists('geoip_country_code_by_name')) {
            $ip = $request->ip();
            $country = @geoip_country_code_by_name($ip);
            if ($country) {
                return $country;
            }
        }

        return 'XX'; // Unknown
    }

    /**
     * Detect search engine from referrer.
     */
    protected function detectSearchEngine(?string $referrer): ?string
    {
        if (empty($referrer)) return null;

        $host = parse_url($referrer, PHP_URL_HOST);
        if (!$host) return null;

        $engines = [
            'google' => 'Google',
            'bing.com' => 'Bing',
            'yahoo.com' => 'Yahoo',
            'duckduckgo.com' => 'DuckDuckGo',
            'yandex' => 'Yandex',
            'baidu.com' => 'Baidu',
        ];

        foreach ($engines as $key => $name) {
            if (stripos($host, $key) !== false) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Track a page hit.
     */
    public function track(Request $request): void
    {
        // Check Do Not Track
        if ($request->header('DNT') === '1') {
            return;
        }

        // Apply Sampling Rate (reduce server load)
        $samplingRate = (int) (\App\Models\Option::where('name', 'my_statistics_sampling_rate')->value('o_valuer') ?? 100);
        if ($samplingRate < 100 && rand(1, 100) > $samplingRate) {
            return;
        }

        // Apply Data Retention (save database space) with a 5% chance to run garbage collection
        $retentionDays = (int) (\App\Models\Option::where('name', 'my_statistics_retention_days')->value('o_valuer') ?? 0);
        if ($retentionDays > 0 && rand(1, 100) <= 5) {
            DB::table('my_statistics_hits')
                ->where('created_at', '<', now()->subDays($retentionDays))
                ->delete();
        }

        $visitorHash = $this->generateVisitorHash($request);
        $url = $request->input('url', $request->header('referer'));
        $title = $request->input('title');
        $referrer = $request->input('referrer');

        // Parse User Agent using standard basic parser (or Jenssegers/Agent if available in Laravel)
        // Let's implement a basic lightweight regex parser to avoid external dependencies if not installed
        $userAgent = $request->userAgent();
        
        $browser = 'Unknown';
        if (preg_match('/(MSIE|Trident|(?!Gecko.+)Firefox|(?!AppleWebKit.+Chrome.+)Safari(?!.+Edge)|(?!AppleWebKit.+)Chrome(?!.+Edge)|(?!AppleWebKit.+Chrome.+Safari.+)Edge|AppleWebKit(?!.+Chrome|.+Safari)|Gecko(?!.+Firefox))(?: |\/)([\d\.apre]+)/', $userAgent, $matches)) {
            $browser = $matches[1];
        } elseif (preg_match('/OPR\/([\d\.]+)/', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Edg\/([\d\.]+)/', $userAgent)) {
            $browser = 'Edge';
        } elseif (stripos($userAgent, 'chrome') !== false) {
            $browser = 'Chrome';
        } elseif (stripos($userAgent, 'safari') !== false) {
            $browser = 'Safari';
        } elseif (stripos($userAgent, 'firefox') !== false) {
            $browser = 'Firefox';
        }

        $os = 'Unknown';
        if (preg_match('/windows nt 10/i', $userAgent)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/windows nt 6\.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6\.2/i', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/windows nt 6\.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'Mac OS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        }

        $device = 'Desktop';
        if (preg_match('/mobile/i', $userAgent)) {
            $device = 'Smartphone';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            $device = 'Tablet';
        }

        // Parse UTM parameters from URL
        $utmSource = null;
        $utmMedium = null;
        $utmCampaign = null;
        
        if ($url) {
            $query = parse_url($url, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $params);
                $utmSource = $params['utm_source'] ?? null;
                $utmMedium = $params['utm_medium'] ?? null;
                $utmCampaign = $params['utm_campaign'] ?? null;
            }
        }

        // Save to Database
        DB::table('my_statistics_hits')->insert([
            'visitor_hash' => $visitorHash,
            'ip' => $request->ip(),
            'url' => $url ? substr($url, 0, 2048) : null,
            'title' => $title ? substr($title, 0, 255) : null,
            'referrer' => $referrer ? substr($referrer, 0, 2048) : null,
            'search_engine' => $this->detectSearchEngine($referrer),
            'browser' => $browser,
            'os' => $os,
            'device' => $device,
            'country' => $this->getCountry($request),
            'utm_source' => $utmSource ? substr($utmSource, 0, 255) : null,
            'utm_medium' => $utmMedium ? substr($utmMedium, 0, 255) : null,
            'utm_campaign' => $utmCampaign ? substr($utmCampaign, 0, 255) : null,
            'created_at' => now(),
        ]);
    }
}
