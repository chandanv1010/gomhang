<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Review;

use App\Services\V1\Core\ReviewService;


class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(
        ReviewService $reviewService,
    ){
        $this->reviewService = $reviewService;
    }

    public function create(Request $request){
        $response = $this->reviewService->create($request);
        return response()->json($response); 
    }

    public function changeStatus(Request $request){
        $id = $request->id;
        $status = $request->status;
        $response = Review::where('id', $id)->update(['status' => $status]);
        return response()->json($response); 
    }
    
    
}
