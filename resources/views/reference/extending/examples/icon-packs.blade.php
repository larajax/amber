@php
    $iconManager = app(\Larajax\Ui\Classes\IconManager::class);
@endphp

<table class="table ui-icon-table">
    <thead>
        <tr>
            <th>
                Key
            </th>
            @foreach (['phosphor', 'bootstrap', 'amber'] as $iconPack)
                <th>
                    {{ $iconPack }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach (['action.delete', 'datepicker.date', 'pagination.next', 'repeater.add'] as $iconKey)
            <tr>
                <td>
                    <code>
                        {{ $iconKey }}
                    </code>
                </td>
                @foreach (['phosphor', 'bootstrap', 'amber'] as $iconPack)
                    <td>
                        <i class="{{ $iconManager->icons($iconPack)[$iconKey] }}" aria-hidden="true"></i>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
