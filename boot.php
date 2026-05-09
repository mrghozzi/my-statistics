<?php

declare(strict_types=1);

use App\Helpers\Hooks;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Lang;

// Require plugin classes manually since there is no plugin autoloader
require_once __DIR__ . '/src/TrackerService.php';
require_once __DIR__ . '/src/Controllers/TrackerController.php';
require_once __DIR__ . '/src/Controllers/AdminStatisticsController.php';

// Register Views & Language
View::addNamespace('my_statistics', __DIR__ . '/views');
Lang::addNamespace('my_statistics', __DIR__ . '/lang');

// Admin Sidebar Hook
Hooks::add_action('admin_sidebar_menu', function (): void {
    $url = route('admin.my_statistics.index');
    $isActive = request()->routeIs('admin.my_statistics.*');
    $linkClass = $isActive ? 'nxl-link active' : 'nxl-link';

    echo '<li class="nxl-item">'
        . '<a href="' . e($url) . '" class="' . e($linkClass) . '">'
        . '<span class="nxl-micon"><i class="feather-pie-chart"></i></span>'
        . '<span class="nxl-mtext">' . __('my_statistics::messages.dashboard_title') . '</span>'
        . '</a>'
        . '</li>';
});

// Frontend Tracker Hook
Hooks::add_action('theme_master_before_body_close', function (): void {
    // Inject the tracker script before the body closes on frontend pages
    echo View::make('my_statistics::tracker')->render();
});
