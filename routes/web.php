<?php

use App\Controllers\CategoriesController;
use App\Controllers\KitchenSinkController;
use App\Controllers\PostsController;
use App\Controllers\UiElementsController;
use App\Controllers\UserGroupsController;
use App\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('users'));

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
|
| A full CRUD resource demonstrating the List, Filter, Toolbar, Form and
| ListStructure widgets. Routes are declared with match(['get','post']) so the
| same URL serves the initial page (GET) and the widget AJAX handlers (POST).
|
*/
Route::match(['get', 'post'], '/users', [UsersController::class, 'index'])->name('users.index');
Route::match(['get', 'post'], '/users/structure', [UsersController::class, 'structure'])->name('users.structure');
Route::match(['get', 'post'], '/users/create', [UsersController::class, 'create'])->name('users.create');
Route::match(['get', 'post'], '/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');

/*
|--------------------------------------------------------------------------
| User Groups
|--------------------------------------------------------------------------
|
| A second, simpler resource (list + form, no filter) showing the widgets are
| configuration-driven and not tied to any one model.
|
*/
Route::match(['get', 'post'], '/user-groups', [UserGroupsController::class, 'index'])->name('user-groups.index');
Route::match(['get', 'post'], '/user-groups/create', [UserGroupsController::class, 'create'])->name('user-groups.create');
Route::match(['get', 'post'], '/user-groups/{userGroup}/edit', [UserGroupsController::class, 'edit'])->name('user-groups.edit');

/*
|--------------------------------------------------------------------------
| Kitchen Sink
|--------------------------------------------------------------------------
|
| A Sample resource whose form shows every form field type, form widget and
| form option side by side, sorted into tabs, along with a custom app form
| widget that loads its own script and stylesheet.
|
*/
Route::match(['get', 'post'], '/kitchen-sink', [KitchenSinkController::class, 'index'])->name('kitchen-sink.index');
Route::match(['get', 'post'], '/kitchen-sink/create', [KitchenSinkController::class, 'create'])->name('kitchen-sink.create');
Route::match(['get', 'post'], '/kitchen-sink/{sample}/edit', [KitchenSinkController::class, 'edit'])->name('kitchen-sink.edit');

/*
|--------------------------------------------------------------------------
| Posts
|--------------------------------------------------------------------------
|
| A blog post resource on October Rain models covering every list column
| type, the advanced filter scopes, a toolbar with a popup button, a hotkey
| and a bulk actions dropdown, and a form with secondary tabs and triggers.
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
| UI Elements
|--------------------------------------------------------------------------
|
| The Ui factory and the controls on their own, with no model behind them:
| buttons, callouts, tabs, loading states and the controls driven by data
| attributes. Each example shows the Blade snippet that renders it.
|
*/
Route::match(['get', 'post'], '/ui', [UiElementsController::class, 'buttons'])->name('ui.buttons');
Route::match(['get', 'post'], '/ui/feedback', [UiElementsController::class, 'feedback'])->name('ui.feedback');
Route::match(['get', 'post'], '/ui/controls', [UiElementsController::class, 'controls'])->name('ui.controls');
