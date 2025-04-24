<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VideoCollection extends ResourceCollection
{

    protected $totalPages;

    public function __construct($resource, $totalPages = null)
    {
        parent::__construct($resource);
        $this->totalPages = $totalPages;
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return JsonResource::toResponse($request);
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = collect([]);

        $this->collection->each(function ($video) use ($response, $request) {
            $result = new VideoResource($video);

            $response->add($result);
        });

        return [
            'data' => $response->toArray(),
            'totalPages' => $this->totalPages,
        ];
    }
}
