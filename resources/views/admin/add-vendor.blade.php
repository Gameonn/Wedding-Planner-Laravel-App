@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Add Vendor Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>                        
            <li class="active">
                <strong>Add Vendor Details</strong>
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
                <h5>Add Vendor Details</h5>                
            </div>
            <div class="ibox-content">

                {!! Form::open(array('url' => 'admin/add-vendor', 'method' => 'post', 'class' => 'form-horizontal')) !!}
                    
                    <div class="form-group"><label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="name"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="email"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-10"><input type="password" class="form-control" name="password"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Phone no</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="phone_no"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Business Name</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="business_name"></div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Business Type</label>
                        <div class="col-sm-10">
                            <select class="form-control m-b" name="business_type">                                
                                <option selected disabled>-----Select-----</option>                                
                                @foreach($sub_business_list as $key2 => $value2)
                                    <option>{{ $value2->sub_business }}</option>
                                @endforeach 
                            </select>                            
                        </div>
                    </div>                
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10"> <textarea class="form-control" rows="3"  name="description"></textarea> </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Location</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="location"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">City</label>
                        <div class="col-sm-10">
                            <select class="form-control m-b" name="city">                                
                                <option selected disabled>-----Select-----</option>                                
                                @foreach($city_list as $key2 => $value2)
                                    <option>{{ $value2->city_name }}</option>
                                @endforeach                                   
                            </select>                            
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Average cost</label>
                        <div class="col-sm-10"><input type="text" class="form-control" name="average_cost"></div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    @if(Session::get('message') !== null)
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ Session::get('message') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">                            
                            <a class="btn btn-white m-r-sm" href="#" role="button">Cancel</a>
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