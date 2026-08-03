<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScholarCatalogueResource extends JsonResource
{

    private $language;

    public function __construct($resource, $language = null)
    {
        parent::__construct($resource);
        $this->language = $language;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->whenLoaded('languages', function () {
            return $this->languages->firstWhere('pivot.language_id', $this->language)?->pivot;
        });
       
        return [
            'id' => $this->id,
            'name' => $translation?->name,
            'canonical' => $translation?->canonical,
            'description' => $translation?->description,
            'content' => $translation?->content,
            'meta_title' => $translation?->meta_title,
            'meta_keyword' => $translation?->meta_keyword,
            'meta_description' => $translation?->meta_description,
            'publish' => $this->publish,
            'order' => $this->order,
            'parent_id' => $this->parent_id,
            'lft' => $this->lft,
            'rgt' => $this->rgt,
            'level' => $this->level,
            'created_at' =>  $this->created_at?->format('d-m-Y'),
            'updated_at' =>  $this->updated_at?->format('d-m-Y'),
        ];

    }
}
