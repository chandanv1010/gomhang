<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class PostRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Post $model
    ){
        $this->model = $model;
        parent::__construct($model);
    }

    

    /**
     * Keyword search over published posts, matching the title first and falling
     * back to the summary so a phrase from the body still finds the article.
     *
     * Only the columns the search listing renders are selected - no eager loads,
     * so it costs one query plus one count.
     */
    public function search(?string $keyword, int $language_id, int $perPage = 12)
    {
        $keyword = trim((string) $keyword);

        return $this->model->select([
                'posts.id',
                'posts.image',
                'posts.post_catalogue_id',
                'posts.created_at',
                'tb2.name',
                'tb2.description',
                'tb2.canonical',
            ])
            ->join('post_language as tb2', 'tb2.post_id', '=', 'posts.id')
            ->where('tb2.language_id', '=', $language_id)
            ->where('posts.publish', '=', 2)
            ->whereNull('posts.deleted_at')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('tb2.name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('tb2.description', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->orderByDesc('posts.id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getPostById(int $id = 0, $language_id = 0){
        return $this->model->select([
                'posts.id',
                'posts.post_catalogue_id',
                'posts.image',
                'posts.icon',
                'posts.album',
                'posts.publish',
                'posts.follow',
                'posts.video',
                'posts.template',
                'posts.created_at',
                'posts.viewed',
                'posts.status_menu',
                'posts.short_name',
                'posts.logo',
                'posts.extra',
                'posts.comments',
                'posts.rate',
                'posts.post_type',
                'posts.recommend',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
        ->join('post_language as tb2', 'tb2.post_id', '=','posts.id')
        ->with('post_catalogues')
        ->where('tb2.language_id', '=', $language_id)
        ->find($id);
    }

}
