@extends('../nav')

@section('container')
<div class="welcome">
    <img class="welcome-img" src="{{ URL::asset('images/'.config('ginkgo.logo').'.svg') }}">
    <h3>{{ config('ginkgo.name')}}管理系统</h3>
    <span>2018&nbsp&copy&nbsp{{ config('ginkgo.copy_right') }}</span>
</div>
  
@endsection