<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
{
    /**
     * {@inheritdoc}
     */
    public function toResponse($request): JsonResponse
    {
        return JsonResource::toResponse($request);
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array|JsonResponse
    {
        $response = collect([]);

        if ($this->collection->isEmpty()) {
            return response()->json(['message' => 'No comments']);
        }

        $this->collection->each(function ($comment) use ($response, $request) {

            $result = collect([
                'comment' => $comment,
                'user' => $comment->user()->first(),
            ]);

            $response->add($result);
        });

        return $response->toArray();
    }
}
