<?php

namespace Database\Seeders;

use App\Models\Sample;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;

/**
 * SampleSeeder creates the kitchen sink samples, each filling a different mix of fields.
 */
class SampleSeeder extends Seeder
{
    /**
     * Seed the kitchen sink samples, linking them to existing users and groups when there are any.
     */
    public function run(): void
    {
        $users = User::query()->orderBy('id')->limit(4)->pluck('id')->all();
        $groups = UserGroup::query()->where('code', '<>', 'guest')->orderBy('id')->pluck('id')->all();

        foreach ($this->samples() as $index => $attributes) {
            $sample = new Sample;
            $sample->forceFill($attributes);
            $sample->user_id = $users[$index] ?? null;
            $sample->save();

            if ($groups) {
                $sample->groups()->sync(array_slice($groups, $index % count($groups), 2));
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
                'html_code' => "<article class=\"lamp\">\n    <h2>Aurora</h2>\n    <p>A warm reading light.</p>\n</article>\n",
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
            ],
        ];
    }
}
