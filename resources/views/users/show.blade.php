<?php
    $date = new App\Helpers\Date;
    $auth = new App\Helpers\Auth;
?>
@extends('../nav')

@section('container')
<div id="top_left"  class="col-sm-3">
    <div class="panel panel-{{ $record->locked ? 'warning' : 'success' }}">
    <div class="panel-body">
        <ul class="list-unstyled">
        @if(isset($record))
        <li><h4>{{ $record->name }}</h4></li>
        <li><strong>状态:  </strong><span class="label label-{{ $record->locked ? 'warning' : 'success' }}">{{ $record->locked ? '锁定' : '正常' }}</span></li>
        <li><strong>工号:  </strong>{{ $record->work_id }}</li>
        <li><strong>性别:  </strong>{{ $record->gender_text }}</li>
        <li><strong>电话:  </strong>{{ $record->mobile }}</li>
        <li><strong>驾校:  </strong>{{ $record->branch_text }}</li>
        <li><strong>职位:  </strong>{{ $record->user_type_text }}</li>
        <li><strong>类型:  </strong>{{ $record->auth_type_text }}</li>
        <li><strong>创建人:  </strong>{{ $record->created_by_text }}</li>
        <li><strong>创建时间:  </strong>{{ $record->created_at }}</li>
        <li><strong>最近更新:  </strong>{{ $record->updated_at }}</li>
        <li><strong>备注:  </strong>{{ $record->content }}</li>
        </ul>
        @endif

        @if($auth->master($record->id) && !$auth->self($record->id) && $auth->admin())
            @if($record->locked)
                <a href="/user/unlock/{{ $record->id }}" class="btn btn-success  btn-sm"> 解锁 </a>
            @else
                <a href="/user/lock/{{ $record->id }}" class="btn btn-warning  btn-sm"> 锁定 </a>
            @endif
            <a href="/user/password_help/{{ $record->id }}" class="btn btn-danger  btn-sm">重置密码</a>
        @endif
        @if($auth->self($record->id) || ($auth->master($record->id) && $auth->admin()))
            <a href="/user/edit/{{ $record->id }}" class="btn btn-danger  btn-sm">信息修改</a>
        @endif

    </div>
</div>
</div>
<div class="col-sm-9">
    <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">财务记录</h3>
    </div>
    <div class="panel-body">
        @if(isset($finance) && count($finance))
        <caption>
            <div class="alert alert-info">
                统计信息
            </div>
        </caption>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>收/付</th>
                    <th>学员</th>
                    <th>项目</th>
                    <th>经手人</th>
                    <th>应收(付)</th>
                    <th>实收(付)</th>
                    <th>日期</th>
                </tr>
            </thead>
            
                @foreach($finance as $f)
                    <tr class="{{ $f->in ? 'default' : 'danger' }}">{{ $f->in ? '收 +' : '付 -' }}>
                        <td </td>
                        <td>{{ $f->customer_id_text }}</td>
                        <td>{{ $f->item_text }}</td>
                        <td>{{ $f->created_by_text }}</td>
                        <td>{{ $f->price }}</td>
                        <td>{{ $f->real_price }}</td>
                        <td>{{ date('Y-m-d', $f->date) }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            无交费信息
        @endif

    </div>
</div>

    {{-- 业务 --}}
    @if(isset($biz) && count($biz))
        @foreach($biz as $b)
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $b->licence_type_text.' - '.$b->class_type_text }}</h3>
                </div>
                <div class="panel-body">
                    <ul class="list-inline">
                    <li>开班</li>
                    <li>科目1</li>
                    <li>科目2</li>
                    <li>科目3</li>
                    <li>科目4</li>
                </ul>
            </div>
        
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            尚无业务信息
        </div>
    @endif
    {{-- end业务 --}}
</div>





@endsection