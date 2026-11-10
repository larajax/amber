<nav class="nav nav-pills mb-4" aria-label="Viewer timezone">
    @foreach (['UTC', 'Australia/Sydney', 'America/New_York', 'Mars/Olympus'] as $timezone)
        <a
            @class(['nav-link', 'active' => app('larajax.ui')->timezone() === $timezone])
            href="{{ route('reference.extending.seams', ['timezone' => $timezone]) }}#timezone">
            {{ $timezone }}
        </a>
    @endforeach
</nav>

<p class="mb-0">
    <code>
        app('larajax.ui')->timezone()
    </code>
    returns
    <strong>
        {{ app('larajax.ui')->timezone() }}
    </strong>
</p>
