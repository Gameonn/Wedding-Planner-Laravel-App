@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Vendor Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/vendor-listing/0/0">Vendor Listing</a>
            </li>
            <li class="active">
                <strong>Vendor Details</strong>
            </li>            
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight"> 

    @if(count($vendor_portfolio_images)>=3)       
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                
                <div id="carousel-example-generic" class="carousel slide my-crousel" data-ride="carousel">
                  <!-- Indicators -->
                  <ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                  </ol>

                  <!-- Wrapper for slides -->
                  <div class="carousel-inner" role="listbox">                    

                    <div class="item active">
                      <div class="carousel-img-box">
                        <img src="{{ $vendor_portfolio_images[0]->image }}" alt="1">  
                      </div>                      
                      <div class="carousel-caption">
                        Image 1
                      </div>
                    </div>

                    <div class="item">
                      <div class="carousel-img-box">
                        <img src="{{ $vendor_portfolio_images[1]->image }}" alt="1">  
                      </div>                      
                      <div class="carousel-caption">
                        Image 1
                      </div>
                    </div>

                    <div class="item">
                      <div class="carousel-img-box">
                        <img src="{{ $vendor_portfolio_images[2]->image }}" alt="1">  
                      </div>                      
                      <div class="carousel-caption">
                        Image 1
                      </div>
                    </div>
                                                     
                  </div>

                  <!-- Controls -->
                  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                  </a>
                  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                  </a>
                </div>

            </div>
        </div>
    </div>
    @endif    

    <div class="row">
      <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Vendor Detail</h5>
            </div>
            <div>
                <!-- <div class="ibox-content no-padding border-left-right">
                    <div class="my-img-box-2">
                        @if(isset($vendor_portfolio_images[0]->image))
                            <img alt="image" class="img-responsive" src="{{ $vendor_portfolio_images[0]->image }}">
                        @else
                            <img alt="image" class="img-responsive" src="{{ URL::to('/') }}/images/default.jpg">
                        @endif
                    </div>                
                </div> -->
                <div class="ibox-content profile-content">

                    <h4><strong>Name</strong></h4>
                    <p> {{$vendor_details[0]->name}} </p>

                    <h4><strong>Email</strong></h4>
                    <p> {{$vendor_details[0]->email}} </p>

                    <h4><strong>Phone</strong></h4>
                    <p> {{$vendor_details[0]->phone_no}} </p>

                    <h4><strong>Role</strong></h4>
                    @if($vendor_details[0]->user_role == 0)
                      <p> User </p>
                    @elseif($vendor_details[0]->user_role == 1)
                      <p> Vendor </p>
                    @else
                      <p> Collaborator </p>
                    @endif

                    <h4><strong>Status</strong></h4>
                    @if($vendor_details[0]->approved == 1)
                      <p> Approved </p>
                    @else
                      <p> Pending </p>
                    @endif
                    
                    <h4><strong>Business Name</strong></h4>
                    <p> {{$vendor_details[0]->business_name}} </p>

                    <h4><strong>Business Type</strong></h4>
                    <p> {{$vendor_details[0]->business_type}} </p> 

                    <h4><strong>Description</strong></h4>
                    <p> {{$vendor_details[0]->description}} </p>

                    <h4><strong>Location</strong></h4>
                    <p> {{$vendor_details[0]->location}} </p>

                    <h4><strong>City</strong></h4>
                    <p> {{$vendor_details[0]->city}} </p>

                    <h4><strong>Average Cost</strong></h4>
                    <p> {{$vendor_details[0]->average_cost}} </p>

                    <h4><strong>Rating</strong></h4>
                    <p> {{$vendor_details[0]->vendor_rating}} </p>

                    <div class="user-button">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-6">
                                <td> <a class="btn btn-warning m-r-sm my-btn" href="{{URL::to('/')}}/admin/vendor-details-edit/{{$vendor_details[0]->user_id}}" role="button">Edit</a> </td>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Vendor Extra Detail</h5>
            </div>
            <div>            
                <div class="ibox-content profile-content">

                  @foreach($vendor_extra_details as $key => $value)

                    <h4><strong> {{ $value->detail_name }} </strong></h4>
                    <p> {{ $value->detail_desc }} </p>

                  @endforeach                              
                    
                  <div class="user-button">
                      <div class="row">
                          <div class="col-md-offset-3 col-md-6">
                              <td> <a class="btn btn-warning m-r-sm my-btn" href="{{URL::to('/')}}/admin/vendor-extra-details-edit/{{$vendor_details[0]->user_id}}" role="button">Edit</a> </td>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
      </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">

                <div class="ibox-content">

                    <h2>Portfolio</h2>                        

                    <div class="lightBoxGallery clearfix">

                        @foreach($vendor_portfolio_images as $key => $value)
                            <div class="light-box-img-cont">
                                <a href="{{ $value->image }}" data-gallery=""><img src="{{ $value->image }}/100"></a> 
                            </div>
                        @endforeach
                        
                    </div>

                    <div class="upload-button-box">
                      <div class="row">
                          <div class="col-md-offset-4 col-md-4">

                            <a class="btn btn-warning m-r-sm my-btn" href="{{URL::to('/')}}/admin/upload-images/{{$vendor_details[0]->user_id}}" role="button">Edit Images</a>

                          </div>
                      </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <h2>Remove Vendor</h2>                        
                    <div class="row">
                      <div class="col-sm-offset-3 col-sm-6">
                        {!! Form::open(array('url' => 'admin/remove-vendor', 'class' => 'remove-vendor-form')) !!}
                          <input type="hidden" name="user_id" class="user_id" value="{{$vendor_details[0]->user_id}}">
                          <button type="button" class="btn btn-w-m btn-danger my-btn btn-remove-vendor">Remove Vendor</button>
                        {!! Form::close() !!}
                      </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>

</div>

@stop