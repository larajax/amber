<?php

namespace App\Controllers\Reference\ExtendingController;

use Illuminate\Support\Facades\Route;

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
        $ui = app('larajax.ui');

        $ui->resolveUrlUsing(function (string $path): string {
            return route('reference.extending.seams', ['path' => $path]).'#widget-paths';
        });

        $ui->resolveLinkUsing('reference', function (string $link): ?string {
            $routeName = 'reference.'.str_replace('/', '.', substr($link, strlen('reference://')));

            return Route::has($routeName) ? route($routeName) : null;
        });

        $ui->resolveTimezoneUsing(function (): ?string {
            return request()->query('timezone');
        });
    }

    /**
     * forgetSeamResolvers removes the resolvers when the request ends, so they never reach another page.
     */
    protected function forgetSeamResolvers(): void
    {
        $ui = app('larajax.ui');

        $ui->resolveUrlUsing(null);
        $ui->resolveLinkUsing('reference', null);
        $ui->resolveTimezoneUsing(null);
    }
}
