# MyStatistics Changelogs

All notable changes to this plugin will be documented in this file.

## [1.2.0] - 2026-06-23
### Added
- Added a new Settings page to control plugin performance and data footprint.
- Added Sampling Rate setting to control the percentage of requests tracked, reducing server load.
- Added Data Retention setting to automatically delete old analytics records, saving database space.
- Added background garbage collection mechanism to clean up old records silently.
- Added translations for the new settings in English and Arabic.

## [1.1.0] - 2026-05-10
### Added
- IP address tracking for each visit (optional/administrative view).
- Full pagination for the "Latest Visitors" section in the admin dashboard.
- Real-time display of visitor IP address next to country flags.

## [1.0.0] - 2026-05-09
### Added
- Initial release of the MyStatistics plugin.
- Cookie-less tracking implementation.
- Daily salted IP-hashing system to ensure GDPR and CCPA privacy compliance.
- Do Not Track (DNT) header support to respect user privacy.
- Async JavaScript tracking script injection to bypass ad-blockers via custom beacon endpoint.
- Detailed admin dashboard mirroring WP Statistics UI structure.
- Traffic trend line chart using Chart.js.
- Widgets for Top Pages, Top Browsers, OS, Devices, and Top Referring Domains.
- Country geolocation tracking using Cloudflare headers or PHP extensions.
- Multi-language support (English and Arabic).
