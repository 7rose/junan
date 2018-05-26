<?php
namespace App\Helpers;
use Request;

class Part
{
    
    // 从地址栏获取action
    public function actionFromUrl()
    {
        $path = Request::path();
        if(!starts_with($path ,'filter/')) return false;
        $path_array = explode('/', $path);
        return end($path_array);
    }

    public function actionText()
    {
        $action = $this->actionFromUrl();

        switch ($action) {
            case 'no_class':
                return "未开班的";
                break;

        case 'ready_for_1':
                return "具备科目1预约条件的";
                break;
            
            default:
                # code...
                break;
        }
    }

}
