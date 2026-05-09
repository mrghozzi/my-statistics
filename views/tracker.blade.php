<script>
    (function() {
        // Do not track respect
        if (navigator.doNotTrack === "1" || window.doNotTrack === "1" || navigator.msDoNotTrack === "1") {
            return;
        }

        function sendHit() {
            var url = window.location.href;
            var referrer = document.referrer;
            var title = document.title;
            
            var payload = {
                url: url,
                referrer: referrer,
                title: title
            };

            var endpoint = "{{ route('my_statistics.track') }}";

            // Use Beacon API if available, fallback to fetch/XHR
            if (navigator.sendBeacon) {
                var formData = new FormData();
                for (var key in payload) {
                    formData.append(key, payload[key]);
                }
                formData.append('_token', '{{ csrf_token() }}');
                navigator.sendBeacon(endpoint, formData);
            } else {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', endpoint, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                xhr.send(JSON.stringify(payload));
            }
        }

        // Wait for page load
        if (document.readyState === 'complete') {
            sendHit();
        } else {
            window.addEventListener('load', sendHit);
        }
    })();
</script>
