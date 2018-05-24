@extends('../nav')

@section('container')
    @if(isset($ok_list) && isset($ignore_list))
    <div class="col-md-8 col-md-offset-2">
       <div class="alert alert-{{ count($ignore_list) ? 'warning' : 'success' }}">
           <ol>
               <li><strong>系统安全原则:</strong> 导入项中不可包括管理员/用户等授权信息,若填写相关信息也将被系统自动忽略</li>
               <li><strong>管理安全原则:</strong> 若导入项中员工类型输入错误或者空白,则自动归入员工类型"其他"中</li>
               <li><strong>合理性:</strong> 若导入项姓名中包含空格,将被自动去除</li>
               <li>若导入项中员工性别输入错误或者空白,则自动标记为"男"</li>
               <li>管理员可以在导入后,对用户信息进行修改和授权以满足使用要求</li>
           </ol>
           <h4>共{{ count($ok_list)+count($ignore_list) }}条数据; 通过校验: {{ count($ok_list) }}条, 因手机号与数据库记录重复被忽略的{{ count($ignore_list) }}条:</h4>
           @if(count($ignore_list))
           <ol>
               @foreach($ignore_list as $key)
               <li>{{ $key['name'].' - '.$key['mobile'] }}</li>
               @endforeach
           </ol>
           @endif

           @if(!count($ok_list))
              <div class="alert alert-danger"> 
                没有通过校验的内容可以导入!
              </div>
           @else
              <a href="/user/import/save" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp写入数据库</a>
           @endif
              <a href="/user/import" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-remove"></span>&nbsp放弃并返回</a>&nbsp&nbsp

       </div>
    <div class="col-md-6 col-md-offset-3">
    @else
    <div class="alert alert-danger">
        缺少必要参数
    </div>
    @endif
@endsection