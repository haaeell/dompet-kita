@extends('layouts.app')
@section('title', 'Lokasi Pasangan')

@section('content')
    @php
        $currentUserId = auth()->id();
        $locationPayload = $members->map(function ($member) use ($locations) {
            $location = $locations->get($member->id);

            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'avatar' => $member->avatar_display,
                'photo' => $member->profile_photo_url,
                'role' => $member->role,
                'is_me' => $member->id === auth()->id(),
                'location' => $location ? [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'accuracy' => $location->accuracy,
                    'label' => $location->label,
                    'address_text' => $location->address_text,
                    'road' => $location->road,
                    'neighbourhood' => $location->neighbourhood,
                    'suburb' => $location->suburb,
                    'village' => $location->village,
                    'district' => $location->district,
                    'city' => $location->city,
                    'state' => $location->state,
                    'postcode' => $location->postcode,
                    'is_active' => $location->is_active,
                    'last_seen_at' => optional($location->last_seen_at)->toIso8601String(),
                    'last_seen_human' => optional($location->last_seen_at)->diffForHumans(),
                ] : null,
            ];
        })->values();
    @endphp

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        .location-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.55fr);
            gap: 18px;
            align-items: stretch;
        }

        .location-map-card {
            position: relative;
            min-height: min(68dvh, 680px);
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ecfeff;
        }

        #coupleMap {
            height: 100%;
            min-height: min(68dvh, 680px);
            width: 100%;
            z-index: 1;
        }

        .location-map-fallback {
            position: absolute;
            inset: 0;
            display: none;
            place-items: center;
            padding: 24px;
            text-align: center;
            background:
                linear-gradient(90deg, rgba(14, 165, 233, .12) 1px, transparent 1px),
                linear-gradient(rgba(14, 165, 233, .12) 1px, transparent 1px),
                #f0fdfa;
            background-size: 42px 42px;
            color: #0f172a;
            z-index: 2;
        }

        .location-map-chip {
            position: absolute;
            left: 16px;
            top: 16px;
            z-index: 401;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .72);
            background: rgba(255, 255, 255, .9);
            padding: 9px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .12);
            backdrop-filter: blur(12px);
        }

        .location-floating-update {
            position: absolute;
            right: 16px;
            top: 16px;
            z-index: 402;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border-radius: 999px;
            border: 1px solid rgba(251, 207, 232, .95);
            background: rgba(255, 255, 255, .94);
            padding: 0 14px;
            color: #be185d;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .14);
            backdrop-filter: blur(12px);
            transition: .2s ease;
        }

        .location-floating-update:hover {
            background: #fdf2f8;
            transform: translateY(-1px);
        }

        .location-pin {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 50% 50% 50% 10%;
            transform: rotate(-45deg);
            color: #fff;
            border: 3px solid #fff;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .25);
        }

        .location-pin span {
            transform: rotate(45deg);
            font-size: 18px;
            line-height: 1;
        }

        .location-pin img {
            transform: rotate(45deg);
            width: 28px;
            height: 28px;
            border-radius: 999px;
            object-fit: cover;
        }

        .location-distance-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid rgba(251, 207, 232, .95);
            background: rgba(255, 255, 255, .95);
            padding: 7px 10px;
            color: #be185d;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
            box-shadow: 0 14px 36px rgba(190, 24, 93, .16);
            backdrop-filter: blur(10px);
        }

        .location-love-card {
            border-radius: 8px;
            border: 1px solid #fbcfe8;
            background: linear-gradient(135deg, #fff 0%, #fdf2f8 52%, #ecfeff 100%);
            padding: 14px;
            box-shadow: 0 16px 44px rgba(219, 39, 119, .08);
        }

        .location-address-box {
            margin-top: 12px;
            border-radius: 8px;
            border: 1px dashed #fbcfe8;
            background: #fff7fb;
            padding: 10px;
        }

        .location-side {
            display: flex;
            flex-direction: column;
            gap: 14px;
            min-width: 0;
        }

        .location-icon-button {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #64748b;
            background: #fff;
            transition: .2s ease;
        }

        .location-icon-button:hover {
            color: #db2777;
            border-color: #fbcfe8;
            background: #fdf2f8;
        }

        .location-member-card {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 14px;
        }

        .location-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: #fce7f3;
            color: #be185d;
            font-weight: 800;
            flex: 0 0 auto;
        }

        .location-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .location-status-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #94a3b8;
        }

        .location-status-dot.active {
            background: #22c55e;
            box-shadow: 0 0 0 5px rgba(34, 197, 94, .13);
        }

        .location-mini-stat {
            border-radius: 8px;
            background: #f8fafc;
            padding: 10px;
            min-width: 0;
        }

        @media (max-width: 1024px) {
            .location-shell {
                grid-template-columns: 1fr;
            }

            .location-map-card,
            #coupleMap {
                min-height: 54dvh;
            }
        }

        @media (max-width: 640px) {
            .location-map-card,
            #coupleMap {
                min-height: 420px;
            }

            .location-floating-update {
                left: 16px;
                right: 16px;
                top: auto;
                bottom: 16px;
            }
        }
    </style>

    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="page-title">Lokasi Pasangan</h1>
            <p class="page-subtitle">Lihat titik terakhir yang dibagikan berdua.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-ghost w-full sm:w-auto justify-center">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <div class="location-shell">
        <section class="location-map-card">
            <div id="coupleMap"></div>
            <div class="location-map-chip">
                <i class="fa-solid fa-location-dot text-pink-600"></i>
                <span id="mapStatusText">Peta pasangan siap</span>
            </div>
            <button id="shareLocationButton" type="button" class="location-floating-update" title="Update lokasi sekarang">
                <i class="fa-solid fa-location-crosshairs"></i>
                <span>Update lokasi</span>
            </button>
            <div id="locationMapFallback" class="location-map-fallback">
                <div>
                    <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-full bg-white text-pink-600 shadow-sm">
                        <i class="fa-solid fa-map-location-dot text-2xl"></i>
                    </div>
                    <div class="text-lg font-bold">Peta belum bisa dimuat</div>
                    <div class="mt-1 text-sm text-slate-500">Data lokasi tetap bisa dibagikan dan dilihat dari kartu di samping.</div>
                </div>
            </div>
        </section>

        <aside class="location-side">
            <div class="location-love-card">
                <div class="flex items-start gap-3">
                    <div class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-full bg-white text-pink-600 shadow-sm">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div class="min-w-0">
                        <div id="coupleDistanceTitle" class="text-sm font-extrabold text-slate-900">Menunggu dua titik aktif</div>
                        <div id="coupleDistanceMessage" class="mt-1 text-xs leading-relaxed text-slate-500">
                            Kalau dua lokasi sudah aktif, jarak kalian akan muncul di peta.
                        </div>
                    </div>
                </div>
            </div>

            <div id="locationHint" class="rounded-lg bg-slate-50 px-3 py-2 text-xs leading-relaxed text-slate-500">
                Lokasi hanya dibagikan setelah kamu menekan update lokasi.
            </div>

            <button id="stopLocationButton" type="button" class="w-full rounded-lg border border-rose-100 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700">
                <i class="fa-solid fa-location-xmark mr-1"></i> Stop berbagi lokasi saya
            </button>

            <div id="locationMembers" class="space-y-3">
                @foreach($members as $member)
                    @php
                        $location = $locations->get($member->id);
                    @endphp
                    <article class="location-member-card" data-member-card="{{ $member->id }}">
                        <div class="flex items-start gap-3">
                            <div class="location-avatar">
                                @if($member->profile_photo_url)
                                    <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}">
                                @else
                                    {{ $member->avatar_display }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="truncate text-sm font-bold text-slate-900">{{ $member->name }}</div>
                                    @if($member->id === $currentUserId)
                                        <span class="rounded-full bg-pink-50 px-2 py-0.5 text-[10px] font-bold text-pink-700">Saya</span>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                    <span class="location-status-dot {{ $location?->is_active ? 'active' : '' }}"></span>
                                    <span data-location-state>
                                        {{ $location?->is_active ? 'Aktif dibagikan' : 'Belum aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <div class="font-semibold text-slate-500">Terakhir</div>
                                <div class="mt-1 truncate font-bold text-slate-900" data-last-seen>
                                    {{ $location?->last_seen_at ? $location->last_seen_at->diffForHumans() : '-' }}
                                </div>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <div class="font-semibold text-slate-500">Jarak</div>
                                <div class="mt-1 truncate font-bold text-slate-900" data-distance>-</div>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-2">
                            <div class="min-w-0 truncate text-xs text-slate-500" data-location-label>
                                {{ $location?->label ?? 'Belum ada titik lokasi' }}
                            </div>
                            <button type="button" class="location-icon-button !h-9 !w-9" data-focus-member="{{ $member->id }}" title="Lihat di peta">
                                <i class="fa-solid fa-magnifying-glass-location"></i>
                            </button>
                        </div>

                        <div class="location-address-box">
                            <div class="flex items-center gap-2 text-xs font-bold text-pink-700">
                                <i class="fa-solid fa-map-pin"></i>
                                <span>Catatan tempat</span>
                            </div>
                            <div class="mt-1 text-xs leading-relaxed text-slate-600" data-address-text>
                                {{ $location?->address_text ?? 'Alamat detail akan tercatat setelah lokasi diperbarui.' }}
                            </div>
                            <div class="mt-2 text-[11px] leading-relaxed text-slate-400" data-address-parts>
                                @if($location?->city || $location?->district || $location?->state)
                                    {{ collect([$location->village, $location->district, $location->city, $location->state, $location->postcode])->filter()->join(' - ') }}
                                @else
                                    Menunggu detail wilayah.
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const locationMembers = @json($locationPayload);
        const currentLocationUserId = @json($currentUserId);
        const locationUpdateUrl = @json(route('locations.update'));
        const locationStopUrl = @json(route('locations.destroy'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const markerColors = ['#db2777', '#0891b2', '#16a34a', '#7c3aed'];
        const markers = new Map();
        let distanceLayers = null;
        let map = null;

        function activeLocations() {
            return locationMembers.filter(member => member.location && member.location.is_active);
        }

        function setHint(text, tone = 'slate') {
            const hint = document.getElementById('locationHint');
            if (!hint) return;

            hint.textContent = text;
            hint.className = 'mt-3 rounded-lg px-3 py-2 text-xs leading-relaxed ' + (
                tone === 'success'
                    ? 'bg-emerald-50 text-emerald-700'
                    : tone === 'error'
                        ? 'bg-rose-50 text-rose-700'
                        : 'bg-slate-50 text-slate-500'
            );
        }

        function avatarHtml(member) {
            if (member.photo) {
                return `<img src="${member.photo}" alt="${member.name}" class="h-7 w-7 rounded-full object-cover">`;
            }

            return `<span>${member.avatar || '♡'}</span>`;
        }

        function markerIcon(member, index) {
            return L.divIcon({
                className: '',
                html: `<div class="location-pin" style="background:${markerColors[index % markerColors.length]}">${avatarHtml(member)}</div>`,
                iconSize: [44, 44],
                iconAnchor: [22, 42],
                popupAnchor: [0, -38],
            });
        }

        function formatDistance(meters) {
            if (!Number.isFinite(meters)) return '-';
            if (meters < 1000) return `${Math.round(meters)} m`;
            return `${(meters / 1000).toFixed(meters < 10000 ? 1 : 0)} km`;
        }

        function addressParts(location) {
            if (!location) return 'Menunggu detail wilayah.';

            return [
                location.road,
                location.neighbourhood,
                location.suburb,
                location.village,
                location.district,
                location.city,
                location.state,
                location.postcode,
            ].filter(Boolean).join(' - ') || 'Menunggu detail wilayah.';
        }

        function distanceBetween(a, b) {
            const toRad = value => value * Math.PI / 180;
            const earthRadius = 6371000;
            const dLat = toRad(b.latitude - a.latitude);
            const dLon = toRad(b.longitude - a.longitude);
            const lat1 = toRad(a.latitude);
            const lat2 = toRad(b.latitude);
            const h = Math.sin(dLat / 2) ** 2 + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLon / 2) ** 2;

            return earthRadius * 2 * Math.atan2(Math.sqrt(h), Math.sqrt(1 - h));
        }

        function midpoint(a, b) {
            return [
                (Number(a.latitude) + Number(b.latitude)) / 2,
                (Number(a.longitude) + Number(b.longitude)) / 2,
            ];
        }

        function coupleDistancePair() {
            const active = activeLocations();
            const me = active.find(member => member.user_id === currentLocationUserId);
            const partner = active.find(member => member.user_id !== currentLocationUserId);

            if (!me || !partner) return null;

            const meters = distanceBetween(me.location, partner.location);
            return { me, partner, meters };
        }

        function sweetDistanceMessage(meters) {
            if (!Number.isFinite(meters)) {
                return {
                    title: 'Menunggu dua titik aktif',
                    message: 'Kalau dua lokasi sudah aktif, jarak kalian akan muncul di peta.',
                };
            }

            if (meters < 100) {
                return {
                    title: 'Sebelah-sebelahan banget',
                    message: 'Jaraknya cuma selempar senyum. Cocok buat bilang, "aku udah dekat".',
                };
            }

            if (meters < 1000) {
                return {
                    title: 'Masih satu pelukan kota',
                    message: `Kalian berjarak ${formatDistance(meters)}. Dekat, tinggal cari alasan buat ketemu.`,
                };
            }

            if (meters < 10000) {
                return {
                    title: 'Agak jauh, tapi masih manis',
                    message: `${formatDistance(meters)} itu bukan jauh, cuma rute kecil menuju quality time.`,
                };
            }

            return {
                title: 'Jauh di peta, dekat di hati',
                message: `Jaraknya ${formatDistance(meters)}. Titiknya boleh berjauhan, kabarnya tetap harus dekat.`,
            };
        }

        function renderSweetDistance() {
            const pair = coupleDistancePair();
            const copy = sweetDistanceMessage(pair?.meters);
            const title = document.getElementById('coupleDistanceTitle');
            const message = document.getElementById('coupleDistanceMessage');

            if (title) title.textContent = copy.title;
            if (message) message.textContent = copy.message;
        }

        function renderCards() {
            const me = locationMembers.find(member => member.user_id === currentLocationUserId);
            const myLocation = me?.location?.is_active ? me.location : null;

            locationMembers.forEach(member => {
                const card = document.querySelector(`[data-member-card="${member.user_id}"]`);
                if (!card) return;

                const isActive = member.location && member.location.is_active;
                const dot = card.querySelector('.location-status-dot');
                const state = card.querySelector('[data-location-state]');
                const lastSeen = card.querySelector('[data-last-seen]');
                const label = card.querySelector('[data-location-label]');
                const addressText = card.querySelector('[data-address-text]');
                const addressPartsText = card.querySelector('[data-address-parts]');
                const distance = card.querySelector('[data-distance]');

                dot?.classList.toggle('active', Boolean(isActive));
                if (state) state.textContent = isActive ? 'Aktif dibagikan' : 'Belum aktif';
                if (lastSeen) lastSeen.textContent = member.location?.last_seen_human || '-';
                if (label) label.textContent = member.location?.label || 'Belum ada titik lokasi';
                if (addressText) addressText.textContent = member.location?.address_text || 'Alamat detail akan tercatat setelah lokasi diperbarui.';
                if (addressPartsText) addressPartsText.textContent = addressParts(member.location);
                if (distance) {
                    distance.textContent = myLocation && member.location
                        ? (member.user_id === currentLocationUserId ? 'Kamu' : formatDistance(distanceBetween(myLocation, member.location)))
                        : '-';
                }
            });

            renderSweetDistance();
        }

        function renderMap() {
            if (!window.L) {
                document.getElementById('locationMapFallback').style.display = 'grid';
                document.getElementById('mapStatusText').textContent = 'Mode kartu aktif';
                return;
            }

            map = L.map('coupleMap', { zoomControl: false }).setView([-6.2, 106.816666], 11);
            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap',
            }).addTo(map);
            distanceLayers = L.layerGroup();
            distanceLayers.addTo(map);

            syncMapMarkers();
        }

        function syncDistanceLine() {
            if (!map || !distanceLayers) return;

            distanceLayers.clearLayers();

            const pair = coupleDistancePair();
            if (!pair) return;

            const from = [pair.me.location.latitude, pair.me.location.longitude];
            const to = [pair.partner.location.latitude, pair.partner.location.longitude];
            const mid = midpoint(pair.me.location, pair.partner.location);
            const label = `${formatDistance(pair.meters)} jaraknya`;

            L.polyline([from, to], {
                color: '#db2777',
                weight: 4,
                opacity: .78,
                dashArray: '10 12',
                lineCap: 'round',
            }).addTo(distanceLayers);

            L.marker(mid, {
                interactive: false,
                icon: L.divIcon({
                    className: '',
                    html: `<div class="location-distance-label"><i class="fa-solid fa-heart"></i>${label}</div>`,
                    iconSize: [120, 34],
                    iconAnchor: [60, 17],
                }),
            }).addTo(distanceLayers);
        }

        function syncMapMarkers() {
            if (!map) return;

            const bounds = [];
            activeLocations().forEach((member, index) => {
                const latLng = [member.location.latitude, member.location.longitude];
                bounds.push(latLng);

                const popup = `
                    <strong>${member.is_me ? 'Kamu' : member.name}</strong><br>
                    ${member.location.label || 'Lagi di sini'}<br>
                    ${member.location.address_text || 'Alamat detail belum tercatat'}<br>
                    <span style="color:#64748b">${member.location.last_seen_human || ''}</span>
                `;

                if (markers.has(member.user_id)) {
                    markers.get(member.user_id).setLatLng(latLng).setPopupContent(popup);
                } else {
                    const marker = L.marker(latLng, { icon: markerIcon(member, index) }).addTo(map).bindPopup(popup);
                    markers.set(member.user_id, marker);
                }
            });

            [...markers.keys()].forEach(userId => {
                if (!activeLocations().some(member => member.user_id === userId)) {
                    map.removeLayer(markers.get(userId));
                    markers.delete(userId);
                }
            });

            if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [48, 48], maxZoom: 16 });
            } else if (bounds.length === 1) {
                map.setView(bounds[0], 16);
            }

            syncDistanceLine();

            const pair = coupleDistancePair();
            document.getElementById('mapStatusText').textContent = pair
                ? `Jarak kalian ${formatDistance(pair.meters)}`
                : (bounds.length ? `${bounds.length} titik aktif` : 'Belum ada lokasi aktif');
        }

        function applyLocationResult(location) {
            const me = locationMembers.find(member => member.user_id === currentLocationUserId);
            me.location = {
                latitude: location.latitude,
                longitude: location.longitude,
                accuracy: location.accuracy,
                label: location.label,
                address_text: location.address_text,
                road: location.road,
                neighbourhood: location.neighbourhood,
                suburb: location.suburb,
                village: location.village,
                district: location.district,
                city: location.city,
                state: location.state,
                postcode: location.postcode,
                is_active: location.is_active,
                last_seen_at: location.last_seen_at,
                last_seen_human: 'baru saja',
            };

            renderCards();
            syncMapMarkers();
        }

        function deactivateMyLocation() {
            const me = locationMembers.find(member => member.user_id === currentLocationUserId);
            if (me) me.location = null;

            renderCards();
            syncMapMarkers();
        }

        async function saveLocation(position) {
            const payload = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                label: 'Lagi di sini',
            };

            const response = await fetch(locationUpdateUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) throw new Error('Gagal menyimpan lokasi.');

            const result = await response.json();
            window.DompetKitaLocationTracker?.enableSharing?.();
            applyLocationResult(result.location);
            setHint(`Lokasi tersimpan. Akurasi sekitar ${formatDistance(payload.accuracy)}.`, 'success');
            Toast.fire({ icon: 'success', title: result.message || 'Lokasi dibagikan.' });
        }

        function requestLocation() {
            if (window.DompetKitaLocationTracker) {
                window.DompetKitaLocationTracker.enableSharing();
                setHint('Sedang mengambil titik dan alamat detail terbaru...');
                window.DompetKitaLocationTracker.requestNow({
                    force: true,
                    label: 'Lagi di sini',
                });
                return;
            }

            if (!navigator.geolocation) {
                setHint('Browser ini belum mendukung akses lokasi.', 'error');
                return;
            }

            const button = document.getElementById('shareLocationButton');
            window.DompetKitaLocationTracker?.enableSharing?.();
            button.disabled = true;
            button.classList.add('opacity-70');
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Mengambil...</span>';
            setHint('Sedang mengambil titik lokasi terbaru...');

            navigator.geolocation.getCurrentPosition(
                position => {
                    saveLocation(position).catch(error => setHint(error.message, 'error')).finally(() => {
                        button.disabled = false;
                        button.classList.remove('opacity-70');
                        button.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i><span>Update lokasi</span>';
                    });
                },
                error => {
                    button.disabled = false;
                    button.classList.remove('opacity-70');
                    button.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i><span>Update lokasi</span>';
                    setHint(error.message || 'Izin lokasi belum diberikan.', 'error');
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 30000 }
            );
        }

        async function stopSharingLocation() {
            const button = document.getElementById('stopLocationButton');
            button.disabled = true;
            button.classList.add('opacity-70');

            try {
                const response = await fetch(locationStopUrl, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok) throw new Error('Gagal menghentikan berbagi lokasi.');

                const result = await response.json();
                window.DompetKitaLocationTracker?.stop?.();
                deactivateMyLocation();
                setHint(result.message || 'Berbagi lokasi dihentikan.', 'success');
                Toast.fire({ icon: 'success', title: result.message || 'Berbagi lokasi dihentikan.' });
            } catch (error) {
                setHint(error.message, 'error');
            } finally {
                button.disabled = false;
                button.classList.remove('opacity-70');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            renderCards();
            renderMap();

            document.getElementById('shareLocationButton').addEventListener('click', requestLocation);
            document.getElementById('stopLocationButton').addEventListener('click', stopSharingLocation);
            window.addEventListener('dompetkita:location-updated', function (event) {
                if (event.detail?.location) {
                    applyLocationResult(event.detail.location);
                    setHint('Lokasi otomatis diperbarui.', 'success');
                }
            });

            document.querySelectorAll('[data-focus-member]').forEach(button => {
                button.addEventListener('click', function () {
                    const marker = markers.get(Number(this.dataset.focusMember));
                    if (!marker || !map) return;
                    map.setView(marker.getLatLng(), 17);
                    marker.openPopup();
                });
            });
        });
    </script>
@endpush
