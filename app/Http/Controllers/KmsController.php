<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KmsCatModel;
use App\Models\KmsModel;

class KmsController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category');

        $categories = KmsCatModel::withCount([
            'kmsItems as jumlah' => function ($q) {
                $q->where('visibility', 1);
            }
        ])->get();

        $content = KmsModel::where('visibility', 1)
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('cat_id', $categoryId);
            })
            ->with(['user', 'category'])
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString(); // ⬅ penting agar pagination ikut filter

        return view('pages.kms.index', compact('categories', 'content', 'categoryId'));
    }


    public function detail($id)
    {
        $content = KmsModel::with(['user', 'category'])->findOrFail($id);
        return view('pages.kms.detail', compact('content'));
    }

    public function liveSearch(Request $request)
    {
        $cari = $request->get('q');

        $content = KmsModel::where('visibility', 1)
            ->where('judul', 'like', "%{$cari}%")
            ->with(['user', 'category'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return response()->json($content);
    }
}
