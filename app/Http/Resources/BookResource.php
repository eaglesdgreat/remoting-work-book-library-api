<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'publisher' => $this->publisher,
            'published_date' => $this->published_date,
            'number_of_pages' => $this->number_of_pages,
            'language' => $this->language,
            'rating' => $this->rating,
            'ratings' => $this->ratings,
            'book_url' => $this->book_url,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
