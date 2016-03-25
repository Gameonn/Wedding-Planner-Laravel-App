@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Sponsor Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li class="active">
                <strong>Sponsor Details</strong>
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
                  <h5>Add Vendor</h5>                                  
              </div>             

              <div class="ibox-content">                   
                {!! Form::open(array('url' => 'admin/add-sponsor', 'class' => 'form-horizontal')) !!}
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Vendors</label>
                      <div class="col-sm-10">
                        <select class="vendor-tag-select2 form-control" name="vendor_id">
                          <option value="" disabled selected>Select</option>
                          @foreach($all_vendor_listing as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->business_name }}</option >
                          @endforeach
                        </select>           
                      </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">                                                        
                            <button class="btn btn-primary" type="submit">Add</button>
                        </div>
                    </div>
                {!! Form::close() !!}                
              </div>

          </div>
        </div>

        <div class="col-lg-12">
          <div class="ibox float-e-margins">
              <div class="ibox-title">
                  <h5>Sponsor Listing </h5>                  
              </div>
              <div class="ibox-content">                  
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <thead>
                          <tr>

                              <th></th>
                              <th>Sr no</th>
                              <th colspan="2">Name</th>
                              <th>Email</th>                              
                              <th>Phone No.</th>
                              <th>Business Name</th>
                              <th>Business Type</th>
                              <th>Location</th>
                              <th>Average Cost</th>
                              <th>Status</th>
                              <th>Details</th>
                              <th>Delete</th>                         
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($vendor_listing as $key => $value)

                            <tr>                            
                              <td>
                                <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" checked="" class="i-checks" name="input[]" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div>
                              </td>
                              <td> {{ $key + 1 }} </td>
                              <td> 
                                @if(strpos($value->image,'graph.facebook.com') !== false)
                                  <div class="my-img-box"> <img alt="image" src="{{ $value->image }}"> </div> 
                                @else
                                  <div class="my-img-box"> <img alt="image" src="{{ $value->image }}/50"> </div> 
                                @endif                                
                              </td>
                              <td> {{ $value->name }} </td>
                              <td> {{ $value->email }} </td>
                              <td> {{ $value->phone_no }} </td>
                              <td> {{ $value->business_name }} </td>
                              <td> {{ $value->business_type }} </td>
                              <td> {{ $value->location }} </td>
                              <td> {{ $value->average_cost }} </td>
                              <td>  
                                @if($value->approved=='1')                                                                  
                                  <button type="button" class="btn btn-success btn-sm m-r-sm">Approved</button> 
                                @else                                  
                                  <button type="button" class="btn btn-warning btn-sm m-r-sm">Pending</button>
                                @endif
                              </td>
                              <td> 
                                <a class="btn btn-primary m-r-sm" href="{{URL::to('/')}}/admin/vendor-details/{{$value->id}}" role="button">Details</a> 
                              </td>
                              <td>                                 
                                  <button type="submit" class="btn btn-w-m btn-danger btn-sm btn-delete-sponsor" vendor-id="{{ $value->id }}">Delete</button> 
                              </td> 
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