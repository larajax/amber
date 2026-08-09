---
subtitle: Action buttons and a search bar for list views.
---
# Toolbar

The Toolbar widget renders a horizontal control bar, typically above a [List widget](lists.md). It has
three optional parts: a buttons area for actions, a search box, and a list setup icon.

## Rendering a toolbar

Build the widget with `Toolbar::make(...)` and render it above the list:

```php
use October\Amber\Widgets\Toolbar;

$toolbar = Toolbar::make([
    'alias' => 'toolbar',
    'buttons' => '~/resources/views/users/_toolbar_buttons.php',
    'search' => ['prompt' => 'Search users...'],
]);
```

```blade
{!! $toolbar->render() !!}

{!! $widget->render() !!}
```

## Widget configuration

Option | Description
------------- | -------------
**buttons** | a partial rendering the toolbar buttons, resolved through the controller.
**search** | search widget configuration array, or a partial name for a custom search area. Omit to hide the search box.
**setupHandler** | an AJAX handler name that opens the list setup popup, rendering a setup icon in the toolbar.

The `listWidgetId` property links the toolbar to a list element for button integrations:

```php
$toolbar->listWidgetId = $widget->getId();
```

With the linkage in place, toolbar buttons can react to the list's row selection — see
[checked-state buttons](lists.md#checked-state-buttons).

## Toolbar buttons

The `buttons` partial renders through the controller, so the controller needs Amber's `ViewMaker`
trait (see [Installation](installation.md)). Compose the buttons with the
[`Ui` facade](ui.md):

```php
<!-- resources/views/users/_toolbar_buttons.php -->
<div data-control="toolbar">
    <?= Ui::button(
        label: 'New User',
        href: 'users/create',
        primary: true
    ) ?>

    <?= Ui::ajaxButton(
        label: 'Delete Selected',
        handler: 'onDeleteSelected',
        danger: true
    ) ?>
</div>
```

## Search

Pass an array to `search` to configure the built-in search widget:

Option | Description
------------- | -------------
**prompt** | the search placeholder text.
**mode** | the search strategy: `all` (contains all words), `any` (contains any word) or `exact` (contains the phrase). Passed to the list.
**scope** | a custom model query scope method to perform the search. Passed to the list.
**searchOnEnter** | wait for the enter key instead of searching on every keystroke. Default: `false`.
**partial** | render a custom partial instead of the standard search input.

The search term persists in the session, so it survives page reloads until cleared.

### Wiring search to a list

The search widget fires a `search.submit` event when a term is entered. Connect it to the list in your
controller action — apply the active term for the initial render, then bind the event to refresh the
list when the term changes:

```php
$toolbar = Toolbar::make([
    'alias' => 'toolbar',
    'search' => ['prompt' => 'Search users...'],
]);

$search = $toolbar->getSearchWidget();

// Apply the active term when the page renders
$widget->setSearchTerm($search->getActiveTerm());

// Refresh the list when the term changes
$search->bindEvent('search.submit', function () use ($widget, $search) {
    $widget->setSearchTerm($search->getActiveTerm(), true);
    return $widget->onRefresh();
});
```

The second argument to `setSearchTerm` resets the list to page one for a new search. Only columns
marked `searchable: true` in the list definition are searched.

Amber registers the underlying `searchWhere` query methods as Eloquent builder macros, so searching
works against plain `Illuminate` models as well as October Rain models.

## Next steps

- [Lists](lists.md) — the record list this toolbar controls
- [UI Elements](ui.md) — the buttons rendered inside the toolbar
