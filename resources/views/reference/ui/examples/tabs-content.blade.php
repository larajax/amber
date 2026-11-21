@php
    $sections = ['Profile', 'Orders', 'Invoices', 'Payments', 'Addresses', 'Notes'];
@endphp

<div class="control-tabs content-tabs" data-control="tab">
    <ul class="nav nav-tabs">
        @foreach ($sections as $section)
            <li @class(['active' => $loop->first])>
                <a href="javascript:;">
                    {{ $section }}
                </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach ($sections as $section)
            <div @class(['tab-pane', 'active' => $loop->first])>
                <div class="padded-container">
                    <p class="mb-0">
                        The customer's {{ strtolower($section) }}.
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
