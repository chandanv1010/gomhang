<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;


use App\Repositories\Post\PostRepository;
use App\Repositories\Post\PostCatalogueRepository;

use Illuminate\Http\Request;


class PostController extends FrontendController
{
   
    protected $postRepository;
    protected $postCatalogueRepository;

    public function __construct(
        PostRepository $postRepository,
        PostCatalogueRepository $postCatalogueRepository,
    ){
        $this->postRepository = $postRepository;
        $this->postCatalogueRepository = $postCatalogueRepository;
        parent::__construct(); 
    }

   
    public function video(Request $request){
        $id = $request->input('id');

        $post = $this->postRepository->getPostById($id, $this->language);
        $html = $this->renderVideoHtml($post->video);

        return response()->json([
            'html' => $html
        ]);
        
    }

    private function renderVideoHtml($video){
        $explode = explode('/userfiles/flash/', $video);
        $html = '';
        if(count($explode) == 2){
            $html .= '<video width="100%" height="380" controls>';
                $html .= '<source src="'.$video.'" type="video/mp4">';
            $html .= '</video>';
        }else{
            $html .= $video;
        }
        return $html;
    }


    public function updateOrder(Request $request){
        $payload['order'] =  $request->input('order');
        unset($payload['product_id']);
        $id = $request->input('product_id');
        $update_order = $this->postCatalogueRepository->update($id, $payload);
        return response()->json([
            'response' => $update_order, 
            'messages' => 'Cập nhật thứ tự thành công',
            'code' => (!$update_order) ? 11 : 10,
        ]);  
    }

}
