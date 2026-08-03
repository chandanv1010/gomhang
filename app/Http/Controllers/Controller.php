<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\RedirectResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $language;

    public function __construct(){
        
    }

    protected function handleActionResponse($response, $request, string $message = 'Cập nhật bản ghi thành công', string $redirectRoute = ''): RedirectResponse {
         if ($response) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()->back()->with('success', $message);
            }
            return redirect()->route($redirectRoute)->with('success', $message);
        }
        return redirect()->back()->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    protected function checkExists($model, string $message = 'Bản ghi không tồn tại', string $redirectRoute = ''){
        if(!$model){
            return redirect()->route($redirectRoute)->with('error', $message);  
        }
    }
    
}
