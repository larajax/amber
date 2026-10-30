/*
 * Reference code blocks
 *
 * Data attributes:
 * - data-code-block - a collapsible source block (a details element), highlighted the first time it opens
 * - data-code-lang - the block's language for the highlighter: yaml, php, blade, javascript or css
 * - data-code-fragment - PHP without its opening tag, such as a single method
 * - data-code-toggle - opens or closes every block on the page and remembers the choice
 *
 * Highlighting uses Shiki from a CDN, loaded only once a block opens; without it the plain source stays readable.
 */

const SHIKI_URL = 'https://esm.sh/shiki@4.5.0/bundle/web';
const OPEN_KEY = 'amber-code-open';
const LANGS = ['yaml', 'php', 'blade', 'javascript', 'css'];

let highlighterPromise = null;

function loadHighlighter() {
    highlighterPromise ??= import(SHIKI_URL).then(({ createHighlighter }) => createHighlighter({
        themes: ['github-light', 'github-dark'],
        langs: LANGS
    }));

    return highlighterPromise;
}

async function highlight(block) {
    const pre = block.querySelector(':scope > pre');
    const lang = block.dataset.codeLang;

    if (!pre || block.dataset.codeHighlighted !== undefined || !LANGS.includes(lang)) {
        return;
    }

    block.dataset.codeHighlighted = '';

    try {
        const highlighter = await loadHighlighter();
        const isFragment = block.dataset.codeFragment !== undefined;
        const code = isFragment ? `<?php\n${pre.textContent}` : pre.textContent;

        const template = document.createElement('template');
        template.innerHTML = highlighter.codeToHtml(code, {
            lang,
            themes: { light: 'github-light', dark: 'github-dark' },
            defaultColor: false
        });

        const highlighted = template.content.firstElementChild;

        // The opening tag only puts the grammar in PHP mode, so its line is dropped again
        if (isFragment) {
            highlighted.querySelector('.line')?.nextSibling?.remove();
            highlighted.querySelector('.line')?.remove();
        }

        pre.replaceWith(highlighted);
    }
    catch (error) {
        console.warn('Code highlighting is unavailable.', error);
    }
}

function getStoredOpen() {
    try {
        return localStorage.getItem(OPEN_KEY) === 'true';
    }
    catch (e) {
        return false;
    }
}

function storeOpen(isOpen) {
    try {
        localStorage.setItem(OPEN_KEY, isOpen ? 'true' : 'false');
    }
    catch (e) {
        // Storage can be unavailable, so the choice lasts for this page only
    }
}

function setAllOpen(isOpen) {
    document.querySelectorAll('[data-code-block]').forEach((block) => {
        block.open = isOpen;
    });

    document.querySelectorAll('[data-code-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', isOpen ? 'true' : 'false');
    });
}

// The toggle event does not bubble, so the listener captures it on the way down
document.addEventListener('toggle', (event) => {
    if (event.target.matches?.('[data-code-block]') && event.target.open) {
        highlight(event.target);
    }
}, true);

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-code-toggle]');
    if (!button) {
        return;
    }

    const isOpen = button.getAttribute('aria-pressed') !== 'true';
    setAllOpen(isOpen);
    storeOpen(isOpen);
});

setAllOpen(getStoredOpen());
