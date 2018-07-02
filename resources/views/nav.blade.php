<?php
    $seek = new App\Helpers\Seek;
    $me = '<li><a href="/login">请登录</a></li>';
    if(Session::has('id')) {
        $auth = new App\Helpers\Auth;
        $pre = new App\Helpers\Pre;
        $conf = new App\Helpers\Config;

        $id = Session::get('id');
        $record = DB::table('users')
                        ->leftJoin('branches', 'users.branch', '=', 'branches.id')
                        ->select('users.name', 'branches.text as branch_text')
                        ->where('users.id', $id)
                        ->first();
        if(!$record){
            Session::flush();
            return redirect('/');
        }

        $config = '';

        if($auth->root()) {
            $config = $conf->confMenu();
        }
        
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
    <title>{{ Config::get('ginkgo')['name'] }}</title>
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
            <a href="/"><img class="logo" src="{{ URL::asset('images/'.Config::get('ginkgo')['logo_top'].'.svg') }}"></a>
            <div class="btn-group">
    </div>
        </div>
        <div class="collapse navbar-collapse pull-right" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                
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
                @if(isset($auth) && $auth->root())
                    <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            系统参数 <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            {!! $conf->confMenu() !!}
                            <li class="divider"></li>
                            <li><a href="/branch">分支机构</a></li>
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
    <div style="height: 50px"></div>

    <!-- 全屏 -->
    @yield('content')
    <div id="side_nav" class="container col-sm-1 side_nav_hight">
        <div class="wel-grid text-center side_ico">
            <a href="/customer">
              <img src="{{ URL::asset('images/ico/customer.svg') }}" class="img-circle panel-icon">
            </a>
              <h5>学员</h5>
        </div>

        <div class="wel-grid text-center side_ico">
            <a href="/user">
              <img src="{{ URL::asset('images/ico/user.svg') }}" class="img-circle panel-icon">
            </a>
              <h5>成员</h5>
        </div>

        <div class="wel-grid text-center side_ico">
            <a href="/finance">
              <img src="{{ URL::asset('images/ico/finance.svg') }}" class="img-circle panel-icon">
            </a>
              <h5>财务</h5>
        </div>

        <div class="wel-grid text-center side_ico">
            <a href="/filter">
              <img src="{{ URL::asset('images/ico/filter.svg') }}" class="img-circle panel-icon">
            </a>
              <h5>考务</h5>
        </div>

        <div class="wel-grid text-center side_ico">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="{{ URL::asset('images/ico/counter.svg') }}" class="img-circle panel-icon">
                        <ul class="dropdown-menu go_right">
                            <li><a href="/counter/finance"><span class="glyphicon glyphicon-usd"></span> 财务</a></li>
                            <li><a href="/counter/biz"><span class="glyphicon glyphicon-stats"></span> 业务</a></li>
                            <li><a href="/counter/lesson"><span class="glyphicon glyphicon-edit"></span> 考务</a></li>
                        </ul>
                </a>
            </div>
              <h5>统计</h5>
        </div>

    </div>
    <!-- 容器 -->
    <div class="container col-sm-11">
        <div style="height: 15px"></div>
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