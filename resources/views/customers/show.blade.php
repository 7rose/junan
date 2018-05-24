<?php
    $date = new App\Helpers\Date;
    $auth = new App\Helpers\Auth;
?>
@extends('../nav')

@section('container')
<div id="top_left"  class="col-sm-3">
    <div class="panel panel-default">
    <div class="panel-body">
        <ul class="list-unstyled">
        <li><h4>{{ $record->name }}</h4></li>
        <li><strong>性别:  </strong>{{ $record->gender_text }}</li>
        <li><strong>年龄:  </strong>{{ $date->ageFromId($record->id_number) }}周岁</li>
        <li><strong>出生日期:  </strong>{{ $date->birthdayFromId($record->id_number) }}</li>
        <li><strong>身份证:  </strong>{{ $record->id_number }}</li>
        <li><strong>地址:  </strong>{{ $record->address }}</li>
        <li><strong>电话:  </strong>{{ $record->mobile }}</li>
        <li><strong>居住地:  </strong>{{ $record->location }}</li>
        <li><strong>创建人:  </strong>{{ $record->created_by_text }}</li>
        <li><strong>创建时间:  </strong>{{ $record->created_at }}</li>
        <li><strong>最近更新:  </strong>{{ $record->updated_at }}</li>
        <li><strong>备注:  </strong>{{ $record->content }}</li>
        </ul>
        {{-- 年龄限制 --}}
        @if($date->badBiz($record->id_number))
        <div class="alert alert-danger">
            <strong>此客户无法办理:</strong>
            <ul class="list-unstyled">
                {!! $date->badBiz($record->id_number) !!}
            </ul>
        </div>
        @endif
         <a href="/customer/biz/{{ $record->id }}" class="btn btn-success btn-sm">+ 新业务</a>&nbsp
        <a href="/finance/create/{{ $record->id }}" class="btn btn-warning btn-sm">$ 收付款</a>
        @if($auth->admin())
            <a href="/customer/edit/{{ $record->id }}" class="btn btn-danger btn-sm">修改</a>&nbsp
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
        @if(count($finance))
        <caption>
            <div class="alert alert-{{ $finance_info['rest'] > 0 || $finance_info['rest'] < 0 ? 'warning' : 'success' }}">
                <ul class="list-inline">
                    <li>应付: {{ $finance_info['to_out'] }}</li>
                    <li>实付: {{ $finance_info['out'] }}</li>
                    <li>应收: {{ $finance_info['to_in'] }}</li>
                    <li>实收: {{ $finance_info['in'] }}</li>
                    <li>结果: {{ $finance_info['rest'] }}</li>
                </ul>
            </div>
        </caption>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>收付</th>
                    <th>驾校</th>
                    <th>项目</th>
                    <th>经手人</th>
                    <th>推荐人</th>
                    <th>应收(付)</th>
                    <th>实收(付)</th>
                    <th>日期</th>
                </tr>
            </thead>
            
                @foreach($finance as $f)
                    <tr class="{{ $f->in ? 'default' : 'danger' }}">
                        <td>{{ $f->in ? '+' : '-' }}</td>
                        <td>{{ $f->branch_text }}</td>
                        <td>{{ $f->item_text }}</td>
                        <td>{{ $f->created_by_text }}</td>
                        <td>{{ $f->user_id_text }}</td>
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
    @if(count($biz))
        @foreach($biz as $b)
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $b->licence_type_text.' - '.$b->class_type_text.' ['.explode('(', $b->class_branch_text)[0].$b->class_no.'期]' }}</h3>
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