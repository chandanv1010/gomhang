<?php  
namespace App\Http\Controllers\Ajax\V2;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class HandlerController {

    public function __construct(){

    }

    public function sort(Request $request){
        try {
            DB::beginTransaction();
            $payload = $request->all();
            $table = $this->setTable($payload['model']);
            DB::table($table)->where(['id' => $payload['id']])->update(['order' => $payload['value']]);
            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có vấn đề xảy ra',
                'code' => 500
            ], 500);
        }
    }

    public function changeFieldStatus(Request $request) {
        try {
            DB::beginTransaction();
            $payload = $request->all();
            $table = $this->setTable($payload['model']);
            $valueUpdate = $payload['value'] == 2 ? 1 : 2;
            DB::table($table)->where(['id' => $payload['id']])->update([$payload['field'] => $valueUpdate]);
            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thành công',
                'code' => 200
            ], 200);



        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có vấn đề xảy ra',
                'code' => 500
            ], 500);
        }
    }

    private function setTable($model){
        $snake = Str::snake($model);
        if (str_ends_with($snake, 'y')) {
            return substr($snake, 0, -1) . 'ies';
        }
        return $snake . 's';
    }

}