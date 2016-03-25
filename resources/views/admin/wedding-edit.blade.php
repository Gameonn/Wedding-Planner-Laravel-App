@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Edit Wedding</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/wedding-listing/0/0">Wedding Listing</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/wedding-details/{{ $wedding_details[0]->id }}">Wedding Details</a>
            </li>
            <li class="active">
                <strong>Edit Wedding</strong>
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
                <h5>Edit <small>wedding.</small></h5>                
            </div>
            <div class="ibox-content">
              <div class="row">
                {!! Form::open(array('url' => 'admin/wedding-edit', 'method' => 'post', 'class' => 'form-horizontal')) !!}

                    <?php $message = Session::get('message') ?>
                    @if(isset($message))                       
                      <div class="col-sm-12">
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ $message }}   
                        </div>
                      </div>               
                    @endif     

                    <input type="hidden" name="wedding_id" value="{{ $wedding_details[0]->id }}">
                    <input type="hidden" name="user_id" value="0">
                    <div class="form-group"><label class="col-sm-2 control-label">Wedding Name</label>
                        <div class="col-sm-10"> <input type="text" class="form-control" name="wedding_name" value="{{ $wedding_details[0]->name }}"> </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Wedding Description</label>
                        <div class="col-sm-10"> <textarea class="form-control" rows="3" name="wedding_description">{{ $wedding_details[0]->description }}</textarea> </div> 
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-md-6">                                  
                                  <label class="col-sm-2 control-label">Date</label>
                                  <div class="col-sm-10">
                                    <input type="date" name="wedding_date" class="form-control" value="{{ $wedding_details[0]->date }}">
                                  </div>                                                                
                                </div>
                                <div class="col-md-6">
                                  <label class="col-sm-2 control-label">Type</label>
                                  <div class="col-sm-10">
                                    <select class="form-control" name="wedding_type">
                                      <option>{{ $wedding_details[0]->wedding_type }}</option>            
                                      @foreach($wedding_type_list as $key => $value)
                                        <option>{{ $value->wedding_type }}</option>
                                      @endforeach                                      
                                    </select>
                                  </div>                                  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">Location</label>
                        <div class="col-sm-10"> <input type="text" class="form-control" name="wedding_location" value="{{ $wedding_details[0]->location }}"> </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                    <div class="form-group"><label class="col-sm-2 control-label">City</label>
                        <div class="col-sm-10"> 
                          <select class="form-control" name="wedding_city">
                            <option>{{ $wedding_details[0]->city }}</option>            
                            @foreach($city_list as $key2 => $value2)
                              <option>{{ $value2->city_name }}</option>
                            @endforeach                                      
                          </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">                            
                            <a class="btn btn-white m-r-sm" href="{{ URL::to('/') }}/admin/wedding-listing/0" role="button">Cancel</a>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </div>
                    </div>
                                      
                {!! Form::close() !!}
              </div>
            </div>
        </div>
    </div>

  </div>

</div>

@stop