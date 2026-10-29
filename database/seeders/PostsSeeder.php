<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\File;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PostsSeeder extends Seeder
{
    /**
     * @var array<string, array<string, mixed>> categories is the tree of post categories, three levels deep.
     */
    protected array $categories = [
        'Guides' => [
            'Getting Started' => [],
            'Forms' => [
                'Validation' => [],
            ],
            'Lists' => [],
        ],
        'News' => [
            'Releases' => [],
            'Community' => [],
        ],
        'Engineering' => [
            'Frontend' => [
                'Accessibility' => [],
            ],
            'Backend' => [],
        ],
    ];

    /**
     * @var array<int, string> tags shared by the posts.
     */
    protected array $tags = [
        'ajax', 'css', 'design', 'javascript', 'laravel', 'performance', 'php', 'release', 'security', 'tips',
    ];

    /**
     * @var array<int, array{0: string, 1: string}> posts as a title and category pair.
     */
    protected array $posts = [
        ['Building Your First Form', 'Getting Started'],
        ['Five Minutes to a Working List', 'Getting Started'],
        ['Understanding Field Spans', 'Forms'],
        ['Triggers That Show and Hide Fields', 'Forms'],
        ['Validating Nested Data', 'Validation'],
        ['Custom Validation Messages', 'Validation'],
        ['Sorting and Searching Lists', 'Lists'],
        ['Filters That Remember Their State', 'Lists'],
        ['Version 1.2 Release Notes', 'Releases'],
        ['Version 1.3 Release Notes', 'Releases'],
        ['Meet the Contributors', 'Community'],
        ['Community Plugins Roundup', 'Community'],
        ['Rendering Faster Tables', 'Frontend'],
        ['Dark Mode Without Flicker', 'Frontend'],
        ['Keyboard Friendly Popups', 'Accessibility'],
        ['Readable Color Contrast', 'Accessibility'],
        ['Queueing Long Imports', 'Backend'],
        ['Caching Option Lists', 'Backend'],
        ['Writing a Custom Form Widget', 'Guides'],
        ['Working with File Uploads', 'Forms'],
        ['Tree Structures with Nested Sets', 'Lists'],
        ['What Is New in the Toolbar', 'News'],
        ['Designing Bulk Actions', 'Engineering'],
        ['Testing AJAX Handlers', 'Backend'],
        ['Migrating from Older Releases', 'Releases'],
        ['Drag and Drop Reordering', 'Lists'],
        ['Localizing Your Interface', 'Guides'],
        ['Securing File Downloads', 'Backend'],
        ['Our Roadmap for Next Year', 'News'],
        ['A Year of Building in Public', 'Community'],
    ];

    /**
     * @var array<int, string> colors used as the post accent colors and image backgrounds.
     */
    protected array $colors = [
        '#2563eb', '#16a34a', '#dc2626', '#9333ea', '#ea580c', '#0891b2', '#ca8a04', '#db2777',
    ];

    /**
     * Seed the categories, tags and posts with their comments and featured images.
     */
    public function run(): void
    {
        fake()->seed(20261030);

        $categories = $this->seedCategories($this->categories);

        // The database seeder mutes model events, so the nested set values are rebuilt from the parent ids
        (new Category)->resetTreeNesting();

        $tags = collect($this->tags)->mapWithKeys(
            fn (string $name) => [$name => Tag::create(['name' => $name])]
        );

        foreach ($this->posts as $index => [$title, $categoryName]) {
            $this->seedPost($index, $title, $categories[$categoryName], $tags);
        }
    }

    /**
     * seedCategories creates the category tree recursively and returns every category keyed by name.
     *
     * @param  array<string, mixed>  $tree
     * @return array<string, Category>
     */
    protected function seedCategories(array $tree, ?Category $parent = null): array
    {
        $created = [];

        foreach ($tree as $name => $children) {
            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => fake()->sentence(),
                'parent' => $parent?->id,
            ]);

            $created[$name] = $category;
            $created += $this->seedCategories($children, $category);
        }

        return $created;
    }

    /**
     * seedPost creates one post with tags, comments and, for most posts, a generated featured image.
     *
     * @param  Collection<string, Tag>  $tags
     */
    protected function seedPost(int $index, string $title, Category $category, Collection $tags): void
    {
        $color = $this->colors[$index % count($this->colors)];
        $status = match (true) {
            $index % 10 === 9 => 'archived',
            $index % 6 === 5 => 'draft',
            $index % 7 === 3 => 'review',
            default => 'approved',
        };
        $isPublished = $status === 'approved' && $index % 8 !== 7;

        $post = Post::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => $category->id,
            'excerpt' => fake()->sentence(18),
            'content' => '<h2>'.e($title).'</h2>'.PHP_EOL.'<p>'.fake()->paragraph(5).'</p>'.PHP_EOL.'<p>'.fake()->paragraph(4).'</p>',
            'notes' => $index % 3 === 0 ? '**To do:** '.fake()->sentence().PHP_EOL.PHP_EOL.'- '.fake()->sentence().PHP_EOL.'- '.fake()->sentence() : null,
            'keywords' => fake()->randomElements(['accessibility', 'performance', 'security', 'testing', 'tutorial'], $index % 3),
            'status' => $status,
            'published' => $isPublished,
            'published_at' => $isPublished ? now()->subHours(($index * 61) % 2900 + 2) : null,
            'featured_color' => $color,
            'views' => $isPublished ? fake()->numberBetween(40, 9000) : 0,
            'review_on' => $index % 2 === 0 ? now()->addDays(($index * 11) % 120 - 20)->toDateString() : null,
            'share_time' => $index % 3 !== 1 ? sprintf('%02d:%02d:00', 7 + $index % 12, ($index * 15) % 60) : null,
        ]);

        // A few posts stay untagged for the filter's empty option
        if ($index % 9 !== 4) {
            $post->tags()->sync($tags->random(($index % 4) + 1)->pluck('id'));
        }

        Comment::factory()
            ->count(($index * 7) % 6)
            ->for($post)
            ->create();

        if ($index % 4 !== 2) {
            $this->attachFeaturedImage($post, $color);
        }
    }

    /**
     * attachFeaturedImage generates a gradient image in the post's accent color and attaches it, when the GD extension is available.
     */
    protected function attachFeaturedImage(Post $post, string $color): void
    {
        if (!function_exists('imagecreatetruecolor')) {
            return;
        }

        $width = 600;
        $height = 360;
        $image = imagecreatetruecolor($width, $height);
        [$red, $green, $blue] = sscanf($color, '#%02x%02x%02x');

        // Fade the accent color into a darker shade from left to right
        for ($x = 0; $x < $width; $x++) {
            $shade = 1 - ($x / $width) * 0.55;
            $line = imagecolorallocate($image, (int) ($red * $shade), (int) ($green * $shade), (int) ($blue * $shade));
            imageline($image, $x, 0, $x, $height, $line);
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'amber');
        imagepng($image, $tempPath);
        imagedestroy($image);

        try {
            $post->featured_image()->add((new File)->fromFile($tempPath, $post->slug.'.png'));
        }
        finally {
            @unlink($tempPath);
        }
    }
}
