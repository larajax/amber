<header class="topbar">
    <button
        class="icon-button topbar-menu"
        type="button"
        aria-label="Open navigation"
        aria-controls="amber-sidebar"
        aria-expanded="false"
        data-amber-menu-toggle>
        <i class="ph ph-list" aria-hidden="true"></i>
    </button>

    <div class="topbar-brand">
        @include('partials.site.brand')
    </div>

    <form
        class="search"
        action="{{ route('posts.index') }}"
        method="GET"
        role="search">
        <label class="visually-hidden" for="amber-search-input">
            Search posts
        </label>
        <label class="form-control form-adorn search-input">
            <span class="form-adorn-icon" aria-hidden="true">
                <i class="ph ph-magnifying-glass"></i>
            </span>
            <input
                id="amber-search-input"
                class="form-ghost"
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search posts..." />
        </label>
    </form>

    <div class="topbar-actions">
        <button
            class="icon-button theme-toggle"
            type="button"
            aria-label="Toggle dark mode"
            data-amber-theme-toggle>
            <i class="ph ph-moon theme-icon-light" aria-hidden="true"></i>
            <i class="ph ph-sun theme-icon-dark" aria-hidden="true"></i>
        </button>
    </div>
</header>
