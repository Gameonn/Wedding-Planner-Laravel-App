@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Edit Vendor Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/vendor-listing/0/0">Vendor Listing</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/vendor-details/{{ $vendor_details[0]->user_id }}">Vendor Details</a>
            </li>
            <li class="active">
                <strong>Edit Vendor Details</strong>
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
                <h5>Edit Vendor Details</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-wrench"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#">Config option 1</a>
                        </li>
                        <li><a href="#">Config option 2</a>
                        </li>
                    </ul>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                {!! Form::open(array('url' => 'admin/vendor-details-edit', 'method' => 'post', 'class' => 'form-horizontal')) !!}
                    <input type="hidden" name="user_id" value="{{ $vendor_details[0]->user_id }}">
                    <div class="form-group"><label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="name" value="{{ $vendor_details[0]->name }}"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="email" value="{{ $vendor_details[0]->email }}"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Phone no</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="phone_no" value="{{ $vendor_details[0]->phone_no }}"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Business Name</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="business_name" value="{{ $vendor_details[0]->business_name }}"></div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Type</label>
                        <div class="col-sm-10">
                            <select class="form-control m-b" name="business_type">
                                @if($vendor_details[0]->business_type == "")
                                    <option>-----Select-----</option>
                                @else
                                    <option>{{ $vendor_details[0]->business_type }}</option>
                                @endif                                
                                @foreach($sub_business_list as $key2 => $value2)
                                    <option>{{ $value2->sub_business }}</option>
                                @endforeach                                   
                            </select>                            
                        </div>
                    </div>                
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10"> <textarea class="form-control" rows="3"  name="description">{{ $vendor_details[0]->description }}</textarea> </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Location</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="location" value="{{ $vendor_details[0]->location }}"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">City</label>
                        <div class="col-sm-10">
                            <select class="form-control m-b" name="city">
                                @if($vendor_details[0]->city == "")
                                    <option>-----Select-----</option>
                                @else
                                    <option>{{ $vendor_details[0]->city }}</option>
                                @endif                                
                                @foreach($city_list as $key2 => $value2)
                                    <option>{{ $value2->city_name }}</option>
                                @endforeach                                   
                            </select>                            
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Average cost</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="average_cost" value="{{ $vendor_details[0]->average_cost }}"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Vendor Rating</label>
                        <div class="col-sm-10"><input type="text" disabled class="form-control" name="vendor_rating" value="{{ $vendor_details[0]->vendor_rating }}"></div>
                    </div>
                    <div class="hr-line-dashed"></div>                    

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">                            
                            <a class="btn btn-white m-r-sm" href="{{ URL::to('/') }}/admin/vendor-details/{{ $vendor_details[0]->user_id }}" role="button">Cancel</a>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

  </div>

</div>

@stop