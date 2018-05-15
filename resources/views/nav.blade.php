<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>军安集团</title>
    <link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
            <a class="navbar-brand" href="#">军安</a>
        </div>
        <div class="collapse navbar-collapse" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">学员</a></li>
                <li><a href="#">员工</a></li>
                <li><a href="#">财务</a></li>
                <li><a href="#">分支机构</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        钟艳 <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#"><span class="glyphicon glyphicon-cog"></span>&nbsp&nbsp系统配置</a></li>
                        <li class="divider"></li>
                        <li><a href="#"><span class="glyphicon glyphicon-off"></span>&nbsp&nbsp安全退出</a></li>
                    </ul>
                </li>
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
</html>