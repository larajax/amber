---
subtitle: Write Markdown with a formatting toolbar, a preview and fullscreen.
demo: /reference/form-widgets/markdown-editor
---
# Markdown Editor

The `markdown` field edits Markdown with a formatting toolbar, a preview and a fullscreen mode, built on
[EasyMDE](https://github.com/Ionaru/easy-markdown-editor). It saves the Markdown text as written.
EasyMDE loads the first time a page shows a Markdown field.

## Default editor {#default}

The toolbar wraps the selection in Markdown: bold, italic, strikethrough, headings 1 to 3, code blocks,
quotes, lists, clean block, links, images, tables and horizontal rules. The last three buttons switch to
the preview, to side by side and to fullscreen. Its icons come from the configured
[icon pack](/extending/icons) (the `markdowneditor.*` keys).

EasyMDE's keyboard shortcuts work as well, such as `Ctrl+B` for bold, `Ctrl+I` for italic, `Ctrl+K`
for a link, `Ctrl+P` for the preview, `F9` for side by side and `F11` for fullscreen. The field
`placeholder` shows while the text is empty.

<<< @/../resources/ui/reference/form-widgets/markdown-editor/default.yaml

## Side by side {#side-by-side}

`sideBySide` opens the editor with the preview beside the text, following its scrolling. `mode: split`
does the same, where `mode: tab` (the default) keeps the preview behind its button. The toolbar button
switches back.

<<< @/../resources/ui/reference/form-widgets/markdown-editor/side-by-side.yaml

## Size {#size}

`size` sets the starting height: `tiny`, `small`, `large` (the default), `huge` or `giant`. The editor
still grows with its content.

<<< @/../resources/ui/reference/form-widgets/markdown-editor/size.yaml

## Safe mode {#safe-mode}

The preview is rendered in the browser, and its HTML runs through
[DOMPurify](https://github.com/cure53/DOMPurify), which removes scripts, event handlers and tags such as
iframes. `safeMode: false` shows HTML in the Markdown as written, so turn it off only for trusted
authors.

Safe mode does not change the saved text, so render the Markdown safely wherever your app shows it.

<<< @/../resources/ui/reference/form-widgets/markdown-editor/safe-mode.yaml

## Read-only {#read-only}

A `readOnly` field shows the rendered Markdown instead of the editor, with any HTML in it escaped, and a
hidden input keeps posting the value so saving the form keeps it.

<<< @/../resources/ui/reference/form-widgets/markdown-editor/read-only.yaml

## Disabled {#disabled}

A `disabled` field also shows the rendered Markdown, but posts nothing, so saving the form leaves the
value alone.

<<< @/../resources/ui/reference/form-widgets/markdown-editor/disabled.yaml

## Preview {#preview}

In preview mode the field shows the rendered Markdown, with any HTML in it escaped. This example's form
is made with `previewMode` on.

<<< @/../resources/ui/reference/form-widgets/markdown-editor/preview.yaml

## Options

Option | Description
------------- | -------------
**sideBySide** | opens the editor with the preview beside the text. Default: `false`.
**mode** | `tab` keeps the preview behind its button, `split` opens it side by side. Default: `tab`.
**safeMode** | cleans the preview HTML with DOMPurify. Default: `true`.
**size** | the starting height: `tiny`, `small`, `large`, `huge` or `giant`. Default: `large`.

The field also takes the common [field properties](/forms/fields#field-properties), such as
`placeholder`, `readOnly` and `disabled`.
