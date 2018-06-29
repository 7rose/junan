@extends('nav')

@section('container')
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                
                    <span class="glyphicon glyphicon-warning-sign"></span> 分支机构

            </div>
            <div class="panel-body">
                @if(isset($records))
                    <div class="input-group input-group-sm">
                        <input type="text" id="text" class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-info" type="button" data-toggle="modal" data-target="#myModal">+ 新机构</button>
                        </span>
                    </div><!-- /input-group -->

                    @if(count($records))
                    <table class="table">
                        <thead>
                            <tr>
                              <th>机构</th>
                              <th>创建时间</th>
                            </tr>
                          </thead>
                        @foreach($records as $record)
                        <tr>
                            <td>
                            @if($record->id != 1)
                                <a href="/branch/close/{{ $record->id }}" class="btn btn-xs btn-{{ $record->show ? 'success' : 'warning' }}">{{ $record->text }}</a>
                            @else
                                {{ $record->text }}
                            @endif
                            </td>
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
            var text_val = $("#text").val();

            var text_input = text_val.replace(/^\s+|\s+$/g,"");

            if(text_input=='') {
                alert("输入内容不能为空!");
                return false;
            }

            var post_data = {'text': text_input};
            var post_url = '/branch/add';

            $.post(
                post_url,
                post_data,
                function(message){
                    location.reload();
                    // $("#modal-msg").html(message);
                    // $("#claim_msg"+id).html("<span class=\"label label-info\">"+message+"</span>");
               }
            );
        }
    </script>
@endsection


















