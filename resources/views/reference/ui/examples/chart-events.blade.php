{{ Ui::barChart(id: 'chartEvents', height: 80, values: ['Mon' => 12, 'Tue' => 19, 'Wed' => 9]) }}

<p class="mb-0" id="chartEventsLog">
    Waiting for the chart...
</p>

<script>
    document.getElementById('chartEvents').addEventListener('chart-bar:ready', (event) => {
        const chart = event.detail.chart;
        document.getElementById('chartEventsLog').textContent = `Chart.js ${chart.constructor.version} drew ${chart.data.datasets[0].data.length} bars.`;
    });
</script>
