---
subtitle: Pick a hex color from a row of swatches.
demo: /reference/form-widgets/color-picker
---
# Color Picker

The `colorpicker` field opens a color picker ([Coloris](https://coloris.js.org)) with preset colors
shown as swatches. It saves the color as a hex string with a leading `#`, and normalizes a value typed
without one.

## Default swatches {#default}

With no options, the field shows a default set of 20 swatches and a picker for any other color.

<<< @/../resources/ui/reference/form-widgets/color-picker/default.yaml

## Fixed swatches {#fixed-swatches}

`availableColors` replaces the default swatches with your own list. Add `allowCustom: false` to limit
the choice to those swatches, so the picker offers nothing else.

<<< @/../resources/ui/reference/form-widgets/color-picker/fixed-swatches.yaml

## Swatches from the model {#model-colors}

`availableColors` also takes the name of a method on the form's model that returns the colors. When the
model has no such method, the form throws an error that names it.

<<< @/../resources/ui/reference/form-widgets/color-picker/model-colors.yaml

<<< @/../app/Models/Sample.php#docs-brand-colors{php}

## Opacity and clearing {#alpha}

`showAlpha` adds an opacity slider, and the value is then saved as 8 digit hex, such as `#3498db80`.
`allowEmpty` adds a clear button, which saves no color at all.

<<< @/../resources/ui/reference/form-widgets/color-picker/alpha.yaml

## Picker only {#input-only}

`showInput` drops the swatches, so the field opens straight into the picker for any color.

<<< @/../resources/ui/reference/form-widgets/color-picker/input-only.yaml

## Read-only {#read-only}

A `readOnly` field shows the color and its swatch without opening the picker, and still posts its value,
so saving the form keeps it. A `disabled` field, or a form in preview mode, looks the same but posts
nothing.

<<< @/../resources/ui/reference/form-widgets/color-picker/read-only.yaml

## Options

Option | Description
------------- | -------------
**availableColors** | the swatches, as a list of hex colors or the name of a model method that returns them. Default: a set of 20 colors.
**allowCustom** | lets the picker choose any color as well as the swatches. Default: `true`.
**allowEmpty** | adds a clear button that empties the value. Default: `false`.
**showAlpha** | adds an opacity slider and saves 8 digit hex. Default: `false`.
**showInput** | shows the picker without swatches. Default: `false`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `readOnly`
and `disabled`.
