@extends('../nav')

@section('container')
    @if(isset($all) && isset($new))
    <div class="col-md-8 col-md-offset-2">
       <div class="alert alert-success">
           <ol>
               <li>花名册中各人员必须连续排列,若检测到空白行,则空白行后无法导入</li>
               <li>若花名册中人员在系统中不存在,则根据花名册人员信息新建</li>
               <li>从花名册中新建的人员,没有财务信息,也没有财务信息归属分支</li>
               <li>管理员可以在导入后,对用户信息进行修正</li>
           </ol>
           <h4>共{{ $all }}条数据通过校验,可以写入开班数据库; 其中{{ $new }}条将被导入学员数据库</h4>


           @if($all == 0)
              <div class="alert alert-danger"> 
                没有通过校验的内容可以导入!
              </div>
           @else
              <a href="/import/class/save" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp写入数据库</a>
           @endif
              <a href="/import/class" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-remove"></span>&nbsp放弃并返回</a>&nbsp&nbsp

       </div>
    <div class="col-md-6 col-md-offset-3">
    @else
    <div class="alert alert-danger">
        缺少必要参数
    </div>
    @endif
@endsection