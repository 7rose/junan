@extends('../nav')

@section('container')
<div class="welcome">
    <img class="doc-img" src="{{ URL::asset('junan/images/'.config('ginkgo.logo').'.svg') }}">
    <h3>{{ Config::get('ginkgo')['name'] }}管理系统</h3>
    @if(config('ginkgo.name') == '军安集团')
    <p><a href="{{ URL::asset('junan/files/doc.pdf') }}" class="btn btn-sm btn-success">下载使用说明书</a></p>
    @else
    <p><a class="btn btn-sm btn-success" disabled="disabled">测试号无法下载使用说明书</a></p>
    @endif

<div class="well doc-well">
    <h4>用户示例</h4>
    <ol>
        <li>权限设置: 
            <p><span class="label label-info">高级管理员</span>系统根用户,拥有最高权限</p>
            <p><span class="label label-primary">管理员</span>管理员</p>
            <p><span class="label label-success">用户</span>基本系统用户, 在本文档中,未标明的均视为此类用户</p>
            <p><span class="label label-default">员工</span>无权操作系统, 但为业务相关者或者内部员工</p>
        </li>
        <li>状态: 
            <p class="alert alert-info">淡蓝色背景表示本人</p>
            <p class="alert alert-warning">黄色背景表示账号处于锁定状态, 解锁可以联系管理员, 管理员自身账号锁定需要高级管理员解锁</p>
        </li>
    </ol>
</div>
    <span>2018&nbsp&copy&nbsp{{ config('ginkgo.copy_right') }}</span>
</div>
  
@endsection