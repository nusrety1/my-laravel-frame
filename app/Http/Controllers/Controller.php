<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function userDetails()
    {
        return apiSuccess(true);
    }

    public function postCreate(Request $request)
    {
        $data = $request->toArray();

        $data['creator_id'] = 1;

        $post = Post::query()->create($data);
        $post->categories()->attach($data['category_id']);

        return apiSuccess();
    }

    public function categoryCreate(Request $request)
    {
        $data = $request->toArray();

        Category::query()->create($data);

        return apiSuccess();
    }
}
