# MyStatistics Plugin for MYADS

**MyStatistics** is a comprehensive, privacy-first analytics plugin for the MYADS platform. It provides detailed insights into your community traffic without relying on cookies or storing any Personally Identifiable Information (PII), ensuring full compliance with GDPR, CCPA, and PECR.

## 🚀 Key Features

- **Cookie-less Tracking**: 100% data ownership with zero reliance on cookies.
- **Privacy First**: IP addresses are never stored in raw text; instead, they are hashed using a dynamic, rotating daily salt to anonymize visitors while still tracking unique hits.
- **Do Not Track (DNT)**: Fully respects user browser privacy settings by ignoring hits from users with DNT headers enabled.
- **Ad-blocker Bypass**: Utilizes an internal, custom beacon endpoint to bypass standard ad-blocker filters.
- **Detailed Analytics Dashboard**: Beautiful "Duralux" themed dashboard offering metrics on:
    - Real-time online visitors.
    - Top viewed pages and content.
    - Search engine referrers and incoming domains.
    - Device, operating system, and browser usage statistics.
    - Global geographic tracking (Top Countries).
- **Performance Settings**:
    - **Sampling Rate**: Track a percentage of visitors to reduce server load on high-traffic sites.
    - **Data Retention**: Automatically clear old statistics after a specified number of days to save database space.

## 🛠️ Installation

1. Upload the `my-statistics` folder to your `/plugins` directory.
2. Go to **Admin Panel > Plugins** and activate "MyStatistics".
3. The plugin will automatically run the necessary database migrations to create the `my_statistics_hits` table.
4. Navigate to **Admin Panel > Plugins > MyStatistics** (Pie Chart icon in sidebar) to view your dashboard.
5. Tracking starts automatically! The tracking script is silently injected into the footer of your public MYADS frontend.

## ⚙️ Technical Details

- **Database Table**: `my_statistics_hits` (Auto-created during activation).
- **Trigger**: Asynchronous JS tracking beacon injected via `theme_master_before_body_close`.
- **Backend Hashing**: IP + Daily Salt + Date = Anonymized Visitor Hash.
- **Dependencies**: Uses `Chart.js` via CDN for rendering the admin traffic trend charts.

---
Created by [MrGhozzi](https://github.com/mrghozzi)
