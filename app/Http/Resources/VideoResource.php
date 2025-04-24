<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authUser = auth()->user() ? clone(auth()->user()) : null;

        if (!empty($authUser)) {
            $authUser->liked = $this->isAuthUserLikedPost();
            $authUser->commented = $this->isAuthUserCommentedPost();
            $authUser->is_following = $authUser->isFollowing($this->user);
        }

        return [
            'video' => [
                'id' => $this->id,
                'preview' => $this->preview,
                'src' => $this->filename,
                'title' => $this->title,
                'description' => $this->description,
                'slug' => $this->slug,
                'views' => $this->views,
                'created_at' => $this->created_at,
                'fullpath' => $this->fullpath,
            ],
            'user' => $this->user,
            'likes_count' => $this->likes()->count(),
            'comments_count' => $this->comments()->count(),
            'comments' => [],
            'tags' => $this->tags,
            'auth_user' => $authUser,

        ];
    }
}
