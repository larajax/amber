{{ Ui::barChart(height: 120, values: [
    // A label and its value, colored from the palette
    'Monday' => 32,

    // A label and a [value, color] pair, where the color can be a CSS variable
    'Tuesday' => [41, 'var(--bs-violet-500)'],

    // A detailed definition, for labels that are not keys
    ['label' => 'Wednesday', 'value' => 27, 'color' => '#00b4f8'],
]) }}
