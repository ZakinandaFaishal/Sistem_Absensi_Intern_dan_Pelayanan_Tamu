@props(['name', 'class' => 'h-5 w-5', 'strokeWidth' => 1.8])

@php
    $baseAttrs = [
        'xmlns' => 'http://www.w3.org/2000/svg',
        'viewBox' => '0 0 24 24',
        'fill' => 'none',
        'stroke' => 'currentColor',
        'stroke-width' => (string) $strokeWidth,
        'stroke-linecap' => 'round',
        'stroke-linejoin' => 'round',
        'class' => $class,
        'aria-hidden' => 'true',
    ];
@endphp

@switch($name)

    {{-- BASIC --}}
    @case('x-mark')
        <svg {{ $attributes->merge($baseAttrs) }}><path d="M6 18 18 6M6 6l12 12" /></svg>
    @break

    @case('check')
        <svg {{ $attributes->merge($baseAttrs) }}><path d="M20 6 9 17l-5-5" /></svg>
    @break

    @case('check-circle')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M9 12.75 11.25 15 15 9.75" />
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
        </svg>
    @break

    @case('exclamation-triangle')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 9v4" />
            <path d="M12 17h.01" />
            <path d="M10.3 4.3 2.6 18a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 4.3a2 2 0 0 0-3.4 0z" />
        </svg>
    @break

    @case('info')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 16v-4" />
            <path d="M12 8h.01" />
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
        </svg>
    @break

    {{-- NAV --}}
    @case('bars-3')
        <svg {{ $attributes->merge($baseAttrs) }}><path d="M4 6h16M4 12h16M4 18h16" /></svg>
    @break

    @case('chevron-down')
        <svg {{ $attributes->merge($baseAttrs) }}><path d="m6 9 6 6 6-6" /></svg>
    @break

    @case('chevron-left')
        <svg {{ $attributes->merge($baseAttrs) }}><path d="m15 18-6-6 6-6" /></svg>
    @break

    @case('chevron-right')
        <svg {{ $attributes->merge($baseAttrs) }}><path d="m9 18 6-6-6-6" /></svg>
    @break

    {{-- TIME --}}
    @case('clock')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 8v5l3 2" />
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
        </svg>
    @break

    @case('calendar-days')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M8 7V3m8 4V3M4 11h16" />
            <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7z" />
        </svg>
    @break

    {{-- DASHBOARD / PAGES --}}
    @case('home')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M3 10.5 12 3l9 7.5" />
            <path d="M5 10v10h14V10" />
        </svg>
    @break

    @case('computer-desktop')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6z" />
            <path d="M8 21h8" />
            <path d="M12 17v4" />
        </svg>
    @break

    {{-- LOCATION / OFFICE --}}
    @case('map-pin')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11z" />
            <path d="M12 10.5a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
        </svg>
    @break

    @case('building-office')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 21V7a2 2 0 0 1 2-2h8v16" />
            <path d="M14 9h4a2 2 0 0 1 2 2v10" />
            <path d="M8 9h2" />
            <path d="M8 13h2" />
            <path d="M8 17h2" />
            <path d="M16 13h2" />
            <path d="M16 17h2" />
        </svg>
    @break

    {{-- GUESTBOOK / DOCUMENTS --}}
    @case('clipboard-document')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M8 4h8v3H8z" />
            <path d="M9 3h6a1 1 0 0 1 1 1v2H8V4a1 1 0 0 1 1-1z" />
            <path d="M7 7h10v14H7z" />
        </svg>
    @break

    {{-- NEW: BOOK ICON (buat “Buku Tamu”) --}}
    @case('book-open')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 6c-2-1.5-5-1.5-7 0v13c2-1.5 5-1.5 7 0" />
            <path d="M12 6c2-1.5 5-1.5 7 0v13c-2-1.5-5-1.5-7 0" />
            <path d="M12 6v16" />
        </svg>
    @break

    @case('document-text')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M7 3h7l3 3v15a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
            <path d="M14 3v4h4" />
            <path d="M8 12h8" />
            <path d="M8 16h8" />
        </svg>
    @break

    {{-- PEOPLE --}}
    @case('user')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" />
            <path d="M4 21a8 8 0 0 1 16 0" />
        </svg>
    @break

    @case('users')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0z" />
            <path d="M4 20a8 8 0 0 1 16 0" />
        </svg>
    @break

    {{-- CONTACT --}}
    @case('envelope')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 6h16v12H4z" />
            <path d="m4 7 8 6 8-6" />
        </svg>
    @break

    @case('phone')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M22 16.9v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.18 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.72c.12.9.32 1.78.58 2.63a2 2 0 0 1-.45 2.11L8.1 9.6a16 16 0 0 0 6.3 6.3l1.14-1.14a2 2 0 0 1 2.11-.45c.85.26 1.73.46 2.63.58A2 2 0 0 1 22 16.9z" />
        </svg>
    @break

    {{-- SECURITY --}}
    @case('lock-closed')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M7 11V8a5 5 0 0 1 10 0v3" />
            <path d="M6 11h12v10H6z" />
        </svg>
    @break

    @case('shield-check')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 3l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4z" />
            <path d="M9 12.5 11 14.5l4-5" />
        </svg>
    @break

    {{-- ACTIONS (CRUD) --}}
    @case('plus')
        <svg {{ $attributes->merge($baseAttrs) }}><path d="M12 5v14M5 12h14" /></svg>
    @break

    @case('pencil-square')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5z" />
        </svg>
    @break

    @case('trash')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 7h16" />
            <path d="M10 11v6" />
            <path d="M14 11v6" />
            <path d="M6 7l1 14h10l1-14" />
            <path d="M9 7V4h6v3" />
        </svg>
    @break

    @case('eye')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" />
            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
        </svg>
    @break

    {{-- SEARCH / FILTER / SORT --}}
    @case('magnifying-glass')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
            <path d="M21 21l-4.3-4.3" />
        </svg>
    @break

    @case('funnel')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M3 4h18l-7 8v6l-4 2v-8L3 4z" />
        </svg>
    @break

    @case('arrows-up-down')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M7 3v18" />
            <path d="M4 6l3-3 3 3" />
            <path d="M17 21V3" />
            <path d="M14 18l3 3 3-3" />
        </svg>
    @break

    {{-- SETTINGS / AUTH --}}
    @case('cog-6-tooth')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
            <path d="M19.4 15a7.9 7.9 0 0 0 .1-2l2-1.2-2-3.5-2.3.6a8 8 0 0 0-1.7-1l-.3-2.4H10.8l-.3 2.4a8 8 0 0 0-1.7 1L6.5 8.3l-2 3.5 2 1.2a7.9 7.9 0 0 0 .1 2l-2 1.2 2 3.5 2.3-.6c.5.4 1.1.7 1.7 1l.3 2.4h4.4l.3-2.4c.6-.3 1.2-.6 1.7-1l2.3.6 2-3.5-2-1.2z" />
        </svg>
    @break

    @case('arrow-right-on-rectangle') {{-- logout --}}
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M10 17l5-5-5-5" />
            <path d="M15 12H3" />
            <path d="M17 21h2a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-2" />
        </svg>
    @break

    {{-- QR / CAMERA --}}
    @case('qr-code')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 4h6v6H4z" />
            <path d="M14 4h6v6h-6z" />
            <path d="M4 14h6v6H4z" />
            <path d="M14 14h2v2h-2z" />
            <path d="M18 14h2v6h-6v-2h4z" />
            <path d="M14 18h2" />
        </svg>
    @break

    @case('camera')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M5 7h3l2-2h4l2 2h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
            <path d="M12 17a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
        </svg>
    @break

        @case('academic-cap') {{-- pendidikan --}}
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 3 2 8l10 5 10-5-10-5z" />
            <path d="M5 10v5c0 2 3 4 7 4s7-2 7-4v-5" />
            <path d="M22 8v6" />
        </svg>
    @break

    @case('briefcase') {{-- pekerjaan --}}
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M9 7V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1" />
            <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z" />
            <path d="M3 13h18" />
        </svg>
    @break

    @case('building-office') {{-- instansi / kantor (opsional, kalau mau seragam) --}}
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 21V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v14" />
            <path d="M9 9h1" />
            <path d="M9 12h1" />
            <path d="M9 15h1" />
            <path d="M12 9h1" />
            <path d="M12 12h1" />
            <path d="M12 15h1" />
            <path d="M8 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4" />
        </svg>
    @break

    @case('badge')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 2l3 2 3.5.5-.5 3.5L20 11l-2 2 .5 3.5-3.5-.5-3 2-3-2-3.5.5.5-3.5-2-2 2-3-.5-3.5L9 4l3-2z" />
            <path d="M9.5 12.5a3 3 0 1 0 5 0" />
        </svg>
    @break

    @case('star')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 17.3 6.8 20l1-5.8L3 9.5l5.9-.9L12 3l3.1 5.6 5.9.9-4.8 4.7 1 5.8z" />
        </svg>
    @break

    @default
        <svg {{ $attributes->merge($baseAttrs) }}><path d="M12 12h.01" /></svg>

@endswitch
