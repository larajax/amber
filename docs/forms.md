---
subtitle: Build data-editing forms from YAML.
---
# Forms

The Form widget builds a working form from a `fields.yaml` definition bound to a model. Field values
populate from the model, tabs and layout come from configuration, and AJAX features (validation,
uploads, partial refresh) are wired through [Larajax](https://larajax.org) automatically.

## Rendering a form

Describe the fields in a YAML file:

```yaml
# resources/amber/user/fields.yaml
fields:
    name:
        label: Name
        type: text
        span: left

    email:
        label: Email Address
        type: email
        span: right

    is_activated:
        label: Activated
        type: checkbox
```

Build the widget in a controller action with `Form::make(...)`, bind it to a model, and pass it to the
view. The `fields` value may be a YAML file path or an inline array of definitions:

```php
namespace App\Controllers;

use App\Models\User;
use October\Amber\Widgets\Form;

class UserController extends ControllerBase
{
    public function edit($id)
    {
        $widget = Form::make([
            'alias' => 'form',
            'model' => User::findOrFail($id),
            'fields' => '~/resources/amber/user/fields.yaml',
        ]);

        return view('users.edit', ['widget' => $widget]);
    }
}
```

Render it in the view, inside a `<form>` element with your own submit control:

```blade
<form>
    {!! $widget->render() !!}

    {{ Ui::ajaxButton(label: 'Save', handler: 'onSave', primary: true) }}
</form>
```

## Saving submitted data

Handle the submit in an AJAX handler on the controller. `getSaveData()` returns the submitted values,
processed per the field definitions (disabled and hidden fields excluded, arrays normalized):

```php
public function onSave($id)
{
    $user = User::findOrFail($id);

    $widget = $this->makeUserForm($user);

    $user->fill($widget->getSaveData());
    $user->save();
}
```

Build the widget from a shared method so the page action and the AJAX handler configure it the same
way. Like all Larajax handlers, the route must accept POST requests.

## Widget configuration

Option | Description
------------- | -------------
**fields** | field definitions placed outside any tabs: a YAML file path or an array. A YAML file may also carry top-level `tabs` and `secondaryTabs` keys.
**tabs** | primary tab definitions and their fields.
**secondaryTabs** | secondary tab definitions and their fields.
**model** | the model instance the form binds to.
**data** | an optional data source for field values; the model is used when not supplied.
**context** | the active form context; fields declaring another context are not shown. Commonly `create` or `update`.
**arrayName** | wraps field input names in an array, e.g. `User[name]`.
**isNested** | flags this form as rendered inside another form.
**previewMode** | renders the form read-only.
**horizontalMode** | renders labels beside fields instead of above them.
**sessionKey** | a session key used to bind deferred bindings (e.g. file uploads on unsaved records).

## Field properties

Every field supports these common properties, where applicable:

Property | Description
------------- | -------------
**label** | a name when displaying the form field to the user.
**type** | defines how this field should be rendered. Default: `text`.
**span** | aligns the form field to one side. Options: `auto`, `left`, `right`, `row`, `full`, `adaptive`. Default: `full`.
**spanClass** | used with the span `row` property to display the form as a grid, for example, `spanClass: col-4`.
**size** | specifies a field size for fields that use it, for example, the textarea field. Options: `tiny`, `small`, `large`, `huge`, `giant`.
**placeholder** | if the field supports a placeholder value.
**comment** | places a descriptive comment below the field.
**commentAbove** | places a comment above the field.
**commentHtml** | allow HTML markup inside the comment. Options: `true`, `false`.
**default** | specify the default value for the field. For `dropdown`, `checkboxlist`, `radio` and `balloon-selector` fields, you may specify an option key here to have it selected by default.
**defaultFrom** | takes the default value from the value of another model attribute.
**tab** | assigns the field to a tab.
**cssClass** | assigns a CSS class to the field container.
**autoFocus** | flags the field to be focused when the form loads. Default: `false`.
**readOnly** | prevents the field from being modified. Options: `true`, `false`.
**disabled** | prevents the field from being modified and excludes it from the saved data. Options: `true`, `false`.
**hidden** | hides the field from the view and excludes it from the saved data. Options: `true`, `false`.
**stretch** | specifies if this field stretches to fit the parent height.
**context** | specifies what context should be used when displaying the field. Context can also be passed by using an `@` symbol in the field name, for example, `name@update`.
**dependsOn** | an array of other field names this field depends on; when the other fields are modified, this field will update.
**changeHandler** | the name of an AJAX handler to call when the field value is changed, optional.
**trigger** | specify conditions for this field using trigger events.
**required** | places an asterisk next to the field label to indicate it is required. Enforce it with validation on the model — the form does not enforce it.
**attributes** | specify custom HTML attributes to add to the form field element.
**containerAttributes** | specify custom HTML attributes to add to the form field container.
**order** | a numerical weight when determining the display order, default value increments at 100 points per field.

## Tabs

Assign fields to tabs by defining them under `tabs` (or `secondaryTabs`) and naming the tab on each
field:

```yaml
tabs:
    fields:
        username:
            type: text
            label: Username
            tab: Account

        password:
            type: password
            label: Password
            tab: Account

        bio:
            type: textarea
            label: Bio
            tab: Profile
```

Tab definitions also accept these properties:

Property | Description
------------- | -------------
**defaultTab** | the default tab to assign fields to. Default: Misc.
**activeTab** | selected tab when the form first loads, name or index. Default: `1`.
**icons** | assign icons to tabs using tab names as the key.
**lazy** | array of tabs to be loaded dynamically when clicked. Useful for tabs with heavy content.
**stretch** | specifies if this tab stretches to fit the parent height.

## Field types

All fields are identified by their **type** property; `text` is the default.

**text** — single line text box.

**number** — single line text box with number validation.

**password** — single line password box.

**email** — single line text box with email validation.

**textarea** — multiline text box. Sizeable via `size`.

**dropdown** — a select box. Supply the options inline, or omit them to source from the model
(see [field options](#field-options) below):

```yaml
status:
    label: Status
    type: dropdown
    options:
        draft: Draft
        published: Published
```

**radio** — a list of radio options, one selectable. Options may carry descriptions:

```yaml
security_level:
    label: Access
    type: radio
    options:
        all: [All, Guests and members]
        registered: [Registered only, Members only]
```

**checkbox** — a single checkbox.

**checkboxlist** — a list of checkboxes, multiple selectable.

**switch** — a switchable toggle.

**balloon-selector** — a group of pill buttons, one selectable.

### Form UI

Layout elements that render inside the form without binding data:

**section** — a heading and subheading:

```yaml
_section1:
    label: User details
    comment: The information for this person
    type: section
```

**hint** — like a section, rendered inside a dismissible callout. Set `mode` to `tip`, `info`,
`success`, `warning` or `danger`.

**ruler** — a horizontal divider:

```yaml
_ruler1:
    type: ruler
```

**partial** — renders a custom view; receives `$field`, `$formModel` and `$value`:

```yaml
content:
    type: partial
    path: ~/resources/views/users/_content_field.php
```

### Form widgets

Richer fields with their own assets and AJAX handlers. Amber currently ships two:

**relation** — displays a dropdown (singular relations) or checkbox list (multiple relations) sourced
from a model relationship, with optional inline quick-create:

```yaml
groups:
    label: Groups
    type: relation
    nameFrom: name
```

**fileupload** — file/image uploader bound to a file attachment:

```yaml
avatar:
    label: Avatar
    type: fileupload
    mode: image
    imageHeight: 260
    imageWidth: 260
```

Relation fields work with both October Rain and plain Eloquent models — see
[Using Eloquent models](eloquent.md).

::: warning
File upload fields require an October Rain model, as they are built on Rain's file attachment
subsystem — see [Using Eloquent models](eloquent.md).
:::

## Field options

Option-based fields (`dropdown`, `radio`, `checkboxlist`, `balloon-selector`) resolve their options in
this order:

1. An inline `options` array in the YAML, as shown above.
2. An `options` method on the model. Omit the property entirely and Amber calls
   `get<Field>Options()`, falling back to `getDropdownOptions()`:

```php
public function getStatusOptions()
{
    return ['draft' => 'Draft', 'published' => 'Published'];
}
```

3. An explicit static or instance method named by the property:

```yaml
status:
    type: dropdown
    options: listStatuses
```

### Preset options

Amber ships preset option lists for common cases, referenced with the `preset:` prefix:

```yaml
timezone:
    label: Timezone
    type: dropdown
    options: preset:timezones
```

Available presets: `timezones`, `locales`, `flags`, `flags@short`, `icons`, `icons@phosphor`,
`icons@bootstrap`. Applications can register or extend presets through the preset manager:

```php
use October\Amber\Classes\PresetManager;

PresetManager::instance()->registerPreset('priorities', function () {
    return ['low' => 'Low', 'high' => 'High'];
});
```

## Contexts

Fields can be limited to a context, so one YAML file serves both create and update forms:

```yaml
password:
    label: Password
    type: password
    context: create
```

Pass the active context when building the widget (`'context' => 'update'`), or append it to the field
name with `@` for per-context field variants (`password@create`).

## Extending the form

Subclass the widget and override its extension points, declared in `HasFormEvents`:

```php
use October\Amber\Widgets\Form;
use October\Rain\Element\ElementHolder;

class UserForm extends Form
{
    protected function eventExtendFieldsBefore(): void
    {
        // Add a field before configuration is parsed
        $this->addFormField('birthday', 'Birthday')->displayAs('text');
    }

    protected function eventExtendFields(ElementHolder $fields): void
    {
        // Modify defined fields
        $fields->email->hidden = true;
    }
}
```

Available overrides: `eventExtendFieldsBefore`, `eventExtendFields`, `eventBeforeRefresh`,
`eventRefreshFields`, `eventRefresh`.

Models can also filter their own form fields by defining a `filterFields` method, called whenever the
form renders or refreshes:

```php
public function filterFields($fields, $context = null)
{
    $fields->email->hidden = !$this->is_registered;
}
```

## Next steps

- [Lists](lists.md) — the record list that links to this form
- [UI Elements](ui.md) — the `Ui::ajaxButton` used to submit the form
- [Using Eloquent models](eloquent.md) — driving Amber from plain `Illuminate` models
