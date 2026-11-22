<span class="button-text">
    <?= e(\App\Models\Post::where('published', true)->count()) ?> published
</span>
