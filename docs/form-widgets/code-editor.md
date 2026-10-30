---
subtitle: Edit code with syntax highlighting, a toolbar and fullscreen.
demo: /reference/form-widgets/code-editor
---
# Code Editor

The `codeeditor` field edits code in [Monaco](https://microsoft.github.io/monaco-editor/), the editor
behind VS Code, with a toolbar and a fullscreen mode. It saves the code as plain text. Monaco loads the
first time a page shows a code field, so other pages don't pay for its size.

## Default editor {#default}

With no options, the editor highlights PHP, wraps long lines at the edge of the field and follows the
page's light or dark mode.

The toolbar runs undo (`Ctrl+Z`) and redo (`Ctrl+Y`), find (`Ctrl+F`) and replace (`Ctrl+H`), Format
Code (`Shift+Alt+F`), Word Wrap (`Alt+Z`) and fullscreen (`Ctrl+Shift+F`, and `Escape` to leave it),
with the language name on the right. Format Code is disabled for a language that has no formatter.

<<< @/../resources/ui/reference/form-widgets/code-editor/default.yaml

## Language {#language}

`language` picks the highlighting: `php` (the default), `html`, `css`, `scss`, `less`, `javascript`,
`json`, `yaml`, `markdown`, `twig` or `plaintext`. The short names `js`, `md`, `yml`, `htm`, `sass`,
`txt` and `plain_text` work too.

Emmet abbreviations expand with `Tab` in `html`, `twig` and `php` (HTML abbreviations) and in `css`,
`scss` and `less`. HTML, CSS (with SCSS and Less) and JSON also get language aware completion, CSS and
JSON mark errors as you type, and Format Code works for those languages.

<<< @/../resources/ui/reference/form-widgets/code-editor/language.yaml

## Size {#size}

`size` sets the starting height: `tiny`, `small`, `large` (the default), `huge` or `giant`. The editor
still grows with its content.

<<< @/../resources/ui/reference/form-widgets/code-editor/size.yaml

## Word wrap {#word-wrap}

`wordWrap` is `fluid` (the default, wrap at the edge of the field), `off`, `40` or `80` (wrap at that
column). `true` and `false` mean `fluid` and `off`. `showPrintMargin` draws a ruler at column 80, or at
40 when `wordWrap` is `40`. The Word Wrap button turns wrapping on and off while editing, without
changing the setting.

<<< @/../resources/ui/reference/form-widgets/code-editor/word-wrap.yaml

## Indentation and folding {#indentation}

`tabSize` sets the indent width (4 by default), and `useSoftTabs: false` indents with tab characters
instead of spaces. `showInvisibles` shows spaces and tabs as faint marks. `codeFolding: markbegin` keeps
the fold arrows in the gutter in view, where `manual` (the default) shows them on hover.

<<< @/../resources/ui/reference/form-widgets/code-editor/indentation.yaml

## Appearance {#appearance}

`fontSize` sets the text size in pixels (12 by default), and `margin` the space above the first line
and below the last (the editor keeps at least 8 pixels). `showGutter: false` hides the line numbers and
fold arrows, `highlightActiveLine: false` stops highlighting the line with the cursor, and
`displayIndentGuides: false` hides the indent guides.

`theme` takes `vs`, `vs-dark`, `hc-black` or `hc-light`. Monaco applies one theme to the whole page,
so editors that name different themes all end up with the last one applied. Leave it empty, as these
examples do, to follow the page color mode.

<<< @/../resources/ui/reference/form-widgets/code-editor/appearance.yaml

## Typing aids {#typing}

`autocompletion: live` suggests words and properties while you type. With `manual` (the default) or
`basic`, suggestions wait for `Ctrl+Space`. `autoClosing: false` stops the editor closing brackets,
quotes and tags for you, and `enableSnippets: false` leaves snippets out of the suggestions.

<<< @/../resources/ui/reference/form-widgets/code-editor/typing.yaml

## Read-only {#read-only}

A `readOnly` editor keeps its toolbar, with the editing buttons disabled, and the code can still be
selected, searched and copied. A `disabled` field looks the same, and its value is not saved.

<<< @/../resources/ui/reference/form-widgets/code-editor/read-only.yaml

## Preview {#preview}

In preview mode the field shows the code as escaped plain text, without loading the editor, and posts
nothing. This example's form is made with `previewMode` on.

<<< @/../resources/ui/reference/form-widgets/code-editor/preview.yaml

## Options

Option | Description
------------- | -------------
**language** | the language to highlight, see [Language](#language). Default: `php`.
**size** | the starting height: `tiny`, `small`, `large`, `huge` or `giant`. Default: `large`.
**wordWrap** | `fluid`, `off`, `40` or `80`. Default: `fluid`.
**showPrintMargin** | draws a ruler at column 80, or 40 with `wordWrap: 40`. Default: `false`.
**tabSize** | the indent width in spaces. Default: `4`.
**useSoftTabs** | indents with spaces rather than tab characters. Default: `true`.
**showInvisibles** | shows spaces and tabs. Default: `false`.
**codeFolding** | `manual` shows the fold arrows on hover, `markbegin` always. Default: `manual`.
**fontSize** | the text size in pixels. Default: `12`.
**margin** | the space above the first line and below the last, in pixels. Default: `0` (at least 8 is kept).
**showGutter** | shows the line numbers and fold arrows. Default: `true`.
**highlightActiveLine** | highlights the line with the cursor. Default: `true`.
**displayIndentGuides** | shows the indent guides. Default: `true`.
**theme** | `vs`, `vs-dark`, `hc-black` or `hc-light`, or empty to follow the page color mode. Default: empty.
**autocompletion** | `live` suggests while typing, `manual` and `basic` on `Ctrl+Space`. Default: `manual`.
**autoClosing** | closes brackets, quotes and tags as you type. Default: `true`.
**enableSnippets** | offers snippets among the suggestions. Default: `true`.
**readOnly** | shows the code without editing it, and still saves it. Default: `false`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `disabled`
and `stretch`.
