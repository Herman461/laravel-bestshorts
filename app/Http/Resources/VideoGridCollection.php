<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VideoGridCollection extends ResourceCollection
{
    public function __construct($resource, $totalPages = null)
    {
        parent::__construct($resource);
        $this->totalPages = $totalPages;
    }

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
//        return parent::toArray($request);
        return [
            'data' => parent::toArray($request),
            'totalPages' => $this->totalPages,
        ];
    }

    public function getData(): array
    {
        return $this->toArray(request())['data']; // Возвращаем только данные из 'data'
    }
}
