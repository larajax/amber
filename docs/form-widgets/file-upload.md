---
subtitle: Upload images and files to a model's file relations.
demo: /reference/form-widgets/file-upload
---
# File Upload

The `fileupload` field uploads files to a relation of the model, by clicking Upload or dropping files on
the field. Each upload becomes a record of a file model, attached through the relation, so the field
writes nothing to the model's own columns.

## How files are stored {#storage}

The field name is the name of a relation to a file model. A `hasOne` or `morphOne` relation holds one
file, and a `hasMany` or `morphMany` relation a list. The file model implements `AttachmentInterface`,
which stores each upload and returns its address, see [file attachments](/guides/models#file-attachments).
The examples on this page use these relations, each a morph relation narrowed to its own field:

<<< @/../app/Models/Sample.php#docs-attachments{php}

An upload is held until the record saves. A new upload is stored as a file record straight away, with
no record to belong to, and its key is kept in the session against the form's session key. Removing a
file waits the same way. Both take effect when the record's Eloquent `saved` event fires, so leaving the
page without saving leaves the record as it was, and a record that does not exist yet can still take
uploads. The form posts its key as `_session_key`, and [Building a Form Page](/guides/forms#held-uploads)
shows how the save picks it up.

## Single image {#image-single}

`mode: image` shows the file as a thumbnail. On a `morphOne` relation the field holds one image, and a
new upload replaces it. `imageWidth` and `imageHeight` set the thumbnail size in pixels, and the field
asks the file model's `getThumb()` for a thumbnail of that size. The preview shows in the same shape,
its shorter side 6.75rem long, and narrows to fit the field. Without `fileTypes`, image mode accepts
jpg, jpeg, bmp, png, webp, avif and gif.

<<< @/../resources/ui/reference/form-widgets/file-upload/image-single.yaml

## Multiple images {#image-multi}

On a `morphMany` relation the field lists every image in a grid. Drag the handle to sort them, and the
new order is written straight away when the file model implements `SortableInterface`. Check several
images to delete them together.

`maxFiles` caps how many files the field takes. `fileTypes` limits the extensions, as a comma separated
list. `thumbOptions` passes options to the file model's `getThumb()`, such as a resize `mode` and an
`extension`, for a file model that resizes images. The demo's file model has no resizer, so its
thumbnails are the images themselves, shown at the thumbnail size.

<<< @/../resources/ui/reference/form-widgets/file-upload/image-multi.yaml

## Crop to a size {#image-crop}

`cropWidth` and `cropHeight` crop each picked image to the shape of that size before it uploads. An
image of another shape opens a popup, where dragging moves the image and the slider or the mouse wheel
zooms it, and Crop saves the part in the frame. The part is saved at its own size, up to the crop size,
so a larger image is scaled down and a smaller one is never enlarged. An image that is the shape already
uploads without the popup, and closing the popup leaves the field as it was.

The server checks each upload has the shape, so an image that skipped the browser's crop is refused.
Shapes within one percent of each other count as the same, as does the shape on its side, since a photo
can be turned by its Exif data. Cropping works in image mode only.

When the field sets neither `imageWidth` nor `imageHeight`, the preview takes the crop's shape. The
thumbnail keeps the default of 190 pixels on its shorter side and grows the longer one to match, so
this banner's thumbnail is made at 456 by 190. A field that sets both keeps its own size and shape.

<<< @/../resources/ui/reference/form-widgets/file-upload/image-crop.yaml

## Crop multiple images {#image-multi-crop}

On a `morphMany` relation, each picked image is cropped in turn. Several images picked or dropped at
once open the popup one after another, its title counting through them, such as Crop Image (2/3).
Closing a popup skips that image and moves on to the next.

This crop is taller than it is wide, so each preview is a portrait, its thumbnail made at 190 by 253
pixels.

<<< @/../resources/ui/reference/form-widgets/file-upload/image-multi-crop.yaml

## Single file {#file-single}

`mode: file` lists the file by its name and size instead of a thumbnail. `fileTypes` checks the
extension of the file name, and `mimeTypes` also checks the type of the content, so a file renamed to
`.pdf` is refused. `maxFilesize` sets the largest file in megabytes. It defaults to PHP's upload limit,
and the field throws an error when it is set higher than that limit.

<<< @/../resources/ui/reference/form-widgets/file-upload/file-single.yaml

Without `fileTypes`, file mode accepts a broad default set of documents, images, audio, video, fonts and
archives.

## Multiple files {#file-multi}

On a `morphMany` relation in file mode, each file has a checkbox and a drag handle. `size` sets how
tall the list grows before it scrolls: `tiny` (100 pixels), `small` (200), `large` (400, the default),
`huge` (500) or `giant` (700).

<<< @/../resources/ui/reference/form-widgets/file-upload/file-multi.yaml

## Private files {#private}

The field follows the relation, and the YAML needs nothing extra. The `contract` relation sets
`is_public` to false through `withAttributes()`, so each upload is a private file, which the demo's file
model stores in its protected folder instead of its public one. Where those folders live, and how a
private file is served, is up to the file model. The demo keeps both folders on the public disk, so an
application with private files would serve them through a controller of its own.

<<< @/../resources/ui/reference/form-widgets/file-upload/private.yaml

## Attach on upload {#attach-on-upload}

`deferredBinding: false` attaches each upload to the record as soon as it finishes, without waiting for
Save. This only applies to a saved record, since a new one has nothing to attach to yet, and removing a
file still waits for Save.

<<< @/../resources/ui/reference/form-widgets/file-upload/attach-on-upload.yaml

## Preview {#preview}

`disabled` puts the field in preview mode: the files show without the toolbar, the checkboxes or the
drag handles, and the upload handlers refuse requests. With no files, the field says so. This copy reads
the gallery through `valueFrom`.

<<< @/../resources/ui/reference/form-widgets/file-upload/preview.yaml

## Options

Option | Description
------------- | -------------
**mode** | `image` for thumbnails or `file` for a list of names. The relation picks single or multiple, or name both, such as `image-multi`. Default: `image`.
**imageWidth** | the thumbnail width in pixels. With `imageHeight`, it also sets the shape the preview shows in. Default: `190`, or the crop's shape when neither size is set.
**imageHeight** | the thumbnail height in pixels. Default: `190`, or the crop's shape when neither size is set.
**cropWidth** | with `cropHeight`, crops each picked image to the shape of this size before it uploads, and scales a larger one down to it. Image mode only. Default: no crop.
**cropHeight** | see `cropWidth`.
**thumbOptions** | options passed to the file model's `getThumb()`, such as `mode` and `extension`, or `false` to show the original images. Default: `mode: auto` and `extension: auto`.
**fileTypes** | the accepted extensions, as a comma separated list, or `*` for any. Default: the image set in image mode, the default set in file mode.
**mimeTypes** | the accepted content types, as a comma separated list of extensions, checked against the type read from the uploaded content. Default: no check.
**maxFilesize** | the largest file in megabytes, which cannot exceed PHP's upload limit. Default: PHP's upload limit.
**maxFiles** | the most files the field takes. Default: no limit.
**deferredBinding** | holds uploads against the session key until the record saves. `false` attaches them to a saved record straight away. Default: `true`.
**size** | in multiple mode, how tall the list grows before it scrolls: `tiny`, `small`, `large`, `huge` or `giant`. Default: `large`.

The field also takes the common [field properties](/forms/fields#field-properties), such as `disabled`
and `valueFrom`.
