@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Edit Vendor Extra Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/vendor-listing/0/0">Vendor Listing</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/vendor-details/{{ $user_id }}">Vendor Details</a>
            </li>
            <li class="active">
                <strong>Edit Vendor Extra Details</strong>
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
                <h5>Edit Vendor Extra Details</h5>                
            </div>
            <div class="ibox-content">

                {!! Form::open(array('url' => 'admin/vendor-extra-details-edit', 'method' => 'post', 'class' => 'form-horizontal')) !!}
                    <input type="hidden" name="user_id" class="user_id" value="{{ $user_id }}">

                    @foreach($vendor_extra_details as $key => $value)

                        <div class="extra-detail-cont">
                            <div class="form-group"><label class="col-sm-2 control-label"> Detail Name </label>
                                <div class="col-sm-9"><input type="text" class="form-control" name="detail_name_{{ $key }}" value="{{ $vendor_extra_details[$key]->detail_name }}"></div>
                                <div class="col-sm-1">
                                    <button class="btn btn-danger btn-delete-extra-details" type="button" detail-name="{{ $vendor_extra_details[$key]->detail_name }}" detail-desc="{{ $vendor_extra_details[$key]->detail_desc }}">
                                        <i class="fa fa-times"></i>
                                    </button>                            
                                </div>
                            </div>                        

                            <div class="form-group"><label class="col-sm-2 control-label"> Detail Description </label>
                                <div class="col-sm-9"><input type="text" class="form-control" name="detail_desc_{{ $key }}" value="{{ $vendor_extra_details[$key]->detail_desc }}"></div>
                            </div> 
                            <div class="hr-line-dashed"></div>                             
                        </div>                        

                    @endforeach                    

                    @if(empty($vendor_extra_details))
                        <div class="extra-detail-cont">
                            <div class="form-group"><label class="col-sm-2 control-label"> Detail Name </label>
                                <div class="col-sm-9"><input type="text" class="form-control" name="detail_name_0" value=""></div>
                                <div class="col-sm-1">                                                              
                                </div>
                            </div>                        

                            <div class="form-group"><label class="col-sm-2 control-label"> Detail Description </label>
                                <div class="col-sm-9"><input type="text" class="form-control" name="detail_desc_0" value=""></div>
                            </div> 
                            <div class="hr-line-dashed"></div>                             
                        </div>         
                    @endif

                    <div class="form-content-cont"></div>            
                    @if(!empty($vendor_extra_details))
                        <input type="hidden" class="curr_val" value="{{ $key }}" name="curr_val">        
                    @else
                        <input type="hidden" class="curr_val" value="0" name="curr_val">        
                    @endif                    

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">                            
                            <a class="btn btn-white m-r-sm" href="{{ URL::to('/') }}/admin/vendor-details/{{ $user_id }}" role="button">Cancel</a>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                            <div class="btn btn-warning btn-add-more">Add More +</div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

  </div>

</div>

@stop