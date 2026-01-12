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
    @case('x-mark')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M6 18 18 6M6 6l12 12" />
        </svg>
    @break

    @case('bars-3')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    @break

    @case('calendar-days')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M8 7V3m8 4V3M4 11h16" />
            <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7z" />
        </svg>
    @break

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

    @case('map-pin')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11z" />
            <path d="M12 10.5a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
        </svg>
    @break

    @case('clipboard-document')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M8 4h8v3H8z" />
            <path d="M9 3h6a1 1 0 0 1 1 1v2H8V4a1 1 0 0 1 1-1z" />
            <path d="M7 7h10v14H7z" />
        </svg>
    @break

    @case('users')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0z" />
            <path d="M4 20a8 8 0 0 1 16 0" />
        </svg>
    @break

    @case('star')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 17.3 6.8 20l1-5.8L3 9.5l5.9-.9L12 3l3.1 5.6 5.9.9-4.8 4.7 1 5.8z" />
        </svg>
    @break

    @case('camera')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M5 7h3l2-2h4l2 2h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
            <path d="M12 17a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
        </svg>
    @break

    @case('user')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" />
            <path d="M4 21a8 8 0 0 1 16 0" />
        </svg>
    @break

    @case('lock-closed')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M7 11V8a5 5 0 0 1 10 0v3" />
            <path d="M6 11h12v10H6z" />
        </svg>
    @break

    @case('envelope')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 6h16v12H4z" />
            <path d="m4 7 8 6 8-6" />
        </svg>
    @break

    @case('phone')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path
                d="M22 16.9v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.18 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.72c.12.9.32 1.78.58 2.63a2 2 0 0 1-.45 2.11L8.1 9.6a16 16 0 0 0 6.3 6.3l1.14-1.14a2 2 0 0 1 2.11-.45c.85.26 1.73.46 2.63.58A2 2 0 0 1 22 16.9z" />
        </svg>
    @break

    @case('identification')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7z" />
            <path d="M9 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
            <path d="M7.5 16a3.5 3.5 0 0 1 3-2h.5a3.5 3.5 0 0 1 3 2" />
            <path d="M14 9h5" />
            <path d="M14 12h5" />
            <path d="M14 15h4" />
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

    @case('check-circle')
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M9 12.75 11.25 15 15 9.75" />
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
        </svg>
    @break

    @default
        <svg {{ $attributes->merge($baseAttrs) }}>
            <path d="M12 12h.01" />
        </svg>
@endswitch
