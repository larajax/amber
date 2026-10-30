{{-- Ends a form page: the shared session key commits deferred uploads, and Save posts every example form on the page --}}
<input type="hidden" name="_session_key" value="{{ $sessionKey }}" />

<div class="reference-save-bar">
    {{ Ui::ajaxButton(dataRequestFlash: true, handler: 'onSaveExamples', label: 'Save', primary: true) }}
    <span class="fg-3">
        Every example on this page edits the same record, so Save stores them all.
    </span>
</div>
