@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Vendor Listing</h2>
        <ol class="breadcrumb">
            <li>
                <a href="index.html">Home</a>
            </li>
            <li class="active">
                <strong>Vendor Listing</strong>
            </li>            
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight"> 

    <div class="row">
      <div class="col-md-12">
        
        <div class="ibox float-e-margins">
          <div class="ibox-title">
              <h5>Conversations</h5>              
          </div>
          <div class="ibox-content">

              <div>
                  <div class="feed-activity-list">

                    @foreach($messages as $key => $value)
                    <div class="feed-element">
                        <a href="{{ URL::to('/') }}/admin/conceirge/0/{{ $value->user_id }}" class="pull-left">
                            <img alt="image" class="img-circle" src="{{ $value->image }}">
                        </a>
                        <div class="media-body">
                            <small class="pull-right">{{ $value->time_since }}</small>
                            <a class="message-author" href="{{ URL::to('/') }}/admin/conceirge/{{ $admin_id }}/{{ $value->user_id }}"> 
                            @if($value->user_name == "")
                              {{ $value->user_phone_no }} 
                            @else
                              {{ $value->user_name }} 
                            @endif                            
                            </a>
                            <small class="text-muted">{{ $value->time }} - {{ $value->date }}</small>
                            <div class="well">
                                {{ $value->message }}
                            </div>
                        </div>
                    </div>  
                    @endforeach                                                                                    
                      
                  </div>                  
              </div>

          </div>
      </div>

      </div>
    </div>

</div>

@stop