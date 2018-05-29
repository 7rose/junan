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

            case 'date_for_1':
                    return "提交科目1预约申请的";
                    break;

            case 'fail_for_1':
                    return "科目1不合格的";
                    break;
            // 2
            case 'ready_for_2':
                    return "具备科目2预约条件的";
                    break;

            case 'date_for_2':
                    return "提交科目2预约申请的";
                    break;

            case 'fail_for_2':
                    return "科目2不合格的";
                    break;

            // 3
            case 'ready_for_3':
                    return "具备科目3预约条件的";
                    break;

            case 'date_for_3':
                    return "提交科目3预约申请的";
                    break;

            case 'fail_for_3':
                    return "科目3不合格的";
                    break;

            // 4
            case 'ready_for_4':
                    return "具备科目4预约条件的";
                    break;

            case 'date_for_4':
                    return "提交科目4预约申请的";
                    break;

            case 'fail_for_4':
                    return "科目1不合格的";
                    break;
            
            default:
                # code...
                break;
        }
    }

}
