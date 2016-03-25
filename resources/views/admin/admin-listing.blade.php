@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Operator Listing</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li class="active">
                <strong>Operator Listing</strong>
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
                  <h5>Add Operator </h5>                  
              </div>              
              <div class="ibox-content">   
                {!! Form::open(array('url' => 'admin/add-operator', 'method' => 'post', 'class' => 'form-horizontal')) !!}
                    <div class="form-group">
                      <label class="col-sm-2 control-label">Operator User Name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="operator_username" placeholder="Operator Username">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">Password</label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">Confirm Password</label>
                      <div class="col-sm-10">
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-2 control-label">Operator Role</label>
                      <div class="col-sm-10">
                        <select class="form-control m-b" name="operator_role">
                            <option value="" disabled selected>Select</option>                            
                            <option value="chat_operator">chat_operator</option>
                            <option value="data_operator">data_operator</option>                            
                        </select>
                      </div>
                    </div>

                    @if(Session::get('message') !== null)
                      <div class="col-sm-12">
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ Session::get('message') }}  
                        </div>
                      </div>
                    @endif

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">                                                        
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
                  <h5>Operator Listing </h5>                  
              </div>              
              <div class="ibox-content">                  
                  <div class="table-responsive">
                      <table class="table table-striped">
                          <thead>
                          <tr>

                              <th></th>
                              <th>Sr no</th>
                              <th>Name</th>   
                              <th>Role</th>   
                              <th>Edit</th>                                       
                          </tr>
                          </thead>
                          <tbody>
                          @foreach($admin_listing as $key => $value)

                            <tr>                            
                              <td>
                                <div class="icheckbox_square-green checked" style="position: relative;"><input type="checkbox" checked="" class="i-checks" name="input[]" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div>
                              </td>
                              <td> {{ $key + 1 }} </td>
                              <td> {{ $value->username }} </td>  
                              <td>  
                                @if($value->user_role=='chat_operator')                                
                                  <button type="button" class="btn btn-primary btn-sm m-r-sm">Chat Operator</button>  
                                @else                                       
                                    <button type="button" class="btn btn-warning btn-sm m-r-sm">Data Operator</button>  
                                @endif
                              </td>
                              <td> 
                                <!-- <button type="button" class="btn btn-danger btn-sm m-r-sm">Edit</button>   -->

                                <button type="button" class="btn btn-danger operator-edit-btn" data-toggle="modal" data-target="#operatorEditModal" operator-id="{{ $value->id }}" operator-username="{{ $value->username }}">
                                  Edit
                                </button>
                                  
                                <div class="modal inmodal" id="operatorEditModal" tabindex="-1" role="dialog" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content animated bounceInRight">
                                      <div class="modal-header">                                            
                                          <h4 class="modal-title">Operator</h4>                                            
                                      </div>

                                      {!! Form::open(array('url' => 'admin/edit-operator', 'method' => 'post', 'class' => 'edit-operator-form')) !!}
                                      <div class="place-modal-content-2">
                                        
                                      </div>
                                      {!! Form::close() !!}

                                    </div>
                                  </div>
                                </div>
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