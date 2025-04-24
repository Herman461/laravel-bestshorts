<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FollowUserCollection extends ResourceCollection
{
    public function __construct($resource, array $followedUserIds = [], $totalPages = 1)
    {
        parent::__construct($resource);
        $this->followedUserIds = $followedUserIds;
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
        return [
        'data' => $this->collection->map(function ($user) {
            return [
                    'user' => $user->makeHidden('email', 'email_verified_at', 'created_at', 'updated_at', 'pivot'),
                    'is_followed' => in_array($user->id, $this->followedUserIds),
                ];
            })->toArray(),
        'totalPages' => $this->totalPages,
        ];
    }
}
