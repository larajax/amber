const sharedSidebar = [
    {
        text: "Introduction",
        items: [
            { text: 'Getting Started', link: '/' },
        ]
    }
];

export default {
    title: 'Amber',
    description: 'Form, List and UI tools for Laravel',
    head: [
        ['link', { rel: 'icon', href: '/favicon.svg' }]
    ],
    themeConfig: {
        nav: [
            { text: 'Guide', link: '/' },
            { text: 'GitHub', link: 'https://github.com/larajax/amber' }
        ],
        sidebar: {
            '/': sharedSidebar
        },
        socialLinks: [
            { icon: 'github', link: 'https://github.com/larajax/amber' }
        ],
        search: { provider: 'local' },
        outline: [2, 3] // h2 and h3
    }
}
