---
subtitle: Country flags from a class with the country's code.
demo: /reference/ui/flags
---
# Flags

The flag element shows a country's flag from a class named after its two letter code in lower case,
such as `flag-au`. Each flag is an SVG image that loads only when a page shows it, so the full set costs
nothing until it is used. The [phone field](/form-widgets/phone) uses these flags in its country picker.

## Country flags {#flags-countries}

Put the class on an empty `span`. There is a flag for every country.

<<< @/../resources/views/reference/ui/examples/flags-countries.blade.php{blade}

## Sized by the text {#flags-sizing}

A flag is 4 by 3 and as tall as the text around it, so it grows and shrinks with the font size of its
parent, with no size of its own to set.

<<< @/../resources/views/reference/ui/examples/flags-sizing.blade.php{blade}

## More flags {#flags-special}

Besides the countries, there are flags for the European Union (`flag-eu`) and the United Nations
(`flag-un`), and for the four nations of the United Kingdom: `flag-gb-eng`, `flag-gb-sct`,
`flag-gb-wls` and `flag-gb-nir`.

<<< @/../resources/views/reference/ui/examples/flags-special.blade.php{blade}
