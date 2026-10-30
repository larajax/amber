<?php

namespace Tests\Feature\Reference;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Larajax\Ui\Classes\UiFactory;
use Larajax\Ui\UiServiceProvider;
use Larajax\Ui\Widgets\Lists\HasValueProcessor;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

/**
 * CoverageTest checks that every unit of larajax/ui has a reference page, using the "covers" lists in config/reference.php.
 */
class CoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_library_unit_is_covered_by_a_reference_page(): void
    {
        $missing = array_values(array_diff($this->libraryUnits(), $this->coveredUnits()));

        $this->assertSame([], $missing, 'These library units have no reference page: '.implode(', ', $missing));
    }

    public function test_every_covered_unit_exists_in_the_library(): void
    {
        $unknown = array_values(array_diff($this->coveredUnits(), $this->libraryUnits()));

        $this->assertSame([], $unknown, 'These covered units are not in the library: '.implode(', ', $unknown));
    }

    public function test_every_reference_page_has_its_named_route(): void
    {
        foreach ($this->referencePages() as $routeName) {
            $this->assertTrue(Route::has($routeName), "The route {$routeName} is not registered.");
        }
    }

    public function test_every_reference_page_renders(): void
    {
        $this->get(route('reference.index'))->assertOk();

        foreach ($this->referencePages() as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_the_home_page_redirects_to_the_reference_overview(): void
    {
        $this->get('/')->assertRedirect('reference');
    }

    /**
     * referencePages returns the route name of every page in the reference registry.
     *
     * @return array<int, string>
     */
    protected function referencePages(): array
    {
        $routeNames = [];

        foreach (config('reference.groups') as $groupCode => $group) {
            foreach (array_keys($group['pages']) as $pageCode) {
                $routeNames[] = "reference.{$groupCode}.{$pageCode}";
            }
        }

        return $routeNames;
    }

    /**
     * coveredUnits returns every unit a reference page claims to demonstrate.
     *
     * @return array<int, string>
     */
    protected function coveredUnits(): array
    {
        $units = [];

        foreach (config('reference.groups') as $group) {
            foreach ($group['pages'] as $page) {
                array_push($units, ...$page['covers']);
            }
        }

        return array_values(array_unique($units));
    }

    /**
     * libraryUnits discovers every widget, form and filter widget, field, column and scope type, control, element and Ui factory method in the library.
     *
     * @return array<int, string>
     */
    protected function libraryUnits(): array
    {
        $root = dirname((new ReflectionClass(UiServiceProvider::class))->getFileName(), 2);

        $units = [
            ...$this->prefixed('widget', $this->baseNames("{$root}/src/Widgets/*.php")),
            ...$this->prefixed('formwidget', $this->baseNames("{$root}/src/FormWidgets/*.php")),
            ...$this->prefixed('filterwidget', $this->baseNames("{$root}/src/FilterWidgets/*.php")),
            ...$this->prefixed('field', $this->partialTypes("{$root}/src/Widgets/Form/partials/_field_*.php", '_field_')),
            ...$this->prefixed('scope', $this->partialTypes("{$root}/src/Widgets/Filter/partials/_scope_*.php", '_scope_')),
            ...$this->prefixed('column', $this->columnTypes()),
            ...$this->prefixed('control', $this->baseNames("{$root}/resources/assets/controls/*", true)),
            ...$this->prefixed('element', $this->baseNames("{$root}/resources/assets/elements/*", true)),
            ...$this->prefixed('ui', $this->uiMethods()),
        ];

        sort($units);

        return $units;
    }

    /**
     * baseNames returns the file or folder names matching a pattern, without their extension.
     *
     * @return array<int, string>
     */
    protected function baseNames(string $pattern, bool $onlyFolders = false): array
    {
        $paths = $onlyFolders ? glob($pattern, GLOB_ONLYDIR) : glob($pattern);

        return array_map(fn (string $path) => pathinfo($path, PATHINFO_FILENAME), $paths);
    }

    /**
     * partialTypes returns the type names of the partials matching a pattern, leaving out the generic widget partial.
     *
     * @return array<int, string>
     */
    protected function partialTypes(string $pattern, string $prefix): array
    {
        $types = array_map(fn (string $name) => substr($name, strlen($prefix)), $this->baseNames($pattern));

        return array_values(array_diff($types, ['widget']));
    }

    /**
     * columnTypes returns the list column types, one for each evalXxxTypeValue method of the value processor.
     *
     * @return array<int, string>
     */
    protected function columnTypes(): array
    {
        $types = [];

        foreach ((new ReflectionClass(HasValueProcessor::class))->getMethods() as $method) {
            if (preg_match('/^eval(\w+)TypeValue$/', $method->getName(), $match)) {
                $types[] = strtolower($match[1]);
            }
        }

        return $types;
    }

    /**
     * uiMethods returns the public methods of the Ui factory.
     *
     * @return array<int, string>
     */
    protected function uiMethods(): array
    {
        $methods = (new ReflectionClass(UiFactory::class))->getMethods(ReflectionMethod::IS_PUBLIC);

        return array_values(array_filter(
            array_map(fn (ReflectionMethod $method) => $method->getName(), $methods),
            fn (string $name) => ! str_starts_with($name, '__')
        ));
    }

    /**
     * prefixed adds a unit kind prefix to each name.
     *
     * @param  array<int, string>  $names
     * @return array<int, string>
     */
    protected function prefixed(string $kind, array $names): array
    {
        return array_map(fn (string $name) => "{$kind}:{$name}", $names);
    }
}
