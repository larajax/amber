@if ($tags->isNotEmpty())
    <ul class="list-unstyled d-flex flex-wrap gap-2 mb-0">
        @foreach ($tags as $tag)
            <li class="badge badge-subtle theme-secondary">
                {{ $tag->name }}
                <span class="fg-3">
                    {{ $tag->posts_count }}
                </span>
            </li>
        @endforeach
    </ul>
@else
    <p class="fg-3 mb-0">
        No tags match "{{ $term }}".
    </p>
@endif
