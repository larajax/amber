---
subtitle: Edit formatted text with a toolbar, selection menus and an HTML view.
demo: /reference/form-widgets/rich-editor
---
# Rich Editor

The `richeditor` field edits formatted text with a toolbar, menus that appear on the selection and an
HTML source view, built on [TipTap](https://tiptap.dev). It saves the content as HTML. The editor loads
the first time a page shows a rich editor field.

## Default toolbar {#default}

With no options, the toolbar has paragraph formats, alignment, bold, italic and underline, ordered and
unordered lists, snippets, tables, links, images, a horizontal line and the HTML view. Selecting a link,
an image or a table opens a menu of its own, such as image alignment or table rows and columns.

Without more setup, images go in by address, links open a form for the address, and the snippet button
stays disabled. [Uploads](#uploads) add Upload from Computer to the image menu, and
[page hooks](#page-hooks) connect a media picker, a page link picker and snippets.

<<< @/../resources/ui/reference/form-widgets/rich-editor/default.yaml

## Toolbar buttons {#toolbar}

`toolbarButtons` lists the buttons in order, with `'|'` for a separator. It also takes a pipe
separated string, such as `bold|italic||insertLink`, where a double pipe is a separator. The list is
used exactly as given.

<<< @/../resources/ui/reference/form-widgets/rich-editor/toolbar.yaml

## Every button {#full-toolbar}

These are the built-in buttons that work on any page. Three more depend on the page: `insertFile`
offers Upload from Computer only once [uploads](#uploads) are on (and takes an address until then),
while `insertPageLink` and `insertSnippet` reach a picker only when the page sets the matching
[hooks](#page-hooks). `emoticons`, left out here, adds a panel of emoji. The full list is in
[Toolbar buttons](#button-list).

Keyboard shortcuts include `Ctrl+B`, `Ctrl+I` and `Ctrl+U` for bold, italic and underline,
`Ctrl+Shift+S` for strikethrough, and `Ctrl+K`, which opens the link picker or the link form.

<<< @/../resources/ui/reference/form-widgets/rich-editor/full-toolbar.yaml

## Uploads {#uploads}

`uploadDisk` names a filesystem disk and turns on the field's `onUploadFile` handler. Files uploaded
from the image and file menus, and images pasted or dropped into the editor, are stored on that disk
under `uploadPath` (`uploads/richeditor` by default) with hashed names, and the editor inserts the
disk's URL for them, so pick a disk with public URLs, such as `public`.

Images accept jpg, jpeg, png, gif and webp. File links also accept pdf, doc, docx, xls, xlsx, ppt,
pptx, txt, csv and zip. Both the file name and its content must match an accepted type, and
`uploadMaxSize` limits each file in megabytes (10 by default). Uploads are off in read-only fields and
in preview mode.

<<< @/../resources/ui/reference/form-widgets/rich-editor/uploads.yaml

A page `uploadFile` [hook](#page-hooks) replaces this upload, for a page that prefers its own endpoint.

## Line breaks {#line-breaks}

`useLineBreaks` makes `Enter` add a `<br>` inside the same paragraph rather than start a new one, for
addresses and signatures.

<<< @/../resources/ui/reference/form-widgets/rich-editor/line-breaks.yaml

## Editor options {#editor-options}

`editorOptions` passes settings to the editor. Here `paragraphFormat` replaces the paragraph formats,
`paragraphStyles` and `linkStyles` replace the class names those menus offer, and images insert on the
left at their own size, with `imageDefaultAlign: left` and `imageResize: false`. The style maps are
class names mapped to labels, and the classes go into the saved HTML, so style them wherever the content
is shown. The full list is in [Editor options](#editor-option-list).

<<< @/../resources/ui/reference/form-widgets/rich-editor/editor-options.yaml

## Size {#size}

`size` sets the starting height: `tiny`, `small`, `large` (the default), `huge` or `giant`. The editor
still grows with its content.

<<< @/../resources/ui/reference/form-widgets/rich-editor/size.yaml

## Read-only {#read-only}

A `readOnly` editor keeps its toolbar, disabled, and the content can still be selected and copied. A
`disabled` field looks the same, and its value is not saved.

<<< @/../resources/ui/reference/form-widgets/rich-editor/read-only.yaml

## Preview {#preview}

In preview mode the field shows the HTML itself, cleaned of scripts and event handlers.
`safeMode: false` shows it unchanged, so keep safe mode on unless every author is trusted. This
example's form is made with `previewMode` on.

<<< @/../resources/ui/reference/form-widgets/rich-editor/preview.yaml

## Page hooks and custom buttons {#page-hooks}

Media pickers, link pickers and snippets belong to the page, and so can uploads. Connect them through
the shared `richEditor` object, which a page script imports with
`import { richEditor } from '/vendor/larajax/ui/index.js'`. Set its hooks and register buttons at the
top level of the script, before the editors load, and they apply to every rich editor on the page.
Each hook is optional: without it, the editor hides or falls back for the feature it powers.

Hook | Description
------------- | -------------
**uploadFile** | `(file, { type })` stores a file and resolves with its URL, where `type` is `image` or `file`. Replaces the `uploadDisk` upload.
**browseMedia** | `({ type, onInsert })` opens a picker for an `image`, `video`, `audio` or `file`, calling `onInsert` with a list of `{ url, title, type }`.
**browseLinks** | `({ value, onInsert })` opens a link picker for `insertPageLink`, calling `onInsert` with `{ url, title }`.
**resolveLink** | `(href)` turns a stored link into an address the browser can open.
**browseSnippets** | `({ onInsert })` opens a snippet picker, calling `onInsert` with the snippet markup.
**mountSnippet** | `(element, { changed, opened, closed })` prepares a snippet for editing and returns a function that releases it. Without it, snippets show as a labelled placeholder.
**editSnippet** | `(element)` opens a snippet's properties, returning `false` when it cannot.
**alert** | `(message)` shows an error message. Default: the browser's `alert()`.
**tooltip** | `(label, shortcut)` returns the attributes that give a button its hover hint. Default: a `title`.

`richEditor.registerButton(name, definition)` adds a button of your own. Name it in a field's
`toolbarButtons`, or give it a `toolbar` placement to add it to the default toolbar of every field that
sets no buttons.

Key | Description
------------- | -------------
**label** | the tooltip and accessible label.
**icon** | an icon class name, or `{ html: '...' }` markup.
**toolbar** | `start`, `end`, `{ before: name }` or `{ after: name }`, its place in the default toolbar.
**separator** | `before`, `after` or `both` adds a separator beside it.
**onClick** | `(editor)` runs the button. The editor has `insertHtml()`, `getContent()`, `setContent()` and `focus()`.
**focus** | focuses the editor before `onClick`. Default: `true`.
**undo** | records an undo step after `onClick`. Default: `false`.

The toolbar and menu icons come from the configured [icon pack](/extending/icons) (the `richeditor.*`
keys), and the editor's labels and messages pass through `__()`, so they follow the app locale.

## Showing the saved HTML {#displaying-content}

Wherever your app shows the saved HTML, include `/vendor/larajax/ui/vendor/richarea/base-styles.css`
for the image alignment and the built-in style classes, and wrap the content in an element with the
`rich-editor-content` class to apply the default typography as well. Clean the HTML first when its
authors are not trusted.

## Options

Option | Description
------------- | -------------
**toolbarButtons** | the buttons, as a list or a pipe separated string, see [Toolbar buttons](#toolbar). Default: the default toolbar.
**toolbar** | the code of a named toolbar definition, used when `toolbarButtons` is not set. The base widget defines none, so it applies only in a subclass that provides them.
**editorOptions** | settings passed to the editor, see [Editor options](#editor-option-list).
**useLineBreaks** | `Enter` adds a line break instead of a new paragraph. Default: `false`.
**uploadDisk** | the filesystem disk that stores uploads, turning them on. Default: none.
**uploadPath** | the folder on the disk for uploads. Default: `uploads/richeditor`.
**uploadMaxSize** | the largest upload in megabytes. Default: `10`.
**safeMode** | cleans the HTML shown in preview mode. Default: `true`.
**readOnly** | shows the content without editing it, and still saves it. Default: `false`.
**size** | the starting height: `tiny`, `small`, `large`, `huge` or `giant`. Default: `large`.

The field also takes the common [field properties](/forms/fields#field-properties), such as
`placeholder`, `disabled` and `stretch`.

### Toolbar buttons {#button-list}

Group | Buttons
------------- | -------------
**Text** | `bold`, `italic`, `underline`, `strikeThrough`, `subscript`, `superscript`, `clearFormatting`
**Styles** | `paragraphFormat`, `paragraphStyle`, `inlineStyle`, `fontFamily`, `fontSize`, `color`, `emoticons`
**Blocks** | `align`, `formatOL`, `formatUL`, `outdent`, `indent`, `quote`, `insertHR`
**Insert** | `insertLink`, `insertPageLink`, `linkStyle`, `insertImage`, `insertVideo`, `insertAudio`, `insertFile`, `insertTable`, `insertSnippet`
**Tools** | `undo`, `redo`, `selectAll`, `html`, `fullscreen`

### Editor options {#editor-option-list}

Option | Description
------------- | -------------
**paragraphFormat** | the paragraph formats, as keys (`N`, `H1` to `H6`, `PRE`) mapped to labels. Default: Normal, Heading 1 to 4 and Code.
**paragraphStyles** | paragraph classes mapped to labels. Default: `oc-text-gray`, `oc-text-bordered`, `oc-text-spaced`, `oc-text-uppercase`.
**inlineStyles** | text classes mapped to labels. Default: `oc-class-code`, `oc-class-highlighted`, `oc-class-transparency`.
**linkStyles** | link classes mapped to labels. Default: `oc-link-green`, `oc-link-strong`.
**imageStyles** | image classes mapped to labels. Default: `oc-img-rounded`, `oc-img-bordered`.
**tableStyles** | table classes mapped to labels. Default: `oc-dashed-borders`, `oc-alternate-rows`.
**tableCellStyles** | table cell classes mapped to labels. Default: `oc-cell-highlighted`, `oc-cell-thick-border`.
**imageDefaultAlign** | where new images go: `left`, `center` or `right`. Default: `center`.
**imageResize** | lets images be resized by dragging their handles. Default: `true`.
**imageAlignClasses** | the classes for each image alignment, keyed `left`, `center` and `right`.
**fontFamily** | CSS font stacks mapped to labels for the `fontFamily` button. Default: six web safe fonts.
**fontSize** | the sizes the `fontSize` button offers, in pixels. Default: `8` to `96`.
**colors** | the hex colors the `color` button offers. Default: a palette of 27 colors.
**emoticons** | the emoji the `emoticons` button offers. Default: 60 emoji.
**linkProtocols** | extra URL schemes allowed in links, beyond the usual web, mail and phone ones. Default: none.

Other keys, and those the field sets itself (`readOnly`, `useLineBreaks`, `toolbarButtons`), are
ignored.
