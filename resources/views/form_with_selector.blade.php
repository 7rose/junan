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

    // 添加按钮
    function add_btn() {
        var btn = "<a class=\"btn btn-info btn-sm\" href=\"javascript:open()\">选择</a>";
        $("#user_id_selector").append(btn);
    }

    // 准备
    function pre() {
        var input = "<input type=\"text\" class=\"form-control\" id=\"key_val\" placeholder=\"请输入工号,手机号,姓名..\">";
        $("#modal_input").html(input);
    }

    // 打开
    function open() {
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

        var post_url = "/user/ajax/selector";
        var post_data = {key: tirm_val};

        $.post(
            post_url,
            post_data,
            function(json){
                // console.log(json);
                json_ex(json)

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

        var table = "<table class=\"table table-striped\">";
        for (var i = josn_obj.length - 1; i >= 0; i--) {
            table += "<tr><td>"+josn_obj[i].work_id+"</td><td>"+josn_obj[i].name+"</td><td>"+josn_obj[i].mobile+"</td><td><button type=\"button\" class=\"btn btn-success btn-xs btn-block\" onclick=\"javascript:set("+josn_obj[i].work_id+")\">选择</button></td></tr>";
        }
        table += "</table>";

        main.html(table);

    }

    // 设定
    function set(work_id) {
        $("#user_id").val(work_id);
        $('#myModal').modal('hide');
    }

</script>
@endsection





















