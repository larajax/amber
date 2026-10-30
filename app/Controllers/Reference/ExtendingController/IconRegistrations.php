<?php

namespace App\Controllers\Reference\ExtendingController;

use Larajax\Ui\Classes\Icons\PhosphorIconPack;
use Larajax\Ui\Facades\Ui;

/**
 * IconRegistrations adds the icon pack and icon keys the Icons page shows, which an app adds once in a service provider instead.
 */
trait IconRegistrations
{
    /**
     * registerExampleIcons registers the amber pack on top of the phosphor pack, then adds status keys to every pack.
     */
    protected function registerExampleIcons(): void
    {
        Ui::registerIconPack('amber', function (): array {
            return array_replace((new PhosphorIconPack)->icons(), [
                'action.delete' => 'ph ph-trash-simple',
                'callout.info' => 'ph ph-lightbulb',
                'search.icon' => 'ph ph-binoculars',
            ]);
        });

        Ui::extendIcons([
            'status.draft' => 'ph ph-note-pencil',
            'status.published' => 'ph ph-globe-simple',
        ]);

        Ui::extendIcons(function (array $icons, string $pack): array {
            if ($pack !== 'bootstrap') {
                return $icons;
            }

            return array_replace($icons, [
                'status.draft' => 'bi bi-pencil-square',
                'status.published' => 'bi bi-globe',
            ]);
        });
    }
}
