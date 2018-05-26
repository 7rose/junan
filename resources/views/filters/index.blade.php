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
    <div class="tab-pane fade in active" id="list">
        @if(isset($records) && count($records))
        <table class="table table-hover">
        <caption>
            
                <div class="dropdown pull-right">
                    <button type="button" class="btn  btn-sm btn-info dropdown-toggle" id="dropdownMenu1" 
                            data-toggle="dropdown">
                        筛选结果: 共{{ count($records) }}条记录
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/no_class">未开班的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_1">没有驾校的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目2/3未分配教练员的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_1">科目1: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目1: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目1: 报名成功的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目1: 不合格的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目2: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目2: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目2: 报名成功的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目2: 不合格的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目3: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目3: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目3: 报名成功的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目3: 不合格的</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目4: 具备预约条件的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目4: 提交预约申请的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目4: 报名成功的</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">科目4: 不合格的</a></li>
                    </ul>
                </div>
                    @if($part->actionFromUrl())
                    <form method="POST" action="/filter/part">
                        {{ csrf_field() }}
                        <input type="hidden" name="all_id" value="{{ $all_id }}">
                        <input type="hidden" name="action" value="{{ $part->actionFromUrl() }}">
                        <input type="hidden" id="post_data" name="post_data" value="">
                    <button type="submit" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-th"></span> 批处理 : {{ $part->actionText() }}</button>
                    </form>
                        
                    @endif

        </caption>
        <thead>
            <tr>
                <th>姓名</th>
                <th>电话</th>
                <th>身份证</th>
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

{{-- 审核 --}}
<script>
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