<?php

namespace App\Controllers\Reference\ExtendingController;

use Illuminate\Support\Facades\Route;
use Larajax\Ui\Facades\Ui;

/**
 * SeamResolvers sets the resolvers the URLs and Timezones page demonstrates, which an app sets once in a service provider instead.
 */
trait SeamResolvers
{
    /**
     * registerSeamResolvers sends widget paths back to this page, resolves reference:// links to the page they name, and reads the viewer timezone from the address.
     */
    protected function registerSeamResolvers(): void
    {
        Ui::resolveUrlUsing(function (string $path): string {
            return route('reference.extending.seams', ['path' => $path]).'#widget-paths';
        });

        Ui::resolveLinkUsing('reference', function (string $link): ?string {
            $routeName = 'reference.'.str_replace('/', '.', substr($link, strlen('reference://')));

            return Route::has($routeName) ? route($routeName) : null;
        });

        Ui::resolveTimezoneUsing(function (): ?string {
            return request()->query('timezone');
        });
    }

    /**
     * forgetSeamResolvers removes the resolvers when the request ends, so they never reach another page.
     */
    protected function forgetSeamResolvers(): void
    {
        Ui::resolveUrlUsing(null);
        Ui::resolveLinkUsing('reference', null);
        Ui::resolveTimezoneUsing(null);
    }
}
