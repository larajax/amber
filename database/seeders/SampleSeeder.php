<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Sample;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * SampleSeeder creates the samples the reference pages edit and list, each filling a different mix of fields.
 */
class SampleSeeder extends Seeder
{
    /**
     * Seed the samples, linking them to existing users, groups, categories and tags when there are any.
     */
    public function run(): void
    {
        $users = User::query()->orderBy('id')->limit(5)->pluck('id')->all();
        $groups = UserGroup::query()->where('code', '<>', 'guest')->orderBy('id')->pluck('id')->all();
        $categories = Category::query()->orderBy('nest_left')->pluck('id')->all();
        $tags = Tag::query()->orderBy('id')->pluck('id')->all();

        foreach ($this->samples() as $index => $attributes) {
            $links = Arr::pull($attributes, 'links', []);

            $sample = new Sample;
            $sample->forceFill($attributes);
            $sample->user_id = $users[$index] ?? null;
            $sample->reviewer_id = $users[$index + 1] ?? null;
            $sample->primary_group_id = $groups ? $groups[$index % count($groups)] : null;
            $sample->primary_category_id = $categories[$index * 3] ?? null;
            $sample->save();

            if ($groups) {
                $sample->groups()->sync(array_slice($groups, $index % count($groups), 2));
            }

            $sample->categories()->sync(array_slice($categories, $index * 3 + 1, 2));
            $sample->labels()->sync(array_slice($tags, $index * 2, 2));

            foreach ($links as $link) {
                $sample->links()->create($link);
            }
        }
    }

    /**
     * samples returns the attributes of each seeded sample.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function samples(): array
    {
        return [
            [
                'name' => 'Aurora Desk Lamp',
                'slug' => 'aurora-desk-lamp',
                'code' => 'auroraDeskLamp',
                'reference' => 'REF-1001',
                'email' => 'lighting@example.com',
                'price' => 89.50,
                'quantity' => 24,
                'excerpt' => 'A warm reading light.',
                'summary' => 'An adjustable desk lamp with a dimmer and a weighted base.',
                'description' => 'Aurora bends to any angle and holds its place, with three colour temperatures and a dimmer.',
                'status' => 'active',
                'priority' => 'high',
                'category' => 'lamps',
                'icon' => 'ph ph-lamp',
                'level' => 'pro-yearly',
                'timezone' => 'Australia/Sydney',
                'size' => 'm',
                'plan' => 'team',
                'features' => ['wifi', 'parking'],
                'permissions' => ['content', 'content_create', 'content_publish'],
                'interests' => ['photography', 'painting'],
                'is_featured' => true,
                'published_at' => '2026-03-14 09:30:00',
                'starts_on' => '2026-11-02',
                'ends_on' => '2026-11-27',
                'opens_at' => '09:00:00',
                'reminder_at' => '08:00:00',
                'color' => '#f39c12',
                'accent_color' => '#6a6cf7',
                'overlay_color' => '#34495e80',
                'css_code' => ".lamp {\n    color: #f39c12;\n}\n",
                'html_code' => "<article class=\"lamp\">\n    <h2>Aurora</h2>\n    <p>A warm reading light that bends to any angle, holds its place wherever you point it, and dims to a soft glow for late nights.</p>\n</article>\n",
                'template' => "<section>\n    <h1>{{ name }}</h1>\n    <p>{{ summary }}</p>\n</section>\n",
                'readme' => "# Aurora Desk Lamp\n\nA **warm** reading light with:\n\n- three colour temperatures\n- a dimmer\n- a weighted base\n",
                'api_key' => 'sk_live_aurora_51f0c2',
                'private_notes' => "Supplier: Lumen Works\nMargin: 42%",
                'tags' => ['lighting', 'bestseller'],
                'keywords' => 'lamp desk dimmable',
                'topics' => ['design', 'engineering'],
                'content' => [
                    ['_group' => 'heading', 'title' => 'Light that follows you', 'subtitle' => 'Bends to any angle'],
                    ['_group' => 'paragraph', 'text' => 'Aurora holds its place wherever you point it.', 'align' => 'left'],
                    ['_group' => 'quote', 'quote' => 'The best lamp on my desk.', 'author' => 'A happy reader'],
                    ['_group' => 'checklist', 'items' => [
                        ['label' => 'Unbox the lamp', 'done' => '1'],
                        ['label' => 'Fit the bulb', 'done' => '0'],
                    ]],
                ],
                'step_style' => 'detailed',
                'steps' => [
                    ['title' => 'Assemble the base', 'minutes' => 5, 'details' => 'Screw the arm into the base.', 'tasks' => [['task' => 'Find the screwdriver']]],
                    ['title' => 'Plug it in', 'minutes' => 1, 'details' => 'Use the supplied cable.', 'tasks' => []],
                ],
                'address' => [
                    'recipient' => 'Lumen Works',
                    'line1' => '12 Harbour Street',
                    'suburb' => 'Sydney',
                    'region_code' => '2000',
                    'instructions' => 'Leave with reception.',
                    'leave_at_door' => '0',
                ],
                'line_items' => [
                    ['item' => 'Lamp body', 'quantity' => 1, 'price' => 60, 'taxable' => 1],
                    ['item' => 'Warm bulb', 'quantity' => 2, 'price' => 14.75, 'taxable' => 1],
                ],
                'meta' => [
                    'seo' => ['title' => 'Aurora Desk Lamp', 'robots' => 'index', 'description' => 'A warm, adjustable reading light.'],
                    'layout' => ['left' => 'Left side', 'right' => 'Right side'],
                    'options' => ['read_only' => 'Saved, but not editable', 'read_only_choice' => 'two', 'read_only_switch' => '1'],
                ],
                'country' => 'au',
                'state' => 'nsw',
                'city' => 'sydney',
                'postcode' => '2000',
                'has_discount' => true,
                'discount_code' => 'AURORA10',
                'discount_percent' => 10,
                'delivery' => 'courier',
                'delivery_address' => '12 Harbour Street, Sydney',
                'headline' => 'The desk lamp that bends to your work',
                'checked_at' => '2026-10-30 09:00:00',
                'page_link' => 'reference://ui/buttons',
                ...$this->editorWidgetValues(),
                'currency' => 'aud',
                'stock_status' => 'low',
                'transport' => 'road',
                'visibility' => 'public',
                'audience' => 'retail',
                'seo' => ['title' => 'Aurora Desk Lamp', 'robots' => 'index', 'description' => 'A warm, adjustable reading light.'],
                'packaging' => ['boxes' => 1, 'crate_notes' => ''],
                'group_code' => 'editors',
                'shipping' => [
                    'recipient' => 'Lumen Works',
                    'line1' => '12 Harbour Street',
                    'suburb' => 'Sydney',
                    'region_code' => '2000',
                    'instructions' => 'Leave with reception.',
                    'leave_at_door' => '0',
                ],
                'contact' => ['person' => 'Mia Lumen', 'phone' => '02 5550 1234'],
                'faqs' => [
                    ['question' => 'Which bulbs does it take?', 'answer' => 'Any E27 bulb up to 9 watts.'],
                    ['question' => 'Can it clamp to a desk?', 'answer' => 'Yes, with the optional clamp.'],
                ],
                'highlights' => [
                    ['text' => 'Three colour temperatures'],
                    ['text' => 'Weighted base'],
                ],
                'milestones' => [
                    ['title' => 'Prototype signed off', 'due' => 'March'],
                    ['title' => 'First production run', 'due' => 'June'],
                ],
                'dimensions' => [
                    ['label' => 'Height', 'value' => '48 cm'],
                    ['label' => 'Base', 'value' => '18 cm'],
                ],
                'sections' => [
                    ['type' => 'text', 'title' => 'Designed for reading', 'body' => 'A warm, even light across the page.'],
                    ['type' => 'image', 'caption' => 'The lamp at full stretch', 'alignment' => 'wide'],
                ],
                'specs' => [
                    ['property' => 'Power', 'value' => '9', 'unit' => 'W', 'note' => 'At full brightness'],
                ],
                'itinerary' => [
                    ['day' => 'Monday', 'stops' => [['place' => 'Factory floor'], ['place' => 'Quality lab']]],
                    ['day' => 'Tuesday', 'stops' => [['place' => 'Warehouse']]],
                ],
                'languages' => ['English', 'Japanese'],
                'links' => [
                    ['title' => 'Assembly guide', 'url' => 'https://example.com/aurora/assembly'],
                    ['title' => 'Warranty', 'url' => 'https://example.com/aurora/warranty'],
                ],
            ],
            [
                'name' => 'Basalt Standing Desk',
                'slug' => 'basalt-standing-desk',
                'code' => 'basaltStandingDesk',
                'reference' => 'REF-1002',
                'price' => 649.00,
                'quantity' => 6,
                'summary' => 'A motorised standing desk with four memory presets.',
                'status' => 'draft',
                'priority' => 'normal',
                'category' => 'desks',
                'icon' => 'ph ph-desktop',
                'level' => 'basic',
                'size' => 'l',
                'plan' => 'business',
                'features' => ['gym'],
                'is_featured' => false,
                'starts_on' => '2026-12-01',
                'color' => '#34495e',
                'tags' => ['furniture'],
                'keywords' => 'desk standing motorised',
                'topics' => ['engineering'],
                'step_style' => 'simple',
                'steps' => [
                    ['title' => 'Attach the legs', 'minutes' => 15, 'tasks' => [['task' => 'Check every bolt']]],
                ],
                'line_items' => [
                    ['item' => 'Desk frame', 'quantity' => 1, 'price' => 499, 'taxable' => 1],
                    ['item' => 'Bamboo top', 'quantity' => 1, 'price' => 150, 'taxable' => 1],
                ],
                'country' => 'nz',
                'state' => 'wgn',
                'city' => 'wellington',
                'postcode' => '6011',
                'delivery' => 'pickup',
                'headline' => 'Stand up',
                'published_at' => '2026-10-20 13:30:00',
                'page_link' => 'reference://extending/icons',
            ],
            [
                'name' => 'Cobalt Lounge Chair',
                'slug' => 'cobalt-lounge-chair',
                'code' => 'cobaltLoungeChair',
                'reference' => 'REF-1003',
                'price' => 329.99,
                'quantity' => 0,
                'summary' => 'A deep lounge chair upholstered in cobalt wool.',
                'status' => 'archived',
                'category' => 'chairs',
                'icon' => 'ph ph-armchair',
                'plan' => 'starter',
                'is_featured' => false,
                'color' => '#3498db',
                'overlay_color' => '#3498db66',
                'tags' => ['furniture', 'archived'],
                'meta' => [
                    'seo' => ['title' => 'Cobalt Lounge Chair', 'robots' => 'noindex'],
                ],
                'country' => 'us',
                'state' => 'ny',
                'city' => 'new-york',
                'postcode' => '10001',
                'delivery' => 'post',
                'delivery_address' => '350 Fifth Avenue, New York',
                'page_link' => 'reference://extending/retired',
            ],
            [
                'name' => 'Driftwood Shelf',
                'slug' => 'driftwood-shelf',
                'code' => 'driftwoodShelf',
                'reference' => 'REF-1004',
                'price' => 119.00,
                'quantity' => 40,
                'summary' => 'A floating shelf cut from reclaimed driftwood.',
                'status' => 'active',
                'priority' => 'low',
                'category' => 'shelving',
                'icon' => 'ph ph-books',
                'is_featured' => true,
                'color' => '#1abc9c',
                'interests' => ['woodworking', 'sailing', 'surfing'],
                'topics' => ['design', 'research'],
                'content' => [
                    ['_group' => 'heading', 'title' => 'Every shelf is different'],
                ],
                'headline' => 'Reclaimed from the shore, finished by hand',
                'published_at' => '2026-10-20 12:30:00',
                'page_link' => 'https://larajax.org',
            ],
        ];
    }

    /**
     * editorWidgetValues returns the first sample's values for the code, color, date, data table, markdown, rich editor and sensitive reference pages.
     *
     * @return array<string, mixed>
     */
    public function editorWidgetValues(): array
    {
        return [
            // Code editor
            'php_code' => "<?php\n\nreturn [\n    'name' => 'Aurora Desk Lamp',\n    'price' => 89.50,\n    'temperatures' => [2700, 4000, 6500],\n];\n",
            'config_code' => "lamp:\n  name: Aurora\n  finishes:\n    - brass\n    - chrome\n  light:\n    temperatures: [2700, 4000, 6500]\n    dimmable: true\n",
            'script_code' => "const lamp = document.querySelector('.lamp');\n\nlamp.addEventListener('click', () => {\n    lamp.classList.toggle('is-on');\n});\n",
            'scss_code' => ".lamp {\n    \$glow: #f39c12;\n\n    color: \$glow;\n\n    &:hover {\n        color: darken(\$glow, 10%);\n    }\n}\n",
            'generated_code' => "{\n    \"sku\": \"AUR-001\",\n    \"stock\": 24,\n    \"finishes\": [\"brass\", \"chrome\"]\n}\n",
            'embed_code' => "<iframe src=\"https://example.com/aurora/viewer\" title=\"Aurora in 3D\"></iframe>\n",

            // Color picker
            'text_color' => '#2c3e50',
            'brand_color' => '#0f766e',
            'border_color' => '#bdc3c7',

            // Data table
            'deliveries' => [
                ['date' => '2026-11-04', 'from' => '09:00', 'until' => '1:00 PM', 'carrier' => 'Harbour Freight'],
                ['date' => '2026-11-11', 'from' => '13:00', 'until' => '5:30 PM', 'carrier' => 'Coastline Couriers'],
            ],
            'variants' => [
                ['sku' => 'AUR-BR', 'finish' => 'brass', 'color' => 'white'],
                ['sku' => 'AUR-CH', 'finish' => 'chrome', 'color' => 'sage'],
                ['sku' => 'AUR-MB', 'finish' => 'matte', 'color' => 'Charcoal'],
            ],
            'stockists' => [
                ['name' => 'Lumen Store', 'country' => 'au', 'city' => 'sydney', 'channel' => 'retail'],
                ['name' => 'Kiwi Lighting', 'country' => 'nz', 'city' => 'wellington', 'channel' => 'wholesale'],
                ['name' => 'Lamp House', 'country' => 'us', 'city' => 'new-york', 'channel' => 'online'],
            ],
            'parts' => [
                ['name' => 'Brass arm', 'sku' => 'LP-104', 'quantity' => 1, 'weight' => 0.4, 'supplier' => 'Lumen Works'],
                ['name' => 'Steel base', 'sku' => 'LP-210', 'quantity' => 1, 'weight' => 1.2, 'supplier' => 'Forge and Co'],
            ],
            'price_breaks' => [
                ['from_quantity' => 1, 'discount' => 0],
                ['from_quantity' => 10, 'discount' => 5],
                ['from_quantity' => 50, 'discount' => 12],
            ],
            'bundle_items' => [
                ['item' => 'Aurora Desk Lamp', 'quantity' => 1],
                ['item' => 'Warm bulb', 'quantity' => 2],
            ],
            'suppliers' => [
                ['name' => 'Lumen Works', 'contact' => 'Mia Lumen', 'email' => 'mia@example.com', 'lead_days' => 14],
                ['name' => 'Forge and Co', 'contact' => 'Ravi Forge', 'email' => 'ravi@example.com', 'lead_days' => 21],
                ['name' => 'Bright Glass', 'contact' => 'Ana Bright', 'email' => 'ana@example.com', 'lead_days' => 10],
            ],
            'components' => [
                ['name' => 'Arm', 'material' => 'Brass', 'weight' => 400],
                ['name' => 'Base', 'material' => 'Steel', 'weight' => 1200],
                ['name' => 'Shade', 'material' => 'Aluminium', 'weight' => 180],
                ['name' => 'Cable', 'material' => 'Fabric', 'weight' => 90],
            ],
            'stock_levels' => [
                ['warehouse' => 'Sydney', 'on_hand' => 24, 'reorder_at' => 10],
                ['warehouse' => 'Melbourne', 'on_hand' => 18, 'reorder_at' => 10],
                ['warehouse' => 'Brisbane', 'on_hand' => 9, 'reorder_at' => 8],
                ['warehouse' => 'Perth', 'on_hand' => 6, 'reorder_at' => 5],
                ['warehouse' => 'Adelaide', 'on_hand' => 11, 'reorder_at' => 5],
                ['warehouse' => 'Auckland', 'on_hand' => 14, 'reorder_at' => 6],
                ['warehouse' => 'Wellington', 'on_hand' => 4, 'reorder_at' => 6],
                ['warehouse' => 'Los Angeles', 'on_hand' => 32, 'reorder_at' => 15],
                ['warehouse' => 'New York', 'on_hand' => 27, 'reorder_at' => 15],
                ['warehouse' => 'Chicago', 'on_hand' => 12, 'reorder_at' => 10],
            ],
            'reviews' => [],
            'spec_sheet' => [
                ['label' => 'Height', 'value' => '48 cm at full stretch, folding down to 30 cm for travel or a low shelf, with the shade tilting a further 10 cm forward over the page','note' => 'Measured from the base to the top of the shade'],
                ['label' => 'Bulb', 'value' => 'E27, up to 9 W', 'note' => 'LED bulbs only, since halogen bulbs run too hot for the shade'],
                ['label' => 'Cable', 'value' => '1.8 m fabric', 'note' => 'With an inline dimmer'],
            ],
            'certifications' => [
                ['standard' => 'IEC 60598', 'issued_by' => 'SafeLight Labs', 'year' => 2025],
                ['standard' => 'Energy Star', 'issued_by' => 'Efficiency Board', 'year' => 2026],
            ],
            'awards' => [
                ['award' => 'Good Design Award', 'year' => 2025],
                ['award' => 'Lighting Product of the Year', 'year' => 2026],
            ],

            // Date picker
            'closes_at' => '17:30:00',
            'restock_on' => '2026-11-02',
            'launch_on' => '2026-03-01',
            'delivery_on' => '2026-11-04',
            'manufactured_on' => '2025-08-18',
            'review_on' => '2026-12-07',
            'discontinued_on' => '2027-12-31',
            'expires_at' => null,
            'webinar_at' => '2026-11-18 19:00:00',
            'inspected_at' => '2026-10-28 15:45:00',
            'sold_at' => '2026-10-29 11:20:00',

            // Markdown editor
            'changelog' => "## 1.2.0\n\n- Added a warm 2200K setting\n- A quieter dimmer\n\n## 1.1.0\n\n- First release with the weighted base\n",
            'faq' => "**Which bulbs fit?** Any E27 bulb up to 9 watts.\n",
            'announcement' => "## Spring launch\n\n<iframe srcdoc=\"<p style='font-family: sans-serif'>Launching on 1 March</p>\" title=\"Launch date\" height=\"60\"></iframe>\n\nAurora ships worldwide from March.\n",
            'terms' => "Orders ship within **two business days**. See the [returns policy](https://example.com/returns).\n",
            'warranty' => "Covered for **five years** against defects in the arm and base.\n",
            'care_guide' => "# Care\n\n1. Unplug the lamp before cleaning\n2. Wipe with a dry cloth\n\n> Never use solvents on the brass finish.\n",

            // Rich editor
            'overview' => '<p>Aurora is a <strong>warm reading light</strong> that bends to any angle and holds its place.</p><ul><li>Three colour temperatures</li><li>A dimmer</li><li>A weighted base</li></ul>',
            'key_points' => '<ul><li><strong>Bends</strong> to any angle</li><li>Dims to a <em>night light</em></li></ul>',
            'specifications' => '<h3>Light</h3><p>2700K to 6500K, dimmable to 5%.</p><h3>Build</h3><p>A brushed brass arm on a weighted steel base.</p>',
            'story' => '<p>Aurora began as a sketch on a late train home, drawn under a reading light that would not stay put.</p>',
            'signature' => '<p>Mia Lumen<br>Lumen Works<br><a href="mailto:mia@example.com">mia@example.com</a></p>',
            'article' => '<h2>Designed for reading</h2><p class="oc-text-gray">Notes from the design team.</p><p>Warm light falls evenly across the page, with no glare on glossy paper.</p>',
            'teaser' => '<p>The desk lamp that bends to your work.</p>',
            'disclaimer' => '<p>Specifications may change without notice. <strong>Colours</strong> vary slightly between batches.</p>',
            'press_release' => '<h2>Aurora launches worldwide</h2><p>Lumen Works today released <strong>Aurora</strong>, a desk lamp that bends to any angle.</p>',

            // Sensitive
            'webhook_secret' => 'whsec_aurora_7f3a91c2',
            'license_key' => 'AURORA-4F2K-91XZ-77QD',
            'access_token' => 'tok_aurora_c0ffee42',
        ];
    }
}
