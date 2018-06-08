<?php
    $part = new App\Helpers\Part;
    // $seek = new App\Helpers\Seek;
    // $auth = new App\Helpers\Auth;
    $all_id = '';
    foreach ($records as $record) {
        $all_id .= $record->id.',';
    }
?>
@extends('../nav')

@section('container')
    @if($part->actionFromUrl())
        <div class="alert alert-info"><h4>{{ $part->actionText() }}</h4></div>
    @endif
    <div class="tab-pane fade in active" id="list">
        <div>
                <div class="dropdown pull-right">
                    @if(count($records))
                    <a href="/filter/download/excel" class="btn btn-sm btn-success">导出Excel</a>
                    @endif
                    
                    <button type="button" class="btn  btn-sm btn-info dropdown-toggle" id="dropdownMenu1" 
                            data-toggle="dropdown">
                        筛选结果: 共{{ count($records) }}条记录
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/no_class">未开班的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/score/choose"><span class="glyphicon glyphicon-edit"></span>成绩处理和考生清单</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_1">科目1: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_1">科目1: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_1">科目1: 不合格的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_2">科目2: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_2">科目2: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_2">科目2: 不合格的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_3">科目3: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_3">科目3: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_3">科目3: 不合格的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_4">科目4: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_4">科目4: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_4">科目4: 不合格的</a></li>
                    </ul>
                </div>
                {{-- 如果有科目和预约日期则处理成绩 --}}
                @if(isset($lesson) && isset($order_date))
                <form method="POST" action="/filter/part">
                    {{ csrf_field() }}
                    <input type="hidden" name="lesson" value="{{ $lesson }}">
                    <input type="hidden" name="order_date" value="{{ $order_date }}">
                    <input type="hidden" name="all_id" value="{{ $all_id }}">
                    <input type="hidden" name="action" value="score_ex">
                    <input type="hidden" id="post_data" name="post_data" value="">
                    <button type="submit" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-th"></span> 批处理 : {{ '科目'.$lesson.': '. date('Y-m-d', $order_date).'成绩' }}</button>
                </form>
                @else
                    @if($part->actionFromUrl())
                    <form method="POST" action="/filter/part">
                        {{ csrf_field() }}
                        <input type="hidden" name="all_id" value="{{ $all_id }}">
                        <input type="hidden" name="action" value="{{ $part->actionFromUrl() }}">
                        <input type="hidden" id="post_data" name="post_data" value="">
                         <button type="submit" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-th"></span> 批处理 : {{ $part->actionText() }}</button>
                    </form>
                    @endif
                @endif


        </div>
        @if(isset($records) && count($records))
        <table class="table table-hover">
        <caption>
        
        </caption>
        <thead>
            <tr>
                <th>姓名</th>
                <th>电话</th>
                <th>身份证</th>
                <th>驾校</th>
                <th>教练</th>
                <th>开班信息</th>
                <th>证照类型</th>
                <th>批处理标记</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                
 
                <tr>
                    <td>{{ $record->customer_name }}</td>
                    <td>{{ $record->customer_mobile }}</td>
                    <td>{{ $record->customer_id_number }}</td>
                    <td>{{ $record->branch_text }}</td>
                    <td>{{ $record->user_name }}</td>
                    <td>{{ explode('(', $record->class_branch_text)[0].$record->class_no }}</td>
                    <td>{{ $record->licence_type_text }}</td>
                    <td id="select{{ $record->id }}">
                        <button  class="btn btn-xs btn-default" onclick="javascript:select({{ $record->id }})"><span class="glyphicon glyphicon-pushpin"></span> 标记</button>
                </td>
                
            @endforeach
        </tbody>
        </table>
        
        @else
            <!-- 顶部间距 -->
            <div style="height: 20px"></div>
            <div class="alert alert-warning"><strong>无记录:</strong> 数据库尚无记录, 或者是没有符合查询条件记录.&nbsp&nbsp
            </div>
        @endif
    </div>


<input type="text" name="name" id="name">

{{-- 审核 --}}
<script src="{{ URL::asset('junan/js/seekPages.js') }}"></script>
<script>
    $(function(){ 
    　　init('ready_for_1');
    }); 
    
    function select(key) {
        var selected = "<button  class=\"btn btn-xs btn-danger\" onclick=\"javascript:cancel("+key +")\"><span class=\"glyphicon glyphicon-pushpin\"></span> 已标记</button>";
        var td = $("#select"+key);
        var post_data = $("#post_data").val();

        td.html(selected);

        if(!post_data){
            $("#post_data").val(key);
        }else{
            var new_data = post_data + "," + key;
            var arr = new_data.split(",");

            var new_arr = [];
            for(var i=0;i<arr.length;i++) {
            　　var items=arr[i];
            　　//判断元素是否存在于new_arr中，如果不存在则插入到new_arr的最后
            　　if($.inArray(items,new_arr)==-1) {
            　　　　new_arr.push(items);
            　　}
            }
            new_arr.join(",");
            $("#post_data").val(new_arr);
        } 
    }

    function cancel(key)
    {
        var normal = "<button  class=\"btn btn-xs btn-default\" onclick=\"javascript:select("+key+")\"><span class=\"glyphicon glyphicon-pushpin\"></span> 标记</button>";
        var td = $("#select"+key);
        td.html(normal);

        var post_data = $("#post_data").val();
        var arr = post_data.split(",");
        arr.splice($.inArray(String(key), arr), 1); // 必须转字符串

        if(arr.length == 0) {
            $("#post_data").val("");
        }else{
            // arr.join(",");
            $("#post_data").val(arr.join(","));
        }
    }

</script>

@endsection