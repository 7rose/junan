@extends('../nav')

@section('container')
<div class="welcome">
    <img class="doc-img" src="{{ URL::asset('junan/images/junan.svg') }}">
    <h3>军安集团综合管理系统用户说明</h3>

<div class="well doc-well">
    <h4>简介</h4>
    <ul>
        <li>本系统由军安集团授权上海翠薇智能开发, 所有权归军安集团。</li>
        <li>建议、意见可发送邮件至：<email>hi@viirose.com</email></li>
        <li>系统采用云端部署, 企业不需要建立数据中心; 使用全程https加密通信</li>
        <li>建议使用chrome浏览器操作</li>
    </ul>
    <h4>用户</h4>
    <ol>
        <li>权限设置: 
            <p><span class="label label-info">高级管理管理员</span>系统根用户,拥有最权限</p>
            <p><span class="label label-primary">管理员</span>管理员</p>
            <p><span class="label label-success">用户</span>基本系统用户, 在本文档中,未标明的均视为此类用户</p>
            <p><span class="label label-default">员工</span>无权操作系统, 但为业务相关者或者内部员工</p>
        </li>
        <li>状态: 
            <p class="alert alert-info">淡蓝色背景表示本人</p>
            <p class="alert alert-warning">黄色背景表示账号处于锁定状态, 解锁可以联系管理员, 管理自身账号锁定需要高级管理员解锁</p>
        </li>
        <li>操作: 
            <ul>
            <li>新成员: 用户级可以建立本单位员工; 管理员可以建立任何机构人员</li>
            <li>导出excel: 列表中实时的状态,即为保存结果, <span class="label label-info"> 注意:excel中的字段可能与显示内容不同</span></li>
            <li>锁定: 需要要管理员. 锁定用户,使其不能操作系统. 通常用于员工长期请假, 离职, 账号被盗等情况, 管理员可以锁定/解锁用户, 高级管理员可以锁定/解锁管理员</li>
            <li>重置密码: 需要要管理员. 重设密码规则请咨询管理员</span></li>
            <li>重设本人密码: 本人在登录以后,可以在导航栏下拉菜单中点击设置.</span></li>
            <li>安全退出: 不使用系统时建立使用退出清空缓存数据,保障业务安全.</span></li>
            </ul>
        </li>
        <li>业务: 
            <ul>
            <li>机构选择: 需要管理员或者军安集团总部人员. 普通用户为固定本机构, 当选择机构时, 管理员所得到工作界面和固定机构的完全相同</li>
            </ul>
        </li>
    </ol>
    <div class="alert alert-info">因企业业务逻辑属于商业秘密, 各模块操作及业务流实现均以现场培训为准. 军安也可能会根据需要对本系统和业务逻辑作出修改.</div>
</div>
    <span>2018&nbsp&copy&nbsp江苏军安驾培集团</span>
</div>
  
@endsection