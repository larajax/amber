<div class="d-flex flex-wrap align-items-start gap-5">
    {{ Ui::pieChart(size: 160, centerText: '114', values: [
        'Published' => [84, 'var(--bs-success-base)'],
        'Drafts' => [12, 'var(--bs-warning-base)'],
        'Deleted' => [18, 'var(--bs-danger-base)'],
    ]) }}

    {{ Ui::pieChart(size: 160, centered: true, wrapLegend: true, values: [
        'Search' => 412,
        'Direct' => 238,
        'Social' => 97,
        'Email' => 64,
    ]) }}
</div>
