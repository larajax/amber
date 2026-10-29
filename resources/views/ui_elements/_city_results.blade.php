@if ($term === '')
    <p class="fg-3 mb-0">
        Type a city name, such as Paris or Sydney.
    </p>
@elseif ($cities)
    <ul class="list-unstyled d-flex flex-wrap gap-2 mb-0">
        @foreach ($cities as $city)
            <li class="badge">
                {{ $city }}
            </li>
        @endforeach
    </ul>
@else
    <p class="fg-3 mb-0">
        No cities match "{{ $term }}".
    </p>
@endif
