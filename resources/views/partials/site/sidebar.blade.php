@php
    $referenceItems = [
        [
            'label' => 'Overview',
            'icon' => 'ph-compass',
            'href' => route('reference.index'),
            'active' => request()->routeIs('reference.index'),
        ],
    ];

    foreach (config('reference.groups') as $groupCode => $group) {
        $referenceItems[] = [
            'label' => $group['label'],
            'icon' => $group['icon'],
            'href' => route('reference.'.$groupCode.'.'.array_key_first($group['pages'])),
            'active' => request()->routeIs("reference.{$groupCode}.*"),
        ];
    }

    $showcaseItems = [
        [
            'label' => 'Posts',
            'icon' => 'ph-article',
            'href' => route('posts.index'),
            'active' => request()->routeIs('posts.index', 'posts.edit'),
            'children' => [
                [
                    'label' => 'Categories',
                    'icon' => 'ph-folders',
                    'href' => route('categories.index'),
                    'active' => request()->routeIs('categories.*'),
                ],
                [
                    'label' => 'New Post',
                    'icon' => 'ph-note-pencil',
                    'href' => route('posts.create'),
                    'active' => request()->routeIs('posts.create'),
                ],
            ],
        ],
        [
            'label' => 'Authors',
            'icon' => 'ph-pen-nib',
            'href' => route('authors.index'),
            'active' => request()->routeIs('authors.index', 'authors.edit'),
            'children' => [
                [
                    'label' => 'New Author',
                    'icon' => 'ph-user-plus',
                    'href' => route('authors.create'),
                    'active' => request()->routeIs('authors.create'),
                ],
            ],
        ],
        [
            'label' => 'Groups',
            'icon' => 'ph-users-three',
            'href' => route('groups.index'),
            'active' => request()->routeIs('groups.*'),
        ],
    ];

    $navSections = [
        'Reference' => $referenceItems,
        'Showcase' => $showcaseItems,
    ];
@endphp

<aside class="sidebar" id="amber-sidebar">
    <div class="sidebar-brand-row">
        @include('partials.site.brand')
        <button
            class="icon-button sidebar-close"
            type="button"
            aria-label="Close navigation"
            data-amber-menu-close>
            <i class="ph ph-x" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Main navigation">
        @foreach ($navSections as $sectionLabel => $navItems)
            <p class="sidebar-nav-heading">
                {{ $sectionLabel }}
            </p>

            <ul class="sidebar-nav-list">
                @foreach ($navItems as $item)
                    @php
                        $children = $item['children'] ?? [];
                        $childIsActive = collect($children)->contains('active', true);
                        $isActive = $item['active'] || $childIsActive;
                    @endphp

                    <li
                        @class(['sidebar-nav-item', 'active' => $isActive, 'show' => $children])
                        @if ($children) data-amber-nav-group @endif>
                        @if ($children)
                            <div class="sidebar-nav-link sidebar-nav-group">
                                <a
                                    class="sidebar-nav-group-link"
                                    href="{{ $item['href'] }}"
                                    @if ($item['active']) aria-current="page" @endif>
                                    <i class="ph {{ $item['icon'] }} sidebar-nav-icon" aria-hidden="true"></i>
                                    <span class="sidebar-nav-label">
                                        {{ $item['label'] }}
                                    </span>
                                </a>
                                <button
                                    class="sidebar-nav-group-toggle"
                                    type="button"
                                    aria-label="Toggle {{ $item['label'] }} navigation"
                                    aria-expanded="true"
                                    data-amber-nav-group-toggle>
                                    <i class="ph ph-caret-up sidebar-nav-chevron" aria-hidden="true"></i>
                                </button>
                            </div>

                            <ul class="sidebar-nav-children">
                                @foreach ($children as $child)
                                    <li @class(['sidebar-nav-child', 'active' => $child['active']])>
                                        <a
                                            class="sidebar-nav-link sidebar-nav-link-child"
                                            href="{{ $child['href'] }}"
                                            @if ($child['active']) aria-current="page" @endif>
                                            <i class="ph {{ $child['icon'] }} sidebar-nav-icon" aria-hidden="true"></i>
                                            <span class="sidebar-nav-label">
                                                {{ $child['label'] }}
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a
                                class="sidebar-nav-link"
                                href="{{ $item['href'] }}"
                                @if ($isActive) aria-current="page" @endif>
                                <i class="ph {{ $item['icon'] }} sidebar-nav-icon" aria-hidden="true"></i>
                                <span class="sidebar-nav-label">
                                    {{ $item['label'] }}
                                </span>
                                @isset($item['badge'])
                                    <span class="sidebar-nav-badge">
                                        {{ $item['badge'] }}
                                    </span>
                                @endisset
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endforeach
    </nav>

    <div class="sidebar-support">
        <a
            class="sidebar-nav-link"
            href="https://amber.larajax.org"
            target="_blank"
            rel="noopener">
            <i class="ph ph-book-open sidebar-nav-icon" aria-hidden="true"></i>
            <span class="sidebar-nav-label">
                Documentation
            </span>
        </a>
    </div>
</aside>
