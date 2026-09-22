<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = Document::query()->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $documents
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $input = $request->get('input');
        $documents = Document::query()
            ->where('title', 'like', '%' . $input . '%')
            ->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $documents
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $document = Document::query()
            ->where('id', $id)
            ->first();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $document
        ]);
    }
}
