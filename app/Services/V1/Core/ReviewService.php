<?php

namespace App\Services\V1\Core;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Classes\ReviewNested;
use Illuminate\Support\Facades\Auth;

use App\Services\V1\BaseService;
use App\Repositories\Core\ReviewRepository;

/**
 * Class AttributeService
 * @package App\Services
 */
class ReviewService extends BaseService
{
    protected $reviewRepository;
    protected $reviewNestedset;
    
    public function __construct(
        ReviewRepository $reviewRepository,
    ){
        $this->reviewRepository = $reviewRepository;
    }


    public function paginate($request){
        $condition['keyword'] = addslashes($request->input('keyword'));

        $perPage = $request->integer('perpage');
        $reviews = $this->reviewRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            ['path' => 'review/index'], 
        );

        return $reviews;
    }

    public function create($request){
        DB::beginTransaction();
        try{

            $payload = $request->except('_token');

            if($request->hasFile('image')){

                $image = $request->file('image');

                $allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];

                if(!in_array($image->getClientMimeType(), $allowedTypes)) {
                    throw new \Exception('Loại tệp không được phép. Chỉ chấp nhận PNG, JPEG, WebP.');
                }

                $maxSize = 5 * 1024 * 1024; 
                
                if ($image->getSize() > $maxSize) {
                    throw new \Exception('Tệp hình ảnh quá lớn. Vui lòng chọn tệp dưới 5MB.');
                }

                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                $destinationPath = public_path('/userfiles/image');

                $image->move($destinationPath, $imageName);
    
                $payload['image'] = 'userfiles/image/' . $imageName;

            }

            $review = $this->reviewRepository->create($payload);

            $this->reviewNestedset = new ReviewNested([
                'table' => 'reviews',
                'reviewable_type' => $payload['reviewable_type']
            ]);

            $this->reviewNestedset->Get('level ASC, order ASC');

            $this->reviewNestedset->Recursive(0, $this->reviewNestedset->Set());

            $this->reviewNestedset->Action();

            DB::commit();
            return [
                'code' => 10,
                'message' => 'Đánh giá sản phẩm thành công'
            ];
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return [
                'code' => 11,
                'message' => 'Có vấn đề xảy ra! Hãy thử lại'
            ];
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $review = $this->reviewRepository->delete($id);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function paginateSelect(){
        return [
            'id', 
            'reviewable_id', 
            'reviewable_type',
            'email', 
            'phone',
            'fullname',
            'gender',
            'score',
            'description',
            'created_at',
            'status',
            'image'
        ];

    }

}
