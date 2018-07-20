@extends('nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                
                    <span class="glyphicon glyphicon-warning-sign"></span> 车辆管理

            </div>
            <div class="panel-body">
                @if(isset($records))
                    <form role="form" class="form-inline">
                {{ csrf_field() }}
                  
                  <div class="form-group">
                    <select class="form-control input-sm" id="type" required="required">
                        <option value="" selected="selected">-- 选择车型 --</option>
                    @foreach($car_types as $key=>$value)
                        <option value="{{ $key }}" > {{ $value }}</option>
                    @endforeach
                    </select>
                  </div>
                  <div class="form-group form-sm">
                    <label for="name"> + </label>
                    <input id="car_no" type="text" class="form-control input-sm" name="date_start" placeholder="请填写牌照号">
                  </div>

                  <a href="javascript:send()" class="btn btn-success btn-sm">+ 添加 </a>
                </form>
                <div style="height: 20px"></div>

                    @if(count($records))
                    <table class="table">
                        <thead>
                            <tr>
                              <th>牌号</th>
                              <th>车型</th>
                              <th>创建时间</th>
                            </tr>
                          </thead>
                        @foreach($records as $record)
                        <tr>
                            <td>
                            
                                <a href="/car/close/{{ $record->id }}" class="btn btn-xs btn-{{ $record->show ? 'success' : 'warning' }}">{{ $record->car_no }}</a>
                            
                            
                            </td>
                            <td>{{ $record->type_text }}</td>
                            <td>{{ $record->created_at ? $record->created_at : '系统初始' }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @else
                        <div class="alert alert-warning">无记录</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    {{-- 弹出窗口 --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                   提示
                </h4>
            </div>
            <div class="modal-body">
                <ol>
                    <li>注意: 为最大程度保证系统数据逻辑, 您无法自行撤销或者修改输入项目包括文字说明!!</li>
                    <li>您无法删除提交内容, 但可能通过点击标题来 "启用" 或者 "关闭"该项目</li>
                    <li>确定继续前,请务必检查文字正确性</li>
                    <li>确定继续前,请务必确认应用逻辑必要性</li>
                </ol>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" data-dismiss="modal">关闭</button>
                <a href="javascript:send()" class="btn btn-sm btn-info">我知道了, 继续提交</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->

    <script>
        function send() {
            var type = $("#type").val();
            var car_no = $("#car_no").val();

            type = type.replace(/^\s+|\s+$/g,"");
            car_no = car_no.replace(/^\s+|\s+$/g,"");

            if(type=='' || car_no=='') {
                alert("车型与牌号均不能为空!");
                return false;
            }

            var post_data = {'type': type, 'car_no': car_no};
            var post_url = '/car/add';

            $.post(
                post_url,
                post_data,
                function(message){
                    if(message != 200){
                        alert(message);
                        return false;
                    }
                    location.reload();
                    // parent.location.reload(); 

                    // $("#modal-msg").html(message);
                    // $("#claim_msg"+id).html("<span class=\"label label-info\">"+message+"</span>");
               }
            );
        }
    </script>
@endsection


















