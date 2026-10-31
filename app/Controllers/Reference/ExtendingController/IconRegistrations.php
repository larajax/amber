<?php

namespace App\Controllers\Reference\ExtendingController;

use Larajax\Ui\Classes\Icons\PhosphorIconPack;
use Larajax\Ui\Facades\Ui;

/**
 * IconRegistrations changes some of the icons the library draws for the Icons page, which an app does once in a service provider instead.
 */
trait IconRegistrations
{
    /**
     * registerExampleIcons registers an amber pack on top of the phosphor pack, then overrides two keys the widgets use in every pack.
     */
    protected function registerExampleIcons(): void
    {
        Ui::registerIconPack('amber', function (): array {
            return array_replace((new PhosphorIconPack)->icons(), [
                'action.delete' => 'ph ph-trash-simple',
            ]);
        });

        Ui::extendIcons([
            'callout.info' => 'ph ph-lightbulb',
            'search.icon' => 'ph ph-binoculars',
        ]);

        Ui::extendIcons(function (array $icons, string $pack): array {
            if ($pack !== 'bootstrap') {
                return $icons;
            }

            return array_replace($icons, [
                'callout.info' => 'bi bi-lightbulb',
                'search.icon' => 'bi bi-binoculars',
            ]);
        });
    }
}
