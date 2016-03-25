@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Chat Operator Listing</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li class="active">
                <strong>Chat Operator Listing</strong>
            </li>            
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">  
  <div class="row">
      <div class="col-lg-12">
          <div class="ibox float-e-margins">
              <div class="ibox-title">
                  <h5>Chat Operator Listing </h5>                  
              </div>
              <div class="ibox-content">                  
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <thead>
                          <tr>

                              <th></th>
                              <th>Sr no</th>
                              <th>Name</th>                              
                              <th>Conversations</th>                              
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($chat_operator_listing as $key => $value)

                            <tr>                            
                              <td>
                                <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" checked="" class="i-checks" name="input[]" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div>
                              </td>
                              <td> {{ $key + 1 }} </td>                              
                              <td> {{ $value->username }} </td>                              
                              <td> <a class="btn btn-primary m-r-sm" href="{{URL::to('/')}}/admin/chat-operator-conversations/{{$value->id}}" role="button">Conversations</a> </td>                              
                            </tr>  

                          @endforeach
                                                  
                          </tbody>
                      </table>
                  </div>

              </div>
          </div>
      </div>

  </div>

</div>

@stop