<?php

/*
|--------------------------------------------------------------------------
| Reference pages
|--------------------------------------------------------------------------
|
| The Reference section has one page per part of larajax/ui, grouped the way
| the library is organised. The sidebar, each group's page navigation and the
| overview are built from this list, and every page is served by the route
| named "reference.{group}.{page}".
|
| Each page lists the library units it demonstrates in "covers". The coverage
| test discovers every unit in the library (widgets, form widgets, filter
| widgets, field, column and scope types, controls, elements and Ui factory
| methods) and fails when one is not covered by a page, so a new library
| feature needs a page before the suite passes.
|
*/

return [

    /*
     * The documentation site, where docs/{group}/{page}.md explains each reference page and its
     * sections carry the same anchors as the page's examples.
     */
    'docs_url' => 'https://ui.larajax.org',

    'groups' => [

        'lists' => [
            'label' => 'Lists',
            'icon' => 'ph-list-bullets',
            'description' => 'The list widget, its column types and options, the list structure, the toolbar and the search widget.',
            'pages' => [
                'columns' => [
                    'label' => 'Columns',
                    'icon' => 'ph-columns',
                    'covers' => [
                        'widget:Lists',
                        'column:text', 'column:number', 'column:image', 'column:file', 'column:switch',
                        'column:summary', 'column:datetime', 'column:time', 'column:date', 'column:timesince',
                        'column:timetense', 'column:selectable', 'column:linkage', 'column:partial', 'column:colorpicker',
                    ],
                ],
                'options' => [
                    'label' => 'List Options',
                    'icon' => 'ph-sliders',
                    'covers' => ['control:rowlink', 'control:simplelist'],
                ],
                'list-structure' => [
                    'label' => 'List Structure',
                    'icon' => 'ph-tree-structure',
                    'covers' => ['widget:ListStructure'],
                ],
                'toolbar' => [
                    'label' => 'Toolbar',
                    'icon' => 'ph-toolbox',
                    'covers' => ['widget:Toolbar', 'control:toolbar', 'control:drag-scroll'],
                ],
                'search' => [
                    'label' => 'Search',
                    'icon' => 'ph-magnifying-glass',
                    'covers' => ['widget:Search', 'control:search-input', 'ui:searchInput'],
                ],
            ],
        ],

        'filters' => [
            'label' => 'Filters',
            'icon' => 'ph-funnel',
            'description' => 'The filter widget with every scope type, and the options that shape how scopes apply.',
            'pages' => [
                'scopes' => [
                    'label' => 'Scope Types',
                    'icon' => 'ph-funnel-simple',
                    'covers' => [
                        'widget:Filter',
                        'scope:checkbox', 'scope:switch', 'scope:dropdown',
                        'filterwidget:Group', 'filterwidget:Date', 'filterwidget:Text', 'filterwidget:Number',
                    ],
                ],
                'options' => [
                    'label' => 'Filter Options',
                    'icon' => 'ph-sliders',
                    'covers' => [],
                ],
            ],
        ],

        'forms' => [
            'label' => 'Forms',
            'icon' => 'ph-textbox',
            'description' => 'The form widget, every built-in field type, form layout and the behaviour options that link fields together.',
            'pages' => [
                'fields' => [
                    'label' => 'Field Types',
                    'icon' => 'ph-text-aa',
                    'covers' => [
                        'widget:Form',
                        'field:text', 'field:email', 'field:number', 'field:password', 'field:textarea',
                        'field:dropdown', 'field:radio', 'field:checkbox', 'field:checkboxlist', 'field:switch',
                        'field:balloon-selector', 'field:section', 'field:hint', 'field:ruler', 'field:partial',
                        'control:balloon-selector',
                    ],
                ],
                'layout' => [
                    'label' => 'Layout',
                    'icon' => 'ph-layout',
                    'covers' => [],
                ],
                'behaviour' => [
                    'label' => 'Behaviour',
                    'icon' => 'ph-lightning',
                    'covers' => ['control:input-preset', 'control:input-trigger'],
                ],
            ],
        ],

        'form-widgets' => [
            'label' => 'Form Widgets',
            'icon' => 'ph-puzzle-piece',
            'description' => 'One page per form widget, showing its options side by side with the YAML that configures them.',
            'pages' => [
                'code-editor' => [
                    'label' => 'Code Editor',
                    'icon' => 'ph-code',
                    'covers' => ['formwidget:CodeEditor'],
                ],
                'color-picker' => [
                    'label' => 'Color Picker',
                    'icon' => 'ph-palette',
                    'covers' => ['formwidget:ColorPicker'],
                ],
                'data-table' => [
                    'label' => 'Data Table',
                    'icon' => 'ph-table',
                    'covers' => ['formwidget:DataTable'],
                ],
                'date-picker' => [
                    'label' => 'Date Picker',
                    'icon' => 'ph-calendar-blank',
                    'covers' => ['formwidget:DatePicker', 'control:datepicker'],
                ],
                'file-upload' => [
                    'label' => 'File Upload',
                    'icon' => 'ph-upload-simple',
                    'covers' => ['formwidget:FileUpload'],
                ],
                'markdown-editor' => [
                    'label' => 'Markdown Editor',
                    'icon' => 'ph-markdown-logo',
                    'covers' => ['formwidget:MarkdownEditor'],
                ],
                'nested-form' => [
                    'label' => 'Nested Form',
                    'icon' => 'ph-stack',
                    'covers' => ['formwidget:NestedForm'],
                ],
                'record-finder' => [
                    'label' => 'Record Finder',
                    'icon' => 'ph-binoculars',
                    'covers' => ['formwidget:RecordFinder'],
                ],
                'relation' => [
                    'label' => 'Relation',
                    'icon' => 'ph-link',
                    'covers' => ['formwidget:Relation'],
                ],
                'repeater' => [
                    'label' => 'Repeater',
                    'icon' => 'ph-rows',
                    'covers' => ['formwidget:Repeater'],
                ],
                'rich-editor' => [
                    'label' => 'Rich Editor',
                    'icon' => 'ph-text-b',
                    'covers' => ['formwidget:RichEditor'],
                ],
                'sensitive' => [
                    'label' => 'Sensitive',
                    'icon' => 'ph-eye-slash',
                    'covers' => ['formwidget:Sensitive'],
                ],
                'tag-list' => [
                    'label' => 'Tag List',
                    'icon' => 'ph-tag',
                    'covers' => ['formwidget:TagList'],
                ],
            ],
        ],

        'ui' => [
            'label' => 'UI',
            'icon' => 'ph-squares-four',
            'description' => 'The Ui factory and the controls on their own, with no model behind them. Each example shows the Blade that renders it.',
            'pages' => [
                'buttons' => [
                    'label' => 'Buttons',
                    'icon' => 'ph-cursor-click',
                    'covers' => ['element:buttons', 'ui:button', 'ui:ajaxButton', 'ui:iconButton'],
                ],
                'dropdowns' => [
                    'label' => 'Dropdowns',
                    'icon' => 'ph-caret-circle-down',
                    'covers' => ['control:dropdown', 'ui:dropdownButton', 'ui:dropdownItem', 'ui:dropdownDivider'],
                ],
                'popups' => [
                    'label' => 'Popups',
                    'icon' => 'ph-app-window',
                    'covers' => ['control:popup', 'control:popover', 'control:tooltip', 'ui:popupButton'],
                ],
                'tabs' => [
                    'label' => 'Tabs',
                    'icon' => 'ph-tabs',
                    'covers' => ['control:tab'],
                ],
                'feedback' => [
                    'label' => 'Feedback',
                    'icon' => 'ph-chat-centered-text',
                    'covers' => [
                        'element:callout', 'element:scoreboard', 'ui:callout', 'ui:slot', 'ui:end',
                        'control:load-indicator', 'control:loader-container', 'control:change-monitor',
                    ],
                ],
                'inputs' => [
                    'label' => 'Inputs',
                    'icon' => 'ph-cursor-text',
                    'covers' => ['control:custom-select', 'control:checkbox', 'control:input-hotkey'],
                ],
            ],
        ],

        'extending' => [
            'label' => 'Extending',
            'icon' => 'ph-plugs-connected',
            'description' => 'Building your own form widget with its own assets, and the seams that adapt the library to your application.',
            'pages' => [
                'custom-widgets' => [
                    'label' => 'Custom Widgets',
                    'icon' => 'ph-puzzle-piece',
                    'covers' => ['ui:renderAssets'],
                ],
                'icons' => [
                    'label' => 'Icons',
                    'icon' => 'ph-shapes',
                    'covers' => ['ui:icon', 'ui:iconClass', 'ui:registerIconPack', 'ui:extendIcons'],
                ],
                'seams' => [
                    'label' => 'URLs and Timezones',
                    'icon' => 'ph-plugs',
                    'covers' => ['ui:url', 'ui:resolveUrlUsing', 'ui:resolveLinkUsing', 'ui:timezone', 'ui:resolveTimezoneUsing'],
                ],
            ],
        ],

    ],

];
