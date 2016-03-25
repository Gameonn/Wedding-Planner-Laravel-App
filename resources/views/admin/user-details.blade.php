@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>User Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li class="active">
                <strong>User Details</strong>
            </li>            
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight"> 

    @if(count($wedding_photos)>=3)       
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
                        <img src="{{ $wedding_photos[0]->image }}" alt="1">  
                      </div>                      
                      <div class="carousel-caption">                        
                      </div>
                    </div>

                    <div class="item">
                      <div class="carousel-img-box">
                        <img src="{{ $wedding_photos[1]->image }}" alt="1">  
                      </div>                      
                      <div class="carousel-caption">                        
                      </div>
                    </div>

                    <div class="item">
                      <div class="carousel-img-box">
                        <img src="{{ $wedding_photos[2]->image }}" alt="1">  
                      </div>                      
                      <div class="carousel-caption">                        
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
                <h5>User Details</h5>
            </div>
            <div>  
                <div class="ibox-content no-padding border-left-right">
                    <img alt="image" class="img-responsive center-block" src="{{$user_details[0]->image}}">
                </div>              
                <div class="ibox-content profile-content">

                    <h4><strong>Name</strong></h4>
                    <p> {{$user_details[0]->name}} </p>

                    <h4><strong>Email</strong></h4>
                    <p> {{$user_details[0]->email}} </p>

                    <h4><strong>Phone Number</strong></h4>
                    <p> {{$user_details[0]->phone_no}} </p>

                    <h4><strong>Role</strong></h4>
                    <p> User / Customer </p>
                    
                </div>
            </div>
        </div>
      </div>    
    
      <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Wedding Details</h5>
            </div>
            <div>                
                <div class="ibox-content profile-content">
                    
                    <h4><strong>Wedding Title</strong></h4>
                    <p> {{$user_details[0]->wedding_name}} </p>

                    <h4><strong>Wedding Description</strong></h4>
                    <p> {{$user_details[0]->wedding_desc}} </p> 

                    <h4><strong>Wedding Date</strong></h4>
                    <p> {{$user_details[0]->date}} </p>                    

                    <h4><strong>Wedding Type</strong></h4>
                    <p> {{$user_details[0]->wedding_type}} </p>                    

                    <h4><strong>Location</strong></h4>
                    <p> {{$user_details[0]->location}} </p> 

                    <h4><strong>City</strong></h4>
                    <p> {{$user_details[0]->city}} </p>                    

                </div>
            </div>
        </div>
      </div>

    </div>

</div>

@stop