@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Vendor Businesses</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li class="active">
                <strong>Vendor Businesses</strong>
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
                  <h5>Add Vendor Business</h5>                  
              </div>             

              <div class="ibox-content">   
                {!! Form::open(array('url' => 'admin/add-business', 'method' => 'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Business Name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="business_name" placeholder="Business Name">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">Sub Categories</label>
                      <div class="col-sm-10"> 
                        <ul id="myTags">
                               
                        </ul>
                      </div>
                    </div> 

                    <div class="form-group">
                      <label class="col-sm-2 control-label">Image</label>   
                      <div class="col-sm-6">
                        <input type="file" class="filestyle" name="image"> 
                      </div>
                    </div>    

                    @if(Session::get('message') !== null)
                      @if(Session::get('message') === 1)
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            Successfully added a category.
                        </div>
                      @else
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{Session::get('message')}}
                        </div>
                      @endif                      
                    @endif                                    

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">                            
                            <!-- <a class="btn btn-white m-r-sm" href="#" role="button">Cancel</a> -->
                            <button class="btn btn-primary" type="submit">Add</button>
                        </div>
                    </div>
                {!! Form::close() !!}
                <div class="hr-line-dashed"></div>                    
              </div>

              <div class="ibox-title">
                  <h5>Vendor Business Listing </h5>                  
              </div>

              <div class="ibox-content">                           

                  <div class="table-responsive">
                      <table class="table table-striped">
                          <thead>
                          <tr>

                              <th></th>
                              <th>Sr no</th>
                              <th colspan="2">Business</th>
                              <th colspan="2">Sub Category</th>
                              <th>Edit</th>                              
                              <th>Delete</th>                                                          
                          </tr>
                          </thead>
                          <tbody>
                          @if(!empty($businesses))
                            @foreach($businesses as $key => $value)

                              <tr>                            
                                <td>
                                  <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" checked="" class="i-checks" name="input[]" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div>
                                </td>
                                <td> {{ $key +1 }} </td>                             
                                <td> 
                                  <div class="my-img-box-3"> <img alt="image" src="{{ $value['business_image'] }}/100"> </div> 
                                </td> 
                                <td> {{ $value['business_name'] }} </td>                               
                                <td colspan="2">                                 

                                  @foreach($value['sub_business'] as $key2 => $value2) 

                                    @if(!empty($value2['sub_business_name']))
                                    
                                      {!! Form::open(array('url'=>'admin/delete-sub-business', 'class'=>'pull-left')) !!}
                                        <input type="hidden" name="sub_business_id" value="{{$value2['sub_business_id']}}">
                                        <span class="label label-warning my-label">
                                          {{ $value2['sub_business_name'] }}
                                          <button class="btn btn-danger btn-circle my-delete-btn" type="submit">
                                            <i class="fa fa-times"></i>
                                          </button>
                                        </span>
                                      {!! Form::close() !!}

                                    @endif                                  

                                  @endforeach                                                                 

                                </td> 
                                <td> 
                                    
                                    <button type="button" class="btn btn-primary my-business-edit-btn" data-toggle="modal" data-target="#myModal" business-id="{{ $value['business_id'] }}" business-name="{{ $value['business_name'] }}" sub-business-string="{{ $value['sub_business_string'] }}">
                                    Edit
                                    </button>
                                    
                                    <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content animated bounceInRight">
                                          <div class="modal-header">                                            
                                              <h4 class="modal-title">Business</h4>                                            
                                          </div>

                                          {!! Form::open(array('url' => 'admin/edit-business', 'class' => 'business-edit-form', 'method' => 'post', 'enctype'=>'multipart/form-data')) !!}
                                          <div class="place-modal-content">
                                            
                                          </div>
                                          {!! Form::close() !!}

                                        </div>
                                      </div>
                                    </div>
                                </td>                              
                                <td> 
                                  {!! Form::open(array('url' => 'admin/delete-business', 'class' => 'delete-business-form', 'method' => 'post')) !!}
                                    <input type="hidden" name="business_id" class="business-id" value="{{ $value['business_id'] }}" >
                                    <button class="btn btn-danger btn-circle" type="submit"><i class="fa fa-times"></i></button> 
                                  {!! Form::close() !!}
                                </td> 
                              </tr>  

                            @endforeach
                          @endif  
                                                  
                          </tbody>
                      </table>
                  </div>

              </div>
          </div>
      </div>

  </div>

</div>

@stop