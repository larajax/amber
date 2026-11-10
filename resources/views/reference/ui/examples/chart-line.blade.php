@php
    $visits = [];
    $orders = [];

    foreach (range(0, 27) as $day) {
        $date = now()->startOfMonth()->subMonth()->addDays($day)->toDateString();
        $visits[$date] = 400 + (int) (120 * sin($day / 3)) + ($day * 6);
        $orders[$date] = 40 + (int) (18 * cos($day / 4)) + $day;
    }
@endphp

{{ Ui::lineChart(height: 260, timeMode: 'weeks', datasets: [
    'Visits' => ['color' => 'var(--bs-blue-500)', 'data' => $visits],
    'Orders' => ['color' => 'var(--bs-green-500)', 'data' => $orders, 'lines' => ['fill' => false]],
]) }}
