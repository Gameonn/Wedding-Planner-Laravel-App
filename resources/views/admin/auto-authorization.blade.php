@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Auto Authorization</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li class="active">
                <strong>Auto Authorization</strong>
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
                <h5>Auto Authorization</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-wrench"></i>
                    </a>                    
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
              <div class="row">
                {!! Form::open(array('url' => 'admin/change-authorization-status', 'method' => 'post', 'class' => 'form-horizontal')) !!}
                    
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Change Auto Authorization</label>
                        <div class="col-sm-4">  
                            @if($auto_authorize_data->status == 0)
                                <input type="hidden" name="status" value="1">
                                <button type="submit" class="btn btn-w-m btn-default">Off</button>
                            @else
                                <input type="hidden" name="status" value="0">
                                <button type="submit" class="btn btn-w-m btn-primary">On</button>
                            @endif                            
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>                    
                                      
                {!! Form::close() !!}
              </div>
            </div>
        </div>
    </div>

  </div>

</div>

@stop