// The reference groups mirror the demo app's Reference section (config/reference.php), page for page
const sharedSidebar = [
    {
        text: 'Introduction',
        items: [
            { text: 'Getting Started', link: '/getting-started' },
            { text: 'Installation', link: '/installation' }
        ]
    },
    {
        text: 'Guides',
        items: [
            { text: 'Building a Form Page', link: '/guides/forms' },
            { text: 'Building a List Page', link: '/guides/lists' },
            { text: 'Models', link: '/guides/models' },
            { text: 'Widgets and Controllers', link: '/guides/widgets' }
        ]
    },
    {
        text: 'Lists',
        collapsed: false,
        items: [
            { text: 'Columns', link: '/lists/columns' },
            { text: 'List Options', link: '/lists/options' },
            { text: 'List Structure', link: '/lists/list-structure' },
            { text: 'Toolbar', link: '/lists/toolbar' },
            { text: 'Search', link: '/lists/search' }
        ]
    },
    {
        text: 'Filters',
        collapsed: false,
        items: [
            { text: 'Scope Types', link: '/filters/scopes' },
            { text: 'Filter Options', link: '/filters/options' }
        ]
    },
    {
        text: 'Forms',
        collapsed: false,
        items: [
            { text: 'Field Types', link: '/forms/fields' },
            { text: 'Layout', link: '/forms/layout' },
            { text: 'Behaviour', link: '/forms/behaviour' }
        ]
    },
    {
        text: 'Form Widgets',
        collapsed: false,
        items: [
            { text: 'Code Editor', link: '/form-widgets/code-editor' },
            { text: 'Color Picker', link: '/form-widgets/color-picker' },
            { text: 'Data Table', link: '/form-widgets/data-table' },
            { text: 'Date Picker', link: '/form-widgets/date-picker' },
            { text: 'File Upload', link: '/form-widgets/file-upload' },
            { text: 'Markdown Editor', link: '/form-widgets/markdown-editor' },
            { text: 'Nested Form', link: '/form-widgets/nested-form' },
            { text: 'Record Finder', link: '/form-widgets/record-finder' },
            { text: 'Relation', link: '/form-widgets/relation' },
            { text: 'Repeater', link: '/form-widgets/repeater' },
            { text: 'Rich Editor', link: '/form-widgets/rich-editor' },
            { text: 'Sensitive', link: '/form-widgets/sensitive' },
            { text: 'Tag List', link: '/form-widgets/tag-list' }
        ]
    },
    {
        text: 'UI',
        collapsed: false,
        items: [
            { text: 'Buttons', link: '/ui/buttons' },
            { text: 'Dropdowns', link: '/ui/dropdowns' },
            { text: 'Popups', link: '/ui/popups' },
            { text: 'Tabs', link: '/ui/tabs' },
            { text: 'Feedback', link: '/ui/feedback' },
            { text: 'Inputs', link: '/ui/inputs' }
        ]
    },
    {
        text: 'Extending',
        collapsed: false,
        items: [
            { text: 'Custom Widgets', link: '/extending/custom-widgets' },
            { text: 'Icons', link: '/extending/icons' },
            { text: 'URLs and Timezones', link: '/extending/seams' }
        ]
    }
];

export default {
    title: 'Larajax UI',
    description: 'Form, List and UI tools for Laravel',
    srcExclude: ['README.md'],
    head: [
        ['link', { rel: 'icon', href: '/favicon.svg' }]
    ],
    themeConfig: {
        nav: [
            { text: 'Guide', link: '/getting-started' },
            { text: 'Demo App', link: 'https://github.com/larajax/amber' },
            { text: 'GitHub', link: 'https://github.com/larajax/ui' }
        ],
        sidebar: {
            '/': sharedSidebar
        },
        socialLinks: [
            { icon: 'github', link: 'https://github.com/larajax/ui' }
        ],
        search: { provider: 'local' },
        outline: [2, 3] // h2 and h3
    }
}
