@extends('nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
            @if(isset($custom))
                @if(array_has($custom,'icon'))
                     <span class="glyphicon glyphicon-{{$custom['icon']}}"></span>
                @endif

                @if(array_has($custom,'title'))
                    {{$custom['title']}}
                @else
                    <span>+ 添加</span>
                @endif
            @endif
            </div>
            <div class="panel-body">
                {!! form($form) !!}
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                推荐人选择
            </div>
            <div class="modal-body">
                <div id="modal_input"></div>
                <div id="modal_main"></div>
            </div>
            <input type="hidden" id="target">
            <input type="hidden" id="now">
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-info btn-sm" onclick="javascript:find()">  搜索  </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script>
    $(document).ready(function(){
      add_btn();
      pre();
    })
    function enter_down(){   
      if(event.keyCode == 13){   
        find();
      }   
    }

    function clear() {
        $("#key_val").val('');
        $("#modal_main").html('');
    }

    // 添加按钮
    function add_btn() {
        // 目标
        $("#target").append(btn);
        var btn = "  <a class=\"btn btn-info btn-sm\" href=\"javascript:open()\">选择</a>";
        $("#user_id_selector").append(btn);

        // 车辆选择
        var btn2 = "  <a class=\"btn btn-success btn-sm\" href=\"javascript:open(1)\">选择</a>";
        // $("#target").val('cars');
        $("#car_no_selector").append(btn2);

    }

    // 准备
    function pre() {
        var input = "<input onkeydown=\"enter_down()\" type=\"text\" class=\"form-control\" id=\"key_val\" placeholder=\"关键词..\">";
        $("#modal_input").html(input);
    }

    // 打开
    function open(now=0) {
        $('#now').val(now);

        clear();
        $('#myModal').modal();
    }

    // 选择器
    function find() {
        var key_val = $("#key_val").val();
        var tirm_val = key_val.replace(/(^\s*)|(\s*$)/g, "");

        if(tirm_val == ''){
            alert('关键词不能为空!'); 
            return false;
        } 
        
        // 用户
        var post_url = "/user/ajax/selector";

        // 车辆
        var target = $("#target").val();
        var now = $("#now").val();

        if(now == 1) {
            post_url = "/cars/ajax/selector";
        }

        var post_data = {key: tirm_val};

        // console.log(post_url);

        $.post(
            post_url,
            post_data,
            function(json){
                // console.log(json);
                json_ex(json);

           }
        );
    }

    // json 处理器
    function json_ex(json) {
        var main = $("#modal_main");
        var josn_obj = JSON.parse(json);

        if(josn_obj.length == 0) {
            main.html('没有符合条件的!');
            return false;
        }

        // 车辆
        // var target = $("#target").val();
        var now = $("#now").val();

        var table = "<table class=\"table table-striped\">";
            if(now == 0){
                for (var i = josn_obj.length - 1; i >= 0; i--) {
                    table += "<tr><td>"+josn_obj[i].work_id+"</td><td>"+josn_obj[i].name+"</td><td>"+josn_obj[i].mobile+"</td><td><button type=\"button\" class=\"btn btn-success btn-xs btn-block\" onclick=\"javascript:set("+josn_obj[i].work_id+")\">选择</button></td></tr>";
                }
            }else if(now == 1){
                for (var i = josn_obj.length - 1; i >= 0; i--) {
                    table += "<tr><td>"+josn_obj[i].car_no+"</td><td>"+josn_obj[i].type_text+"</td><td><button type=\"button\" class=\"btn btn-success btn-xs btn-block\" onclick=\"javascript:set('"+josn_obj[i].car_no+"')\">选择</button></td></tr>";
                }
            }
        table += "</table>";

        main.html(table);

    }

    // 设定
    function set(val) {
        // 车辆
        // var target = $("#target").val();
        var now = $("#now").val();

        if(now == 0){
            $("#user_id").val(val);
        }else if(now == 1){
            // $("#target").val('');
            $("#car_no").val(val);
        }
        $('#myModal').modal('hide');
    }

</script>
@endsection





















