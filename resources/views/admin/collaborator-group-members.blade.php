@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{ $collaborator_group_members[0]->group_name }} Members</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/collaborator-group-listing/0/0">Collaborator Group Listing</a>
            </li>
            <li class="active">
                <strong>{{ $collaborator_group_members[0]->group_name }} Members</strong>
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
                  <h5>{{ $collaborator_group_members[0]->group_name }} Members</h5>                  
              </div>
              <div class="ibox-content">                                  
                <div class="row">
                  @foreach($collaborator_group_members as $key => $value)
                    <div class="col-lg-4">
                        <div class="contact-box">
                            <!-- <a href="profile.html"> -->
                              <div class="col-sm-4">
                                  <div class="text-center my-img-box-4">
                                    @if(strpos($value->user_image,'graph.facebook.com') !== false)
                                      <img alt="image" class="m-t-xs img-responsive" src="{{ $value->user_image }}">
                                    @else
                                      <img alt="image" class="m-t-xs img-responsive" src="{{ $value->user_image }}/100">
                                    @endif                                                      
                                      <div class="m-t-xs font-bold"></div>
                                  </div>
                              </div>
                              <div class="col-sm-8">
                                  <h3><strong>{{ $value->user_name }}</strong></h3>
                                  <h5>Phone No</h5>
                                  <p>{{ $value->user_phone_no }}</p>     
                              </div>
                              <div class="clearfix"></div>
                            <!-- </a> -->
                        </div>
                    </div>
                  @endforeach                    
                </div>
              </div>
          </div>
      </div>

  </div>

</div>

@stop