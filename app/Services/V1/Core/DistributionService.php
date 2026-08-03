<?php

namespace App\Services\V1\Core;

use App\Repositories\Core\DistributionRepository;
use Illuminate\Support\Facades\DB;
use App\Services\V1\BaseService;

class DistributionService extends BaseService
{
    protected $distributionRepository;

    public function __construct(
        DistributionRepository $distributionRepository
    ){
        $this->distributionRepository = $distributionRepository;
    }

    public function paginate($request){
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'province_id' => $request->integer('province_id'),
            'district_id' => $request->integer('district_id'),
        ];
        $perPage = $request->integer('perpage') > 0 ? $request->integer('perpage') : 20;
        
        return $this->distributionRepository->distributionPagination(
            ['id', 'name', 'phone', 'email', 'address', 'image', 'map', 'province_id', 'district_id', 'publish'],
            $condition,
            $perPage,
            ['path' => 'distribution/index'],
            ['id', 'DESC'],
            [],
            ['region', 'area']
        );
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $distribution = $this->distributionRepository->create($payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $distribution = $this->distributionRepository->update($id, $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $this->distributionRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }
}
