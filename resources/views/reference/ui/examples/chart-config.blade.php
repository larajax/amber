{{ Ui::lineChart(height: 220, datasets: [
    'Temperature' => ['color' => 'var(--bs-red-500)', 'data' => [
        '2026-11-01' => 18, '2026-11-02' => 21, '2026-11-03' => 19, '2026-11-04' => 24, '2026-11-05' => 26,
    ]],
], config: [
    'scales' => [
        'x' => ['time' => ['unit' => 'day']],
        'y' => ['min' => 0, 'max' => 30, 'ticks' => ['stepSize' => 10]],
    ],
    'plugins' => ['legend' => ['display' => false]],
]) }}
