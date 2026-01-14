@extends('layouts.kiosk_mode')

@section('title', 'Mode Kiosk')

@section('content')
    <main class="relative min-h-screen w-full overflow-hidden">
        {{-- Background --}}
        <div class="absolute inset-0">
            <img src="{{ asset('img/background.png') }}" alt="Kabupaten Magelang"
                class="h-full w-full object-cover scale-[1.03]">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/70"></div>
            <div
                class="absolute inset-0 [background:radial-gradient(ellipse_at_center,rgba(0,0,0,0.08)_0%,rgba(0,0,0,0.55)_70%,rgba(0,0,0,0.8)_100%)]">
            </div>
        </div>

        {{-- Header (logo + clock only) --}}
        <header class="relative z-10 flex items-center justify-between px-6 py-5 sm:px-10">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-lg p-2">
                    <img src="{{ asset('img/logo kab.mgl.png') }}" alt="Logo Kabupaten Magelang"
                        class="h-12 w-12 sm:h-14 sm:w-14 object-contain">
                </div>
                <div class="hidden sm:block text-left">
                    <div class="text-white font-semibold">Diskominfo</div>
                    <div class="text-white/70 text-sm">Kabupaten Magelang</div>
                </div>
            </div>

            <div class="text-right">
                <div id="kioskClock" class="text-white font-semibold text-lg sm:text-2xl tabular-nums">--:--:--</div>
                <div id="kioskDate" class="text-white/70 text-xs sm:text-sm">—</div>
            </div>
        </header>

        {{-- Content --}}
        <section class="relative z-10 min-h-[calc(100vh-84px)] px-6 sm:px-10">
            <div class="mx-auto flex min-h-[calc(100vh-84px)] max-w-3xl flex-col items-center justify-center text-center">
                <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl text-white drop-shadow tracking-wide">
                    Selamat Datang
                </h1>

                <p class="mt-2 text-white/90 text-sm sm:text-base tracking-wide">
                    Silakan pilih layanan
                </p>

                <div class="mt-6 h-[2px] w-24 rounded-full bg-white/40"></div>

                @php
                    $btnBase = 'w-72 max-w-full inline-flex items-center justify-center gap-3 rounded-2xl bg-white/15 px-6 py-5 text-base font-semibold text-white
                                backdrop-blur-md border border-white/25 shadow-xl
                                hover:bg-white/25 hover:-translate-y-0.5 hover:shadow-2xl
                                focus:outline-none focus:ring-2 focus:ring-white/50
                                transition duration-200';
                @endphp

                <div class="mt-10 w-full flex flex-col items-center gap-6">
                    <a href="{{ route('kiosk.absensi') }}" class="{{ $btnBase }}">
                        <x-icon name="map-pin" class="h-7 w-7 shrink-0" />
                        <span class="leading-none">Absensi Magang</span>
                    </a>

                    <a href="{{ route('guest.create') }}" class="{{ $btnBase }}">
                        <x-icon name="clipboard-document" class="h-7 w-7 shrink-0" />
                        <span class="leading-none">Buku Tamu</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- FLOATING: TAMU SEDANG HADIR (kiri bawah) --}}
        <section class="anim fixed left-4 bottom-4 z-20 w-[360px] max-w-[calc(100vw-32px)]">
            <div class="rounded-3xl border border-white/20 bg-white/10 backdrop-blur-xl shadow-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-white/15 flex items-center justify-between">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="h-9 w-9 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center">
                            <x-icon name="clipboard-document" class="h-5 w-5 text-white" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-white truncate">Tamu Sedang Hadir</div>
                            <div class="text-[11px] text-white/70 truncate">
                                <span id="kioskActiveGuestCount" class="font-semibold text-white/90">0</span> aktif • update 5 detik
                            </div>
                        </div>
                    </div>

                    <button id="kioskBtnToggleGuest" type="button"
                        class="rounded-xl bg-white/10 border border-white/15 px-2.5 py-2 text-xs font-semibold text-white/90
                               hover:bg-white/15 transition">
                        <span id="kioskGuestChevron" class="inline-block transition">▾</span>
                    </button>
                </div>

                <div id="kioskGuestBody" class="p-3">
                    <div id="kioskActiveGuestLoading"
                        class="rounded-2xl border border-white/15 bg-white/5 px-3 py-3 text-sm text-white/80">
                        Memuat daftar tamu...
                    </div>

                    <ul id="kioskActiveGuestList" class="hidden space-y-2 max-h-72 overflow-auto pr-1"></ul>

                    <div id="kioskActiveGuestEmpty"
                        class="hidden rounded-2xl border border-white/15 bg-white/5 px-3 py-3 text-sm text-white/80">
                        Belum ada tamu yang sedang berkunjung.
                    </div>
                </div>
            </div>
        </section>

        {{-- MODAL: REMINDER SURVEY --}}
        <div id="surveyReminderModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="relative mx-auto mt-24 w-[92%] max-w-md">
                <div class="rounded-3xl border border-white/20 bg-white/10 backdrop-blur-xl shadow-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-white/15 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-white font-extrabold text-base">Ingatkan Isi Survey</div>
                            <div id="surveyReminderName" class="text-white/70 text-sm truncate">—</div>
                        </div>
                        <button id="btnCloseSurveyModal" type="button"
                            class="rounded-xl bg-white/10 border border-white/15 px-3 py-2 text-xs font-semibold text-white/90 hover:bg-white/15 transition">
                            Tutup
                        </button>
                    </div>

                    <div class="px-5 py-4 space-y-3">
                        <div class="rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-sm text-white/85">
                            Tamu ini <span class="font-semibold">belum mengisi survey</span>. Silakan ingatkan untuk mengisi survey sebelum selesai.
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <a id="btnGoSurvey" href="#"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-white/20 border border-white/25 px-4 py-3 text-sm font-semibold text-white
                                       hover:bg-white/25 transition">
                                Isi Survey
                            </a>

                            <button id="btnForceComplete" type="button"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-500/25 border border-emerald-400/30 px-4 py-3 text-sm font-semibold text-emerald-50
                                       hover:bg-emerald-500/30 transition">
                                Tetap Selesai
                            </button>
                        </div>

                        <div class="text-[11px] text-white/60">
                            Klik <span class="font-semibold text-white/80">Isi Survey</span> untuk membuka form survey, atau <span class="font-semibold text-white/80">Tetap Selesai</span> untuk menutup kunjungan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    {{-- Animations --}}
    <style>
        @media (prefers-reduced-motion: reduce) {
            .anim,
            .anim * { animation: none !important; transition: none !important; }
        }

        @keyframes popIn {
            from { opacity: 0; transform: translateY(8px) scale(.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .pop-in { animation: popIn .22s ease-out both; }

        @keyframes fadeOutDown {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(10px); }
        }
        .fade-out-down { animation: fadeOutDown .22s ease-in both; }
    </style>

    <script>
        (function() {
            const clockEl = document.getElementById('kioskClock');
            const dateEl = document.getElementById('kioskDate');

            function pad(n) {
                return String(n).padStart(2, '0');
            }

            function tick() {
                const now = new Date();
                const hh = pad(now.getHours());
                const mm = pad(now.getMinutes());
                const ss = pad(now.getSeconds());

                if (clockEl) clockEl.textContent = `${hh}:${mm}:${ss}`;
                if (dateEl) {
                    dateEl.textContent = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    });
                }
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>

    {{-- Polling Active Guests + tombol selesai + reminder survey --}}
    <script>
        (function () {
            const ENDPOINT = @json(route('guest.active'));

            // base endpoint untuk complete: /admin/tamu/{visit}/complete
            const COMPLETE_BASE = @json(url('/admin/tamu'));
            // base url survey: /tamu/{visit}/survey
            const SURVEY_BASE = @json(url('/tamu'));

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const listEl = document.getElementById('kioskActiveGuestList');
            const loadingEl = document.getElementById('kioskActiveGuestLoading');
            const emptyEl = document.getElementById('kioskActiveGuestEmpty');
            const countEl = document.getElementById('kioskActiveGuestCount');

            const btnToggle = document.getElementById('kioskBtnToggleGuest');
            const bodyEl = document.getElementById('kioskGuestBody');
            const chev = document.getElementById('kioskGuestChevron');

            // modal
            const modal = document.getElementById('surveyReminderModal');
            const btnCloseModal = document.getElementById('btnCloseSurveyModal');
            const btnGoSurvey = document.getElementById('btnGoSurvey');
            const btnForceComplete = document.getElementById('btnForceComplete');
            const modalName = document.getElementById('surveyReminderName');

            // state data terbaru by id
            const latestById = new Map();
            let pendingCompleteId = null;

            // collapse/expand
            btnToggle?.addEventListener('click', () => {
                if (!bodyEl) return;
                const isHidden = bodyEl.classList.contains('hidden');
                if (isHidden) {
                    bodyEl.classList.remove('hidden');
                    if (chev) chev.style.transform = 'rotate(0deg)';
                } else {
                    bodyEl.classList.add('hidden');
                    if (chev) chev.style.transform = 'rotate(-180deg)';
                }
            });

            function escapeHtml(str) {
                return String(str)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function badgeHtml(status) {
                return `
                    <span class="inline-flex items-center rounded-full bg-emerald-500/20 border border-emerald-400/30 px-2.5 py-1 text-[10px] font-semibold text-emerald-100">
                        ${escapeHtml(status)}
                    </span>
                `;
            }

            function selesaiBtnHtml(id) {
                return `
                    <button type="button"
                        data-action="complete"
                        data-id="${id}"
                        class="inline-flex items-center justify-center rounded-xl bg-white/10 border border-white/15 px-3 py-2 text-[11px] font-semibold text-white/90
                               hover:bg-white/15 transition active:scale-[0.98]">
                        Selesai
                    </button>
                `;
            }

            function itemHtml(item) {
                const arrived = item.arrived_at
                    ? `<div class="text-[11px] text-white/60 mt-0.5">Datang: <span class="font-semibold text-white/75">${escapeHtml(item.arrived_at)}</span></div>`
                    : '';

                return `
                    <li data-id="${item.id}" class="pop-in rounded-2xl border border-white/15 bg-white/5 px-3 py-2.5 hover:bg-white/10 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white truncate">${escapeHtml(item.name || 'Tamu')}</div>
                                ${arrived}
                            </div>
                            <div class="shrink-0 flex flex-col items-end gap-2">
                                ${badgeHtml(item.status || 'Sedang berkunjung')}
                                ${selesaiBtnHtml(item.id)}
                            </div>
                        </div>
                    </li>
                `;
            }

            function showState(state) {
                if (state === 'loading') {
                    loadingEl?.classList.remove('hidden');
                    emptyEl?.classList.add('hidden');
                    listEl?.classList.add('hidden');
                    return;
                }
                if (state === 'empty') {
                    loadingEl?.classList.add('hidden');
                    emptyEl?.classList.remove('hidden');
                    listEl?.classList.add('hidden');
                    return;
                }
                loadingEl?.classList.add('hidden');
                emptyEl?.classList.add('hidden');
                listEl?.classList.remove('hidden');
            }

            function closeModal() {
                modal?.classList.add('hidden');
                pendingCompleteId = null;
            }

            function openModalFor(item) {
                pendingCompleteId = String(item.id);

                if (modalName) {
                    modalName.textContent = `Tamu: ${item.name || 'Tamu'}`;
                }

                // survey url:
                // - jika backend mengirim survey_url => pakai itu
                // - fallback => /tamu/{id}/survey
                const surveyUrl = item.survey_url || `${SURVEY_BASE}/${encodeURIComponent(item.id)}/survey`;
                if (btnGoSurvey) btnGoSurvey.setAttribute('href', surveyUrl);

                modal?.classList.remove('hidden');
            }

            btnCloseModal?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            btnForceComplete?.addEventListener('click', async () => {
                if (!pendingCompleteId) return;
                await completeVisit(pendingCompleteId);
                closeModal();
            });

            function needsSurveyReminder(item) {
                // Ideal: backend kirim boolean:
                // item.survey_filled = true/false atau item.has_survey = true/false atau item.survey_completed = true/false
                // Reminder hanya kalau data jelas "belum isi".
                if (typeof item.survey_filled === 'boolean') return item.survey_filled === false;
                if (typeof item.has_survey === 'boolean') return item.has_survey === false;
                if (typeof item.survey_completed === 'boolean') return item.survey_completed === false;

                // fallback aman: kalau tidak ada fieldnya, jangan pop-up (biar tidak mengganggu)
                return false;
            }

            async function completeVisit(id) {
                const url = `${COMPLETE_BASE}/${encodeURIComponent(id)}/complete`;

                // set loading on button
                const btn = listEl?.querySelector(`button[data-action="complete"][data-id="${CSS.escape(String(id))}"]`);
                const original = btn ? btn.textContent : '';
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = '...';
                    btn.classList.add('opacity-70');
                }

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    if (!res.ok) {
                        // fallback: kalau controller return redirect/html, tetap anggap gagal
                        throw new Error('Request failed');
                    }

                    // animasi hilang dari list
                    const li = listEl?.querySelector(`li[data-id="${CSS.escape(String(id))}"]`);
                    if (li) {
                        li.classList.remove('pop-in');
                        li.classList.add('fade-out-down');
                        setTimeout(() => li.remove(), 220);
                    }

                    // refresh list biar sinkron
                    setTimeout(fetchActive, 250);

                } catch (e) {
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = original || 'Selesai';
                        btn.classList.remove('opacity-70');
                    }
                    alert('Gagal menandai selesai. Pastikan akun memiliki akses admin dan koneksi stabil.');
                }
            }

            // Delegation click for "Selesai"
            listEl?.addEventListener('click', async (e) => {
                const target = e.target?.closest?.('button[data-action="complete"]');
                if (!target) return;

                const id = target.getAttribute('data-id');
                if (!id) return;

                const item = latestById.get(String(id));

                // jika belum isi survey -> popup reminder
                if (item && needsSurveyReminder(item)) {
                    openModalFor(item);
                    return;
                }

                // langsung selesai
                await completeVisit(id);
            });

            async function fetchActive() {
                try {
                    const res = await fetch(ENDPOINT, { headers: { 'Accept': 'application/json' } });
                    const json = await res.json();
                    const data = Array.isArray(json.data) ? json.data : [];

                    // simpan state
                    latestById.clear();
                    data.forEach(d => latestById.set(String(d.id), d));

                    if (countEl) countEl.textContent = String(data.length);

                    if (!data.length) {
                        Array.from(listEl?.querySelectorAll('li') || []).forEach(li => li.remove());
                        showState('empty');
                        return;
                    }

                    showState('list');

                    const nextIds = new Set(data.map(d => String(d.id)));

                    // remove items that are no longer active
                    Array.from(listEl?.querySelectorAll('li') || []).forEach(li => {
                        const id = li.getAttribute('data-id');
                        if (id && !nextIds.has(String(id))) {
                            li.classList.remove('pop-in');
                            li.classList.add('fade-out-down');
                            setTimeout(() => li.remove(), 220);
                        }
                    });

                    // add/update
                    data.forEach(item => {
                        const id = String(item.id);
                        const existing = listEl?.querySelector(`li[data-id="${CSS.escape(id)}"]`);
                        if (!existing) {
                            listEl?.insertAdjacentHTML('beforeend', itemHtml(item));
                        } else {
                            existing.innerHTML = (new DOMParser())
                                .parseFromString(itemHtml(item), 'text/html')
                                .body.firstElementChild.innerHTML;
                        }
                    });

                } catch (e) {
                    if (loadingEl && !loadingEl.classList.contains('hidden')) {
                        loadingEl.textContent = 'Gagal memuat daftar tamu. Coba refresh halaman.';
                    }
                }
            }

            showState('loading');
            fetchActive();
            setInterval(fetchActive, 5000);
        })();
    </script>
@endsection
