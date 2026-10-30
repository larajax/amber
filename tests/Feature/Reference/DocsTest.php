<?php

namespace Tests\Feature\Reference;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * DocsTest keeps the docs site and the reference pages in step: one docs page per reference page, one docs anchor per example, and code samples imported from the files the examples run.
 */
class DocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_reference_page_has_a_docs_page_that_points_back_to_it(): void
    {
        $missing = [];

        foreach ($this->referencePages() as [$group, $page]) {
            $markdown = $this->docsPage($group, $page);

            if ($markdown === null || !str_contains($markdown, "demo: /reference/{$group}/{$page}\n")) {
                $missing[] = "docs/{$group}/{$page}.md";
            }
        }

        $this->assertSame([], $missing, 'These docs pages are missing, or lack their demo frontmatter: '.implode(', ', $missing));
    }

    public function test_every_reference_page_is_in_the_docs_sidebar(): void
    {
        $config = File::get(base_path('docs/.vitepress/config.mjs'));
        $missing = [];

        foreach ($this->referencePages() as [$group, $page]) {
            if (!str_contains($config, "link: '/{$group}/{$page}'")) {
                $missing[] = "/{$group}/{$page}";
            }
        }

        $this->assertSame([], $missing, 'These pages are not in the docs sidebar: '.implode(', ', $missing));
    }

    public function test_every_example_section_and_docs_link_has_its_anchor_in_the_docs(): void
    {
        $docsUrl = preg_quote(config('reference.docs_url'), '/');
        $missing = [];

        foreach ($this->referencePages() as [$group, $page]) {
            $html = $this->get(route("reference.{$group}.{$page}"))->assertOk()->getContent();
            $markdown = $this->docsPage($group, $page) ?? '';

            preg_match_all('/<section class="ui-section" id="([^"]+)"/', $html, $sections);
            preg_match_all('/href="'.$docsUrl.'\/'.preg_quote("{$group}/{$page}", '/').'#([^"]+)"/', $html, $links);

            foreach (array_unique([...$sections[1], ...$links[1]]) as $anchor) {
                if (!str_contains($markdown, "{#{$anchor}}")) {
                    $missing[] = "{$group}/{$page}#{$anchor}";
                }
            }

            if (count($sections[1]) !== count($links[1])) {
                $missing[] = "{$group}/{$page} (a section without a reference._heading docs link)";
            }
        }

        $this->assertSame([], $missing, 'These examples have no docs heading with their anchor: '.implode(', ', $missing));
    }

    public function test_every_snippet_import_in_the_docs_points_to_an_existing_file_and_region(): void
    {
        $broken = [];

        foreach ($this->docsFiles() as $file) {
            preg_match_all('/^<<< @\/(\S+?)(?:#([\w-]+))?(?:\{[^}]*\})?\s*$/m', File::get($file), $imports, PREG_SET_ORDER);

            foreach ($imports as $import) {
                $path = base_path('docs/'.$import[1]);
                $region = $import[2] ?? '';

                if (!is_file($path) || ($region !== '' && !str_contains(File::get($path), "#region {$region}"))) {
                    $broken[] = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file).': '.$import[0];
                }
            }
        }

        $this->assertSame([], $broken, 'These snippet imports point nowhere: '.implode(', ', $broken));
    }

    public function test_every_example_file_is_imported_by_the_docs(): void
    {
        $missing = [];

        foreach ($this->referencePages() as [$group, $page]) {
            $markdown = $this->docsPage($group, $page) ?? '';
            $folder = resource_path("ui/reference/{$group}/{$page}");

            foreach (is_dir($folder) ? File::allFiles($folder) : [] as $file) {
                $path = 'resources/ui/reference/'.str_replace('\\', '/', "{$group}/{$page}/".$file->getRelativePathname());

                if (!str_contains($markdown, $path)) {
                    $missing[] = $path;
                }
            }
        }

        foreach (array_keys(config('reference.groups')) as $group) {
            $folder = resource_path("views/reference/{$group}/examples");
            $groupMarkdown = implode("\n", array_map(fn ($file) => File::get($file), File::glob(base_path("docs/{$group}/*.md"))));

            foreach (is_dir($folder) ? File::allFiles($folder) : [] as $file) {
                $path = 'resources/views/reference/'.str_replace('\\', '/', "{$group}/examples/".$file->getRelativePathname());

                if (!str_contains($groupMarkdown, $path)) {
                    $missing[] = $path;
                }
            }
        }

        $this->assertSame([], $missing, 'These example files are not imported by the docs: '.implode(', ', $missing));
    }

    /**
     * referencePages returns each page in the reference registry as a [group, page] pair.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    protected function referencePages(): array
    {
        $pages = [];

        foreach (config('reference.groups') as $group => $definition) {
            foreach (array_keys($definition['pages']) as $page) {
                $pages[] = [$group, $page];
            }
        }

        return $pages;
    }

    /**
     * docsPage returns the markdown of a reference page's docs page, or null when it does not exist.
     */
    protected function docsPage(string $group, string $page): ?string
    {
        $path = base_path("docs/{$group}/{$page}.md");

        return is_file($path) ? File::get($path) : null;
    }

    /**
     * docsFiles returns every markdown file of the docs site.
     *
     * @return array<int, string>
     */
    protected function docsFiles(): array
    {
        $files = [];

        foreach (File::allFiles(base_path('docs'), true) as $file) {
            $path = $file->getPathname();

            if ($file->getExtension() === 'md' && !str_contains($path, 'node_modules') && !str_contains($path, '.vitepress')) {
                $files[] = $path;
            }
        }

        return $files;
    }
}
