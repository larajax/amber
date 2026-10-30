<?php

use App\Controllers\AuthorsController;
use App\Controllers\CategoriesController;
use App\Controllers\GroupsController;
use App\Controllers\PostsController;
use App\Controllers\Reference\ExtendingController;
use App\Controllers\Reference\FiltersController;
use App\Controllers\Reference\FormsController;
use App\Controllers\Reference\FormWidgetsController;
use App\Controllers\Reference\IndexController;
use App\Controllers\Reference\ListsController;
use App\Controllers\Reference\UiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('reference'));

/*
|--------------------------------------------------------------------------
| Posts
|--------------------------------------------------------------------------
|
| The blog posts on October Rain models: a filtered list with bulk actions
| and a quick post popup, and a form that links each post to its category,
| tags and author. These routes are declared with match(['get','post']) so
| the same URL serves the page (GET) and the widget AJAX handlers (POST).
|
*/
Route::match(['get', 'post'], '/posts', [PostsController::class, 'index'])->name('posts.index');
Route::match(['get', 'post'], '/posts/create', [PostsController::class, 'create'])->name('posts.create');
Route::match(['get', 'post'], '/posts/{post}/edit', [PostsController::class, 'edit'])->name('posts.edit');

/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
|
| Post categories on a nested set tree, shown by the ListStructure widget as
| an expandable tree that can be nested and reordered by dragging.
|
*/
Route::match(['get', 'post'], '/categories', [CategoriesController::class, 'index'])->name('categories.index');
Route::match(['get', 'post'], '/categories/create', [CategoriesController::class, 'create'])->name('categories.create');
Route::match(['get', 'post'], '/categories/{category}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');

/*
|--------------------------------------------------------------------------
| Authors
|--------------------------------------------------------------------------
|
| The users who write the posts, on a plain Eloquent model: a filtered list
| with bulk delete and a form page that validates in the controller.
|
*/
Route::match(['get', 'post'], '/authors', [AuthorsController::class, 'index'])->name('authors.index');
Route::match(['get', 'post'], '/authors/create', [AuthorsController::class, 'create'])->name('authors.create');
Route::match(['get', 'post'], '/authors/{user}/edit', [AuthorsController::class, 'edit'])->name('authors.edit');

/*
|--------------------------------------------------------------------------
| Groups
|--------------------------------------------------------------------------
|
| The groups authors belong to, where clicking a row in the list edits the
| group in a popup and saving it refreshes the list.
|
*/
Route::match(['get', 'post'], '/groups', [GroupsController::class, 'index'])->name('groups.index');
Route::match(['get', 'post'], '/groups/create', [GroupsController::class, 'create'])->name('groups.create');
Route::match(['get', 'post'], '/groups/{userGroup}/edit', [GroupsController::class, 'edit'])->name('groups.edit');

/*
|--------------------------------------------------------------------------
| Reference
|--------------------------------------------------------------------------
|
| One page per part of the library, listed in config/reference.php. Each
| route is named reference.{group}.{page}, which the sidebar, the page
| navigation and the coverage test read.
|
*/
Route::get('/reference', [IndexController::class, 'index'])->name('reference.index');

/*
|--------------------------------------------------------------------------
| Reference: Lists
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/reference/lists/columns', [ListsController::class, 'columns'])->name('reference.lists.columns');
Route::match(['get', 'post'], '/reference/lists/options', [ListsController::class, 'options'])->name('reference.lists.options');
Route::match(['get', 'post'], '/reference/lists/list-structure', [ListsController::class, 'listStructure'])->name('reference.lists.list-structure');
Route::match(['get', 'post'], '/reference/lists/toolbar', [ListsController::class, 'toolbar'])->name('reference.lists.toolbar');
Route::match(['get', 'post'], '/reference/lists/search', [ListsController::class, 'search'])->name('reference.lists.search');

/*
|--------------------------------------------------------------------------
| Reference: Filters
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/reference/filters/scopes', [FiltersController::class, 'scopes'])->name('reference.filters.scopes');
Route::match(['get', 'post'], '/reference/filters/options', [FiltersController::class, 'options'])->name('reference.filters.options');

/*
|--------------------------------------------------------------------------
| Reference: Forms
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/reference/forms/fields', [FormsController::class, 'fields'])->name('reference.forms.fields');
Route::match(['get', 'post'], '/reference/forms/layout', [FormsController::class, 'layout'])->name('reference.forms.layout');
Route::match(['get', 'post'], '/reference/forms/behaviour', [FormsController::class, 'behaviour'])->name('reference.forms.behaviour');

/*
|--------------------------------------------------------------------------
| Reference: Form Widgets
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/reference/form-widgets/code-editor', [FormWidgetsController::class, 'codeEditor'])->name('reference.form-widgets.code-editor');
Route::match(['get', 'post'], '/reference/form-widgets/color-picker', [FormWidgetsController::class, 'colorPicker'])->name('reference.form-widgets.color-picker');
Route::match(['get', 'post'], '/reference/form-widgets/data-table', [FormWidgetsController::class, 'dataTable'])->name('reference.form-widgets.data-table');
Route::match(['get', 'post'], '/reference/form-widgets/date-picker', [FormWidgetsController::class, 'datePicker'])->name('reference.form-widgets.date-picker');
Route::match(['get', 'post'], '/reference/form-widgets/file-upload', [FormWidgetsController::class, 'fileUpload'])->name('reference.form-widgets.file-upload');
Route::match(['get', 'post'], '/reference/form-widgets/markdown-editor', [FormWidgetsController::class, 'markdownEditor'])->name('reference.form-widgets.markdown-editor');
Route::match(['get', 'post'], '/reference/form-widgets/nested-form', [FormWidgetsController::class, 'nestedForm'])->name('reference.form-widgets.nested-form');
Route::match(['get', 'post'], '/reference/form-widgets/record-finder', [FormWidgetsController::class, 'recordFinder'])->name('reference.form-widgets.record-finder');
Route::match(['get', 'post'], '/reference/form-widgets/relation', [FormWidgetsController::class, 'relation'])->name('reference.form-widgets.relation');
Route::match(['get', 'post'], '/reference/form-widgets/repeater', [FormWidgetsController::class, 'repeater'])->name('reference.form-widgets.repeater');
Route::match(['get', 'post'], '/reference/form-widgets/rich-editor', [FormWidgetsController::class, 'richEditor'])->name('reference.form-widgets.rich-editor');
Route::match(['get', 'post'], '/reference/form-widgets/sensitive', [FormWidgetsController::class, 'sensitive'])->name('reference.form-widgets.sensitive');
Route::match(['get', 'post'], '/reference/form-widgets/tag-list', [FormWidgetsController::class, 'tagList'])->name('reference.form-widgets.tag-list');

/*
|--------------------------------------------------------------------------
| Reference: UI
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/reference/ui/buttons', [UiController::class, 'buttons'])->name('reference.ui.buttons');
Route::match(['get', 'post'], '/reference/ui/dropdowns', [UiController::class, 'dropdowns'])->name('reference.ui.dropdowns');
Route::match(['get', 'post'], '/reference/ui/popups', [UiController::class, 'popups'])->name('reference.ui.popups');
Route::match(['get', 'post'], '/reference/ui/tabs', [UiController::class, 'tabs'])->name('reference.ui.tabs');
Route::match(['get', 'post'], '/reference/ui/feedback', [UiController::class, 'feedback'])->name('reference.ui.feedback');
Route::match(['get', 'post'], '/reference/ui/inputs', [UiController::class, 'inputs'])->name('reference.ui.inputs');

/*
|--------------------------------------------------------------------------
| Reference: Extending
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/reference/extending/custom-widgets', [ExtendingController::class, 'customWidgets'])->name('reference.extending.custom-widgets');
Route::match(['get', 'post'], '/reference/extending/icons', [ExtendingController::class, 'icons'])->name('reference.extending.icons');
Route::match(['get', 'post'], '/reference/extending/seams', [ExtendingController::class, 'seams'])->name('reference.extending.seams');
