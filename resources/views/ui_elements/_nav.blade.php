@php
    $uiPages = [
        ['label' => 'Buttons', 'icon' => 'ph-cursor-click', 'href' => url('ui'), 'active' => request()->is('ui')],
        ['label' => 'Feedback', 'icon' => 'ph-chat-centered-text', 'href' => url('ui/feedback'), 'active' => request()->is('ui/feedback')],
        ['label' => 'Controls', 'icon' => 'ph-sliders-horizontal', 'href' => url('ui/controls'), 'active' => request()->is('ui/controls')],
    ];
@endphp

<nav class="nav nav-pills ui-page-nav mb-7" aria-label="UI element pages">
    @foreach ($uiPages as $uiPage)
        <a
            @class(['nav-link', 'active' => $uiPage['active']])
            href="{{ $uiPage['href'] }}"
            @if ($uiPage['active']) aria-current="page" @endif>
            <i class="ph {{ $uiPage['icon'] }}" aria-hidden="true"></i>
            {{ $uiPage['label'] }}
        </a>
    @endforeach
</nav>
