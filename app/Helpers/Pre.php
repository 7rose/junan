<?php
namespace App\Helpers;

use DB;

/**
 * 预处理
 */
class Pre
{
    // 更新财务信息
    public function updateFinance() 
    {
        // DB::table('customers')->update($this->ex());
        $records_array = $this->getRecordsArray();

        if($records_array) {
            foreach ($records_array as $record) {
                // array_push($out, $this->getInsertItem($record));
                DB::table('customers')->where('id', $record->id)->update(['finance_info' => $this->getFinanceNum($record)]);
            }
        }
        // return $out;
    }

    // 数据库获取信息
    private function getRecordsArray($value='')
    {
        $records = DB::table('customers')
                        ->leftJoin('finance', 'customers.id', '=', 'finance.customer_id')
                        ->select(
                            'customers.id', 
                            DB::raw('
                                group_concat(finance.customer_id) as num, 
                                group_concat(finance.price) as all_price, 
                                group_concat(finance.real_price) as all_real_price, 
                                group_concat(finance.in) as all_in
                                '))
                        ->groupBy('customers.id')
                        ->get()
                        ->toArray();
        return $records;
    }

    // 单条修改模型
    private function getFinanceNum($record)
    {
        $in_array = explode(',', $record->all_in);
        $price_array = explode(',', $record->all_price);
        $real_price_array = explode(',', $record->all_real_price);

        $n = 0;
        for ($i=0; $i < count($in_array) ; $i++) { 
            $in_array[$i] == 1 ? $n += (floatval($price_array[$i]) - floatval($real_price_array[$i])) : $n += (floatval($real_price_array[$i]) - floatval($price_array[$i]));
        }
        return $n;
    }    

    // 计算财务结果
    private function ex()
    {
        $records_array = $this->getRecordsArray();
        $out =[];
        if($records_array) {
            foreach ($records_array as $record) {
                array_push($out, $this->getFinanceNum($record));
            }
        }
        return $out;
    }

    // end
}







