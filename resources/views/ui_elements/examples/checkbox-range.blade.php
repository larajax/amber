<ul class="list-unstyled mb-0" id="uiWeekdays">
    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
        <li class="form-field mb-2">
            <input type="checkbox" class="check" id="uiDay{{ $day }}" name="days[]" value="{{ $day }}">
            <label for="uiDay{{ $day }}">
                {{ $day }}
            </label>
        </li>
    @endforeach
</ul>

<script>
    document.getElementById('uiWeekdays').addEventListener('click', (ev) => {
        if (ev.target.matches('input[type=checkbox]')) {
            jax.checkboxRangeRegisterClick(ev, 'li', 'input[type=checkbox]');
        }
    });
</script>
