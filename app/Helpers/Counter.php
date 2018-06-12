<?php

namespace App\Helpers;
use Session;

/**
 * 统计
 */
class Counter
{
    // 学费id, 用于计算学费推荐业绩
    private $recruit_id = 27;

    // 换班费id, 用于计算班型推荐业绩
    private $change_class_id = 32;

    // 转数组
    private function arrayFromStr($string)
    {
        $array_out = explode(',', $string);
        return count($array_out) ? $array_out : false;
    }

    // 主统计属性
    public function fllow($record)
    {
        $in_array = $this->arrayFromStr($record->finance_in);
        $price_array = $this->arrayFromStr($record->finance_price);
        $real_price_array = $this->arrayFromStr($record->finance_real_price);
        $item_array = $this->arrayFromStr($record->finance_item);

        if(!count($in_array) || !count($price_array) || !count($real_price_array) || !count($item_array)) return false;

        $total_num = count($in_array);

        // 总体
        $all_num = 0;
        $all_price = 0;
        $all_real_price = 0;

        // 招生
        $recruit_num = 0;
        $recruit_price = 0;
        $recruit_real_price = 0;

        // 换班
        $change_class_num = 0;
        $change_class_price = 0;
        $change_class_real_price = 0;

        for ($i=0; $i < count($in_array); $i++) { 

            $all_num += intval($in_array[$i]) == 1 ? 1 : -1;
            $all_price += intval($in_array[$i]) == 1 ? $price_array[$i] : -$price_array[$i];
            $all_real_price += intval($in_array[$i]) == 1 ? $real_price_array[$i] : -$real_price_array[$i];

            if(intval($in_array[$i]) == 1 && intval($item_array[$i]) == $this->recruit_id) {
                $recruit_num += 1;
                $recruit_price += $price_array[$i];
                $recruit_real_price += $real_price_array[$i];
            }
            if(intval($in_array[$i]) != 1 && intval($item_array[$i]) == $this->recruit_id) {
                $recruit_num += -1;
                $recruit_price += -$price_array[$i];
                $recruit_real_price += -$real_price_array[$i];
            }

            if(intval($in_array[$i]) == 1 && intval($item_array[$i]) == $this->change_class_id) {
                $change_class_num += 1;
                $change_class_price += $price_array[$i];
                $change_class_real_price += $real_price_array[$i];
            }
            if(intval($in_array[$i]) != 1 && intval($item_array[$i]) == $this->change_class_id) {
                $change_class_num += -1;
                $change_class_price += -$price_array[$i];
                $change_class_real_price += -$real_price_array[$i];
            }
        }

        $out_array = ['total'=>$total_num, 'all'=>[$all_num, $all_price, $all_real_price], 'recruit'=>[$recruit_num, $recruit_price, $recruit_real_price], 'change_class'=>[$change_class_num, $change_class_price, $change_class_real_price]];

        return $out_array;
    }

    // 个人财务统计
    public function personal($records)
    {
        if(!count($records)) return false;

        // 总体
        $all_num = 0;
        $all_price = 0;
        $all_real_price = 0;

        // 招生
        $recruit_num = 0;
        $recruit_price = 0;
        $recruit_real_price = 0;

        // 换班
        $change_class_num = 0;
        $change_class_price = 0;
        $change_class_real_price = 0;

        foreach ($records as $record) {

            $all_num += intval($record->in) == 1 ? 1 : -1;
            $all_price += intval($record->in) == 1 ? $record->price : -$record->price;
            $all_real_price += intval($record->in) == 1 ? $record->real_price : -$record->real_price;

            if(intval($record->in) == 1 && intval($record->item) == $this->recruit_id) {
                $recruit_num += 1;
                $recruit_price += $record->price;
                $recruit_real_price += $record->real_price;
            }
            if(intval($record->in) != 1 && intval($record->item) == $this->recruit_id) {
                $recruit_num += -1;
                $recruit_price += -$record->price;
                $recruit_real_price += -$record->real_price;
            }

            if(intval($record->in) == 1 && intval($record->item) == $this->change_class_id) {
                $change_class_num += 1;
                $change_class_price += $record->price;
                $change_class_real_price += $record->real_price;
            }
            if(intval($record->in) != 1 && intval($record->item) == $this->change_class_id) {
                $change_class_num += -1;
                $change_class_price += -$record->price;
                $change_class_real_price += -$record->real_price;
            }
        }

        $out_array = ['all'=>[$all_num, $all_price, $all_real_price], 'recruit'=>[$recruit_num, $recruit_price, $recruit_real_price], 'change_class'=>[$change_class_num, $change_class_price, $change_class_real_price]];

        return $out_array;
    }

    // 个人统计信息显示
    public function personalInfo($records)
    {
        $personal_array = $this->personal($records);
        $out_html = '';

        $out_html .= '<li>总体 - '.'金额: ￥'.$personal_array['all'][2].',  人次: '.$personal_array['all'][0].'</li>';
        $out_html .= '<li>招生 - '.'金额: ￥'.$personal_array['recruit'][2].',  人次: '.$personal_array['recruit'][0].'  比例: '.$this->percent($personal_array['recruit'][2], $personal_array['all'][2]).'%</li>';
        $out_html .= '<li>换班 - '.'金额: ￥'.$personal_array['change_class'][2].',  人次: '.$personal_array['change_class'][0].',  比例: '.$this->percent($personal_array['change_class'][2], $personal_array['all'][2]).'%</li>';
        return $out_html;
    }


    // 百分比
    public function percent($part, $all)
    {
        $float_part = floatval($part);
        $float_all = floatval($all);
        $resault = $float_part * 100 / $float_all;
        return round($resault, 2);
    }

    // 计算净值
    public function purePrice($in_array, $price_array, $real_price_array)
    {
        $out = 0;
        for ($i=0; $i < count($in_array) ; $i++) {
            $in_array[$i] == 1 ? $out += (floatval($price_array[$i]) - floatval($real_price_array[$i])) : $out += (floatval($real_price_array[$i]) - floatval($price_array[$i]));
        }
        return $out;
    }

    // 总体
    public function total($records)
    {
        $out = 0;
        if(count($records)) {
            foreach ($records as $record) {
                $out += intval($record->in) == 1 ? $record->real_price : -$record->real_price;
            }
        }
        return ['total_num'=>count($records) ,'total'=>$out];
    }

    /**
     *
     * 业务统计
     *
     */
    public function lessonInfo($record)
    {
        $pass_array = $this->arrayFromStr($record->lesson_pass);
        $doing_array = $this->arrayFromStr($record->lesson_doing);
        // $pass_array = $this->arrayFromStr($record->lesson_end);

        if(!count($pass_array) || !count($doing_array)) return false;

        // 成绩录入
        $ex_score = '<a class="btn btn-xs btn-block btn-info" href="/filter/score_ex/'.$record->lesson.'-'.$record->order_date.'-'.$record->branch.'">成绩录入</a>';

        $all = count($pass_array);
        $pass = 0;

        for ($i=0; $i < count($pass_array); $i++) { 
            if(intval($doing_array[$i]) == 1) return $ex_score;
            if(intval($pass_array[$i]) == 1) $pass += 1;

        }

        // $resault = $pass * 100 / $all;

        return $all.'人参加考试, 合格'.$pass.'人, 合格率:  '.$this->percent($pass, $all).'%';
    }

    public function lessonSum($record)
    {
        $pass_array = $this->arrayFromStr($record->lesson_pass);
        $doing_array = $this->arrayFromStr($record->lesson_doing);

        if(!count($pass_array) || !count($doing_array)) return false;

        $all = count($pass_array);
        $pass = 0;

        for ($i=0; $i < count($pass_array); $i++) { 
            if(intval($pass_array[$i]) == 1) $pass += 1;
        }

        $out = ['all'=>$all, 'pass'=>$pass, 'percent'=>$this->percent($pass, $all)];
        return $out;
    }

    // 业务-新招, 在学, 毕业
    public function bizSum($record)
    {
        $finish_array = $this->arrayFromStr($record->biz_finish);
        $date_array = $this->arrayFromStr($record->biz_date);
        $finish_time_array = $this->arrayFromStr($record->biz_finish_time);

        if(!count($date_array) || !count($finish_time_array) || !Session::has('date_range')) return false;

        $date_small = intval(Session::get('date_range')['range'][0]);
        $date_big = intval(Session::get('date_range')['range'][1]);

        $all = count($date_array);

        $new = 0;
        $finished = 0;
        $doing = 0;

        for ($i=0; $i < count($date_array); $i++) { 
            if(intval($date_array[$i]) >= $date_small && intval($date_array[$i]) <= $date_big) $new += 1;
            if(intval($finish_time_array[$i]) >= $date_small && intval($finish_time_array[$i]) <= $date_big) $finished += 1;
            if(intval($finish_array[$i]) < 1) $doing += 1;
        }

        $out = ['new'=>$new, 'finished'=>$finished, 'doing'=>$doing];
        return $out;
    }

    // end
}

















