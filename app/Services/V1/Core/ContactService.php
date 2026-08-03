<?php

namespace App\Services\V1\Core;
use Illuminate\Support\Facades\DB;
use App\Mail\ContactMail;

use App\Services\V1\BaseService;

use App\Repositories\Core\ContactRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Post\PostRepository;

class ContactService extends BaseService 
{
    protected $contactRepository;
    protected $productRepository;
    protected $postRepository;

    public function __construct(
        ContactRepository $contactRepository,
        ProductRepository $productRepository,
        PostRepository $postRepository
    ){
        $this->contactRepository = $contactRepository;
        $this->productRepository = $productRepository;
        $this->postRepository = $postRepository;
    }

    public function paginate($request){
        $condition['keyword'] = addslashes($request->input('keyword'));
        $perPage = $request->integer('perpage');
        $contacts = $this->contactRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            ['path' => 'contact/index'], 
        );
        return $contacts;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except('_token');

            $payload['name'] = $request->input('name') ?? $request->input('fullname');
            $contact = $this->contactRepository->create($payload);
            $product_name = ($contact->product_id != null) ? $this->productRepository->getProductById($contact->product_id, 1)->name : null;
            $post_name = ($contact->post_id != null) ?  $this->postRepository->getPostById($contact->post_id, 1)->name : null;
            $to = '';
            $cc = 'tuannc.dev@gmail.com';
            $data = [
                'name' => $contact->name, 
                'created_at' => $contact->created_at,
                'phone' => $contact->phone,
                'address' => $contact->address,
                'type' => $contact->type ?? null,
                'product_id' => $request->product_id,
                'product_name' => $product_name ?? $post_name,
                'post_id' => $post_name, 
            ];

            // \Mail::to($to)->cc($cc)->send(new ContactMail($data));
            DB::commit();
            return [
                'code' => 10,
                'message' => 'Gửi liên hệ thành công , Chúng tôi sẽ sớm phản hồi lại bạn'
            ];
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return [
                'code' => 11,
                'message' => 'Có vấn đề xảy ra! Hãy thử lại'
            ];
        }
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $contact = $this->contactRepository->update($id, $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $contact = $this->contactRepository->delete($id);
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
            'name',
            'address',
            'phone',
            'product_id',
            'post_id',
            'gender',
            'publish',
            'created_at',
            'type',
            'message'
        ];
    }
}
