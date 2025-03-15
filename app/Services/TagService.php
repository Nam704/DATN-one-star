<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class TagService
{
    protected $tag;
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function getTags(){
        return $this->tag->all();
    }

    public function getTagById($id){
        return Tag::find($id);
    }

    public function createTag($data){
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tag,slug',
        ]);

        if ($validator->fails()) {
            Log::error("Validation error:", $validator->errors()->toArray());
            return ['status' => 'error', 'errors' => $validator->errors()];
        }

        $slug = isset($data['slug']) ? $data['slug'] : Str::slug($data['name']);

        $category = Tag::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);
        return ['status' => 'success', 'data' => $category];
    }
}