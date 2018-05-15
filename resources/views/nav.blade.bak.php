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

<nav class="navbar navbar-default" role="navigation">
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
<div class="container">
    <ul id="myTab" class="nav nav-tabs">
    <li class="active">
        <a href="#list" data-toggle="tab">列表</a>
    </li>
    <li><a href="#seek" data-toggle="tab">查询</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active" id="list">
        <table class="table table-hover">
        <caption>
            <a href="#" class="btn btn-sm btn-default">+ 新学员</a>&nbsp&nbsp
            <a href="#" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-th-list"></span>&nbsp&nbsp导出excel</a>&nbsp&nbsp
        </caption>
        <thead>
            <tr>
                <th>姓名</th>
                <th>手机号</th>
                <th>驾校</th>
                <th>进度</th>
                <th>报名日期</th>
                <th>身份证号</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>张三国</td>
                <td>13870000000</td>
                <td>鸿远</td>
                <td>科目3:已报名</td>
                <td>2018-3-12</td>
                <td>320823198017264563</td>
            </tr>
            <tr>
                <td>Sachin</td>
                <td>Mumbai</td>
                <td>Mumbai</td>
                <td>400003</td>
                <td>400003</td>
                <td>400003</td>
            </tr>
            <tr>
                <td>Uma</td>
                <td>PunePune</td>
                <td>411027</td>
                <td>411027</td>
                <td>411027</td>
                <td>411027</td>
            </tr>
        </tbody>
        </table>
    </div>
    <div class="tab-pane fade" id="seek">
        <p>iOS 是一个由苹果公司开发和发布的手机操作系统。最初是于 2007 年首次发布 iPhone、iPod Touch 和 Apple 
            TV。iOS 派生自 OS X，它们共享 Darwin 基础。OS X 操作系统是用在苹果电脑上，iOS 是苹果的移动版本。</p>
    </div>
</div>

    
    
</div>
<script>
    $(function () {
        $('#myTab li:eq(1) a').tab('show');
    });
</script>
</body>
</html>