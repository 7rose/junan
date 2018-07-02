@extends('../nav')

@section('container')
<div class="welcome">
    <img class="welcome-img" src="{{ URL::asset('images/junan.svg') }}">
    <h3>{{ Config::get('ginkgo')['name'] }}管理系统</h3>
    <span>2018&nbsp&copy&nbsp{{ Config::get('ginkgo')['copy_right'] }}</span>
</div>
  
@endsection