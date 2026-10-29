<div class="control-list">
    <table class="table data" data-control="rowlink">
        <thead>
            <tr>
                <th>
                    Row
                </th>
                <th>
                    Clicking it
                </th>
                <th>
                    Excluded cell
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <a href="{{ url('ui') }}">
                        Buttons page
                    </a>
                </td>
                <td>
                    Follows the link
                </td>
                <td class="nolink">
                    Not linked
                </td>
            </tr>
            <tr>
                <td>
                    <a href="javascript:;" data-control="popup" data-handler="onLoadDialog" data-request-data="size: 'small'" data-size="small">
                        Small dialog
                    </a>
                </td>
                <td>
                    Opens a popup
                </td>
                <td class="nolink">
                    Not linked
                </td>
            </tr>
            <tr>
                <td>
                    <a href="javascript:;" data-request="onRowAction" data-request-flash>
                        AJAX handler
                    </a>
                </td>
                <td>
                    Runs a request
                </td>
                <td class="nolink">
                    Not linked
                </td>
            </tr>
        </tbody>
    </table>
</div>
