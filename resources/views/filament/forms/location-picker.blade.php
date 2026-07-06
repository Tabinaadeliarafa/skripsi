<div
    wire:ignore
    class="rounded-2xl overflow-hidden border border-gray-200 bg-white"
    style="height: 360px;"
>
    <div id="laporan-location-map" style="height: 100%; width: 100%;"></div>
</div>

<p class="text-xs text-gray-500 mt-2">
    Klik salah satu titik pada peta Kabupaten Bekasi untuk mengisi latitude dan longitude secara otomatis.
</p>

@once
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            const mapElement = document.getElementById('laporan-location-map');

            if (!mapElement || mapElement.dataset.loaded === 'true') {
                return;
            }

            mapElement.dataset.loaded = 'true';

            const latInput = document.getElementById('latitude-input');
            const lngInput = document.getElementById('longitude-input');

            const defaultLat = latInput && latInput.value ? parseFloat(latInput.value) : -6.238270;
            const defaultLng = lngInput && lngInput.value ? parseFloat(lngInput.value) : 107.148452;

            const map = L.map('laporan-location-map', {
                scrollWheelZoom: true,
            }).setView([defaultLat, defaultLng], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            let marker = null;

            if (latInput && lngInput && latInput.value && lngInput.value) {
                marker = L.marker([defaultLat, defaultLng]).addTo(map);
            }

            fetch('/geo/kecamatan.geojson')
                .then(response => response.json())
                .then(data => {
                    const geoLayer = L.geoJSON(data, {
                        style: {
                            color: '#12395C',
                            weight: 2,
                            fillColor: '#D45B1F',
                            fillOpacity: 0.12,
                        }
                    }).addTo(map);

                    map.fitBounds(geoLayer.getBounds());
                });

            map.on('click', function (e) {
                const lat = Number(e.latlng.lat).toFixed(6);
                const lng = Number(e.latlng.lng).toFixed(6);

                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(map);
                }

                if (latInput) {
                    latInput.value = lat;
                    latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    latInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (lngInput) {
                    lngInput.value = lng;
                    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            setTimeout(function () {
                map.invalidateSize();
            }, 500);
        }, 500);
    });
</script>