<?php

namespace App\Services;

use App\Models\KmsModel;
use App\Models\KmsCatModel;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class KmsService
{
    /**
     * Get categories with visible item counts (cached)
     *
     * @return Collection<int, KmsCatModel>
     */
    public function categoriesWithVisibleCounts(): Collection
    {
        return Cache::remember('kms:categories:with_visible_counts', 300, function () {
            return KmsCatModel::query()
                ->select(['cat_id', 'cat_name', 'status'])
                ->withCount([
                    'kmsItems as jumlah' => function ($q) {
                        $q->where('visibility', 1);
                    }
                ])
                ->orderBy('cat_name')
                ->get();
        });
    }

    /**
     * Paginated public list
     */
    public function listPublic(?int $categoryId = null, int $perPage = 10): Paginator
    {
        return KmsModel::query()
            ->select(['kms_id', 'cat_id', 'judul', 'created_at'])
            ->where('visibility', 1)
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('cat_id', $categoryId);
            })
            ->with(['category:cat_id,cat_name'])
            ->orderByDesc('created_at')
            ->simplePaginate($perPage)
            ->withQueryString();
    }

    /**
     * Show single content
     */
    public function show(string $id): KmsModel
    {
        return KmsModel::query()
            ->select(['kms_id', 'cat_id', 'judul', 'link', 'author', 'visibility', 'created_at', 'updated_at', 'thumbnail', 'status'])
            ->with([
                'user:id,name',
                'category:cat_id,cat_name',
            ])
            ->findOrFail($id);
    }

    /**
     * Live search by title for public contents
     *
     * @return Collection<int, KmsModel>
     */
    public function search(string $term, int $limit = 10): Collection
    {
        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], trim($term));
        if ($escaped === '' || mb_strlen($escaped) < 2) {
            return collect();
        }

        return KmsModel::query()
            ->select(['kms_id', 'cat_id', 'judul', 'created_at'])
            ->where('visibility', 1)
            ->where('judul', 'like', '%' . $escaped . '%')
            ->with(['category:cat_id,cat_name'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new KMS entry
     */
    public function create(array $data): KmsModel
    {
        $now = now();

        $payload = [
            'kms_id'     => (string) Str::uuid(),
            'cat_id'     => (int) $data['category_id'],
            'judul'      => $data['title'],
            'link'       => $data['link'] ?? null,
            'author'     => Auth::id(),
            'visibility' => isset($data['visibility']) ? (int) $data['visibility'] : 1,
            'status'     => isset($data['status']) ? (int) $data['status'] : 1,
            'thumbnail'  => $data['thumbnail'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        return KmsModel::create($payload);
    }

    /**
     * Update an existing KMS entry
     */
    public function update(string $id, array $data): KmsModel
    {
        $model = KmsModel::findOrFail($id);

        $model->fill([
            'cat_id'     => isset($data['category_id']) ? (int) $data['category_id'] : $model->cat_id,
            'judul'      => $data['title'] ?? $model->judul,
            'link'       => array_key_exists('link', $data) ? $data['link'] : $model->link,
            'visibility' => isset($data['visibility']) ? (int) $data['visibility'] : $model->visibility,
            'status'     => isset($data['status']) ? (int) $data['status'] : $model->status,
            'thumbnail'  => array_key_exists('thumbnail', $data) ? $data['thumbnail'] : $model->thumbnail,
            'updated_at' => now(),
        ]);

        $model->save();

        return $model->refresh();
    }

    /**
     * Soft delete by setting deleted_at (since model has no timestamps)
     */
    public function delete(string $id): void
    {
        $model = KmsModel::findOrFail($id);
        $model->deleted_at = now();
        $model->save();
    }
}
