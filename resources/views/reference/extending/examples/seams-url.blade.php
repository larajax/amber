<ul class="list-unstyled mb-0">
    <li>
        <code>
            {{ app('larajax.ui')->url('samples/create') }}
        </code>
    </li>
    <li>
        <code>
            {{ app('larajax.ui')->url('reference://ui/tabs') }}
        </code>
    </li>
    <li>
        <code>
            {{ app('larajax.ui')->url('reference://extending/retired') ?: 'An empty string' }}
        </code>
    </li>
</ul>
