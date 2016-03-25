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
        
        <div class="ibox chat-view">

          <div class="ibox-title">Image View</div>

          <div class="ibox-content">
            <div class="image-cont">
              <img src="{{ $response[0]['image'] }}">              
            </div>            
            <div class="my-cont">                         

              @if(!empty($response[0]['vendor_tagged']) && $response[0]['vendor_tagged'][0]['business_name'] != "")

                @foreach($response[0]['vendor_tagged'] as $key => $value)                                  
                  <a href="{{ URL::to('/') }}/admin/vendor-details/{{ $value['vendor_id'] }}" role="button" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="{{ $value['business_type'] }}">{{ $value['business_name'] }}</a>
                @endforeach

              @endif
            </div>
          </div>

        </div>

        <div class="ibox chat-view">

          <div class="ibox-title">Image View</div>

          <div class="ibox-content" style="height: 120px;">

            <div class="my-cont">
              {!! Form::open(array('url' => 'admin/tag-vendors')) !!}
                <div class="col-sm-2">
                  <label class="col-sm-2 control-label">Tag Vendors</label>
                </div>
                <div class="col-xs-10">                
                    <div class="form-group">
                      <select class="vendor-tag-select2 form-control" multiple="multiple" name="vendor_ids[]">
                        @foreach($vendor_listing as $key => $value)
                          <option value="{{ $value->user_id }}">{{ $value->business_name }}</option >
                        @endforeach
                      </select>           
                    </div>                                    
                </div>                
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">                        
                        <input type="hidden" name="image_id" value="{{ $image_id }}" >
                        <button type="submit" class="btn btn-primary">Add</button>
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