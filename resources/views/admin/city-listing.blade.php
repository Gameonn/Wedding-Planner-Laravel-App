@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>City Listing</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li class="active">
                <strong>City Listing</strong>
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
              <h5>Add City</h5>                  
          </div>
          <div class="ibox-content">   
            {!! Form::open(array('url' => 'admin/add-city', 'method' => 'post', 'class' => 'form-horizontal')) !!}
                <div class="form-group">
                  <label class="col-sm-2 control-label">City Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="city_name" placeholder="City Name">
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label">State</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="state" placeholder="State">
                  </div>
                </div>                    

                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">                            
                        <a class="btn btn-white m-r-sm" href="#" role="button">Cancel</a>
                        <button class="btn btn-primary" type="submit">Add</button>
                    </div>
                </div>
            {!! Form::close() !!}
            <div class="hr-line-dashed"></div>                    
          </div>
        </div>
      </div>
      <div class="col-lg-12">
          <div class="ibox float-e-margins">
              <div class="ibox-title">
                  <h5>City Listing</h5>                  
              </div>              

              <div class="ibox-content">                           

                  <div class="table-responsive">
                      <table class="table table-striped">
                          <thead>
                          <tr>

                              <th></th>
                              <th>Sr no</th>
                              <th>City Name</th>    
                              <th>State</th>    
                              <th>Edit</th>
                              <th>Delete</th>                                                          
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($city_listing as $key => $value)

                            <tr>                            
                              <td>
                                <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" checked="" class="i-checks" name="input[]" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div>
                              </td>
                              <td> {{ $key +1 }} </td>                              
                              <td> {{ $value->city_name }} </td> 
                              <td> {{ $value->state }} </td> 
                              <td> 
                                  
                                  <button type="button" class="btn btn-primary my-city-edit-btn" data-toggle="modal" data-target="#myModal" city-id="{{ $value->city_id }}" city-name="{{ $value->city_name }}" state="{{ $value->state }}">
                                  Edit
                                  </button>
                                  
                                  <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content animated bounceInRight">
                                        <div class="modal-header">                                            
                                            <h4 class="modal-title">City</h4>                                            
                                        </div>

                                        {!! Form::open(array('url' => 'admin/edit-city', 'method' => 'post')) !!}
                                        <div class="place-modal-content-2">
                                          
                                        </div>
                                        {!! Form::close() !!}

                                      </div>
                                    </div>
                                  </div>
                              </td>
                              <td> 
                                {!! Form::open(array('url' => 'admin/remove-city', 'method' => 'post')) !!}
                                  <input type="hidden" name="city_id" value="{{ $value->city_id }}" >
                                  <button class="btn btn-warning btn-circle" type="submit"><i class="fa fa-times"></i></button> 
                                {!! Form::close() !!}
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