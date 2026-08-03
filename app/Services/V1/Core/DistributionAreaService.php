<?php

namespace App\Services\V1\Core;

use App\Repositories\Core\DistributionAreaRepository;
use Illuminate\Support\Facades\DB;
use App\Services\V1\BaseService;

class DistributionAreaService extends BaseService
{
    protected $distributionAreaRepository;

    public function __construct(
        DistributionAreaRepository $distributionAreaRepository
    ){
        $this->distributionAreaRepository = $distributionAreaRepository;
    }

    public function paginate($request){
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
        ];
        
        $perPage = $request->integer('perpage') > 0 ? $request->integer('perpage') : 20;
        
        $where = [];
        if (!empty($condition['keyword'])) {
            $where[] = ['name', 'LIKE', '%'.$condition['keyword'].'%'];
        }
        if ($condition['publish'] !== 0) {
            $where[] = ['publish', '=', $condition['publish']];
        }
        
        $condition['where'] = $where;

        return $this->distributionAreaRepository->pagination(
            ['id', 'name', 'parent_id', 'publish'],
            $condition,
            $perPage,
            ['path' => 'distribution/area/index'],
            ['id', 'DESC'],
            [],
            ['parent']
        );
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $area = $this->distributionAreaRepository->create($payload);
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
            $area = $this->distributionAreaRepository->update($id, $payload);
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
            $this->distributionAreaRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }
}
