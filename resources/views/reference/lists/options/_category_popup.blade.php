<div class="dialog-header">
    <h4 class="dialog-title">
        {{ $category->name }}
    </h4>
    <button type="button" class="btn-close" data-dismiss="popup" aria-label="Close"></button>
</div>

<div class="dialog-body">
    <p>
        {{ $category->description }}
    </p>

    @if ($category->posts->isNotEmpty())
        <ul class="mb-0">
            @foreach ($category->posts as $post)
                <li>
                    {{ $post->title }}
                </li>
            @endforeach
        </ul>
    @else
        <p class="fg-3 mb-0">
            No posts in this category.
        </p>
    @endif
</div>

<div class="dialog-footer">
    <button type="button" class="btn-solid theme-secondary" data-dismiss="popup">
        Close
    </button>
</div>
