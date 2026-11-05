<?php

namespace Tests\Unit\Element;

use Larajax\Ui\Element\ElementBase;
use Larajax\Ui\Element\ElementHolder;
use October\Rain\Extension\Container;
use October\Rain\Extension\ExtensionBase;
use PHPUnit\Framework\TestCase;

/**
 * ElementBaseTest checks that elements boot their extensions and that a holder tracks the values written to it.
 */
class ElementBaseTest extends TestCase
{
    public function test_an_extend_callback_is_applied_on_construction(): void
    {
        $callbacks = Container::$classCallbacks;

        try {
            $seen = null;
            ElementBaseTestElement::extend(function ($element) use (&$seen) {
                $seen = [$element, $element->label];
                $element->addDynamicMethod('shout', fn () => strtoupper($element->label));
            });

            $element = new ElementBaseTestElement(['label' => 'Name']);

            $this->assertSame([$element, 'Name'], $seen);
            $this->assertSame('NAME', $element->shout());
            $this->assertArrayNotHasKey('shout', $element->config);
        }
        finally {
            Container::$classCallbacks = $callbacks;
        }
    }

    public function test_an_implemented_behavior_is_applied(): void
    {
        $element = new ElementBaseTestElementWithBehavior;

        $this->assertSame('applied', $element->behaviorMarker());
        $this->assertSame($element, $element->span('full'));
        $this->assertSame('full', $element->span);
    }

    public function test_a_holder_returns_the_written_value_after_a_read(): void
    {
        $holder = new ElementHolder(['name' => 'old']);

        $this->assertSame('old', $holder->get('name'));

        $holder['name'] = 'new';
        $this->assertSame('new', $holder->get('name'));
        $this->assertSame(['name' => 'new'], $holder->getTouchedElements());

        $holder['name'] = null;
        $this->assertNull($holder->get('name'));
        $this->assertSame(['name' => null], $holder->getTouchedElements());

        unset($holder['name']);
        $this->assertNull($holder->get('name'));
    }
}

/**
 * ElementBaseTestElement is a plain element for the extension tests.
 */
class ElementBaseTestElement extends ElementBase
{
}

/**
 * ElementBaseTestBehavior is a behavior that marks the element it is applied to.
 */
class ElementBaseTestBehavior extends ExtensionBase
{
    public function behaviorMarker(): string
    {
        return 'applied';
    }
}

/**
 * ElementBaseTestElementWithBehavior implements the marker behavior.
 */
class ElementBaseTestElementWithBehavior extends ElementBase
{
    public $implement = [ElementBaseTestBehavior::class];
}
