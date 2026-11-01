---
subtitle: Take a phone number with a country picker, saved in the international format.
demo: /reference/form-widgets/phone
---
# Phone

The `phone` field takes a phone number with a country picker at its start. It formats the number as it
is typed, using the numbering rules of Google's libphonenumber
([libphonenumber-js](https://github.com/catamphetamine/libphonenumber-js)), and saves it in the
international E.164 format, such as `+61412345678`: a plus, the country code and the number, with no
spaces. That form means the same thing in every country, sorts and searches cleanly, and is the form
SMS and calling services expect. The rules load the first time a page shows a phone field.

## Phone number {#default}

A number typed without a country code belongs to `defaultCountry`, so `0412 345 678` in Australia saves
as `+61412345678`. Typing a number that starts with `+` picks its country as you type, and once the
field loses focus the number shows in that country's own format. A stored number shows in the format of
its country, with that country picked.

<<< @/../resources/ui/reference/form-widgets/phone/default.yaml

The cursor stays beside the same digit as the spaces and brackets move, and Backspace after a space
deletes the digit before it. When the field loses focus, a number with the wrong length for its country
is marked invalid. That check is the length only, so for a stricter rule, such as only Australian mobile
numbers, validate the number on the model, for example with the `phone` rule of the
[laravel-phone](https://github.com/Propaganistas/Laravel-Phone) package.

Without `defaultCountry`, the field takes the region of the app locale, such as `AU` for `en_AU`, and
otherwise the region of the browser's language.

## Choosing the countries {#countries}

The picker shows the country's flag and calling code, and opens a list of every country with its
[flag](/ui/flags), its name in the page's language and its calling code. `countries` limits the list
to a set of two letter codes, and `preferredCountries` puts some at the top, above a divider. The
country of a stored number is always listed, so the number still shows correctly.

<<< @/../resources/ui/reference/form-widgets/phone/countries.yaml

The list is Bootstrap's combobox, so it needs the Bootstrap script on the page, like the library's
dropdowns. It opens at the current country, the arrow keys move through it, typing a country's name
jumps to it (spaces included, as in "united k"), and Enter or Space picks the country and moves to the
number.

## Read-only {#read-only}

A `readOnly` field shows the number with the picker locked, and still posts its value, so saving the
form keeps it. A `disabled` field looks the same but posts nothing.

<<< @/../resources/ui/reference/form-widgets/phone/read-only.yaml

## Preview mode {#preview}

In preview mode the number is a link that calls it, in the national format when it belongs to the
default country and in the international format otherwise.

<<< @/../resources/ui/reference/form-widgets/phone/preview.yaml

## Saving

The field saves the E.164 number that the browser worked out. A number posted in the international format
with spaces or punctuation, such as `+61 (412) 345-678`, saves as `+61412345678`, and an empty field saves
as `null`. Anything else saves as posted, for the model's validation to judge.

## Options

Option | Description
------------- | -------------
**defaultCountry** | the two letter country for numbers typed without a country code. Default: the region of the app locale, otherwise of the browser's language.
**countries** | the two letter countries the picker offers. Default: every country.
**preferredCountries** | the two letter countries listed first in the picker.

The field also takes the common [field properties](/forms/fields#field-properties), such as `readOnly`,
`disabled` and `placeholder`.
