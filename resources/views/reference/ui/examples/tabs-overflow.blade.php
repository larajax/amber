@php
    $months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];
@endphp

<div class="control-tabs primary-tabs" data-control="tab">
    <ul class="nav nav-tabs">
        @foreach ($months as $month)
            <li @class(['active' => $loop->first])>
                <a href="javascript:;">
                    {{ $month }} report
                </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach ($months as $month)
            <div @class(['tab-pane', 'active' => $loop->first])>
                <p class="pt-4 mb-0">
                    The {{ $month }} pane.
                </p>
            </div>
        @endforeach
    </div>
</div>
