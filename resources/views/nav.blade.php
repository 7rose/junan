<?php
    $seek = new App\Helpers\Seek;
    $me = '<li><a href="/login">请登录</a></li>';
    if(Session::has('id')) {
        $auth = new App\Helpers\Auth;
        $pre = new App\Helpers\Pre;

        $id = Session::get('id');
        $record = DB::table('users')
                        ->leftJoin('branches', 'users.branch', '=', 'branches.id')
                        ->select('users.name', 'branches.text as branch_text')
                        ->where('users.id', $id)
                        ->first();
        $me = '<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        '.$record->name.'-'.$record->branch_text.' <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/doc"><span class="glyphicon glyphicon-book"></span>&nbsp&nbsp使用说明书</a></li>
                        <li><a href="/user/reset_password"><span class="glyphicon glyphicon-cog"></span>&nbsp&nbsp重设密码</a></li>
                        <li class="divider"></li>
                        <li><a href="/logout"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp安全退出</a></li>
                    </ul>
                </li>';
     }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>军安集团</title>
    <link rel="stylesheet" href="{{ URL::asset('node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <script src="{{ URL::asset('node_modules/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('junan/css/style.css') }}" >
</head>
<body>
    <!-- 导航 -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid"> 
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#example-navbar-collapse">
                <span class="sr-only">切换导航</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/"><img class="logo" src="{{ URL::asset('junan/images/logo.svg') }}"></a>
            <div class="btn-group">
    </div>
        </div>
        <div class="collapse navbar-collapse pull-right" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="{{ $seek->navClick('customer') ? 'active' : '' }}"><a href="/customer">学员</a></li>
                <li class="{{ $seek->navClick('user') ? 'active' : '' }}"><a href="/user">成员</a></li>
                <li class="{{ $seek->navClick('finance') ? 'active' : '' }}"><a href="/finance">财务</a></li>
                <li class="{{ $seek->navClick('filter') ? 'active' : '' }}"><a href="/filter">考务</a></li>

                <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        统计 <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/counter/finance"><span class="glyphicon glyphicon-usd"></span> 财务</a></li>
                        <li><a href="/counter/biz"><span class="glyphicon glyphicon-stats"></span> 业务</a></li>
                    </ul>
                </li>
                @if(isset($auth) && $auth->admin())
                    <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            导入Excel <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/import/user"><span class="glyphicon glyphicon-user"></span> 成员</a></li>
                            <li><a href="/import/class"><span class="glyphicon glyphicon-list-alt"></span> 开班花名册</a></li>
                        </ul>
                    </li>
                @endif
                {!! $me !!}
                @if(Session::has('id'))
                {!! $pre->navBranches() !!}
                @endif
            </ul>
        </div>
                
        </div>
    </nav>
    <!-- 顶部间距 -->
    <div style="height: 70px"></div>

    <!-- 全屏 -->
    @yield('content')

    <!-- 容器 -->
    <div class="container">
        @yield("container")
    </div>
</body>
<script>
    // ajax csrf
    $(function(){ 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        });
    });
</script>
</html>