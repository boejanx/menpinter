<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KmsService;
use App\Http\Requests\KmsStoreRequest;
use App\Http\Requests\KmsUpdateRequest;

class KmsController extends Controller
{
    public function __construct(private KmsService $kms)
    {
    }

    public function index(Request $request)
    {
        $categoryId = $request->integer('category');
        $categories = $this->kms->categoriesWithVisibleCounts();
        $content = $this->kms->listPublic($categoryId, 10);

        return view('pages.kms.index', compact('categories', 'content', 'categoryId'));
    }

    public function detail(string $id)
    {
        $content = $this->kms->show($id);
        return view('pages.kms.detail', compact('content'));
    }

    public function liveSearch(Request $request)
    {
        $term = trim((string) $request->query('q', ''));
        if ($term === '' || mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $content = $this->kms->search($term, 10);
        return response()->json($content);
    }

    // Admin endpoints
    public function store(KmsStoreRequest $request)
    {
        $model = $this->kms->create($request->validated());
        return response()->json(['message' => 'Created', 'data' => $model], 201);
    }

    public function update(string $id, KmsUpdateRequest $request)
    {
        $model = $this->kms->update($id, $request->validated());
        return response()->json(['message' => 'Updated', 'data' => $model]);
    }

    public function destroy(string $id)
    {
        $this->kms->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
}
