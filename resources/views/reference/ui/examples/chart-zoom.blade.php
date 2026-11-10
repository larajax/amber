@php
    $signups = [];

    foreach (range(0, 89) as $day) {
        $signups[] = [now()->startOfDay()->subDays(89 - $day), 30 + (int) round(10 * sin($day / 8)) + intdiv($day, 4)];
    }
@endphp

<p>
    Drag across the chart to zoom in.
    <a href="#" id="chartResetZoom" style="display: none">
        Reset zoom
    </a>
</p>

{{ Ui::lineChart(height: 220, zoomable: true, resetZoomLink: '#chartResetZoom', datasets: [
    'Signups' => ['color' => 'var(--bs-violet-500)', 'data' => $signups, 'points' => ['show' => false]],
]) }}
