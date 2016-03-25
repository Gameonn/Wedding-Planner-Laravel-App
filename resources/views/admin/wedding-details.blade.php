@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Wedding Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/wedding-listing/0/0">Wedding Listing</a>
            </li>
            <li class="active">
                <strong>Wedding Details</strong>
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
                        Image 1
                      </div>
                    </div>

                    <div class="item">
                      <div class="carousel-img-box">
                        <img src="{{ $wedding_photos[1]->image }}" alt="1">  
                      </div>                      
                      <div class="carousel-caption">
                        Image 1
                      </div>
                    </div>

                    <div class="item">
                      <div class="carousel-img-box">
                        <img src="{{ $wedding_photos[2]->image }}" alt="1">  
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
      <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Wedding Detail</h5>
            </div>
            <div>                
                <div class="ibox-content profile-content">

                    <h4><strong>Wedding Name</strong></h4>
                    <p> {{$wedding_details[0]->name}} </p>

                    <h4><strong>Date</strong></h4>
                    <p> {{$wedding_details[0]->date}} </p>

                    <h4><strong>Wedding Type</strong></h4>
                    <p> {{$wedding_details[0]->wedding_type}} </p>

                    <h4><strong>Location</strong></h4>
                    <p> {{$wedding_details[0]->location}} </p>

                    <h4><strong>City</strong></h4>
                    <p> {{$wedding_details[0]->city}} </p>

                    <h4><strong>User</strong></h4>
                    <p> {{$wedding_details[0]->user_name}} </p> 

                    <h4><strong>Description</strong></h4>
                    <p> {{$wedding_details[0]->description}} </p>                    

                    <div class="user-button">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-6">
                                <td> <a class="btn btn-warning m-r-sm my-btn" href="{{ URL::to('/') }}/admin/wedding-edit/{{$wedding_details[0]->id}}" role="button">Edit</a> </td>
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

                <h2>Wedding Photos</h2>                        

                <div class="lightBoxGallery clearfix">                  

                    @foreach($wedding_photos as $key => $value)
                        <div class="light-box-img-cont">
                            <a href="{{ URL::to('/') }}/admin/view-wedding-image-by-id/{{$value->id}}"> <img src="{{ $value->image }}/100"> </a>
                        </div>
                    @endforeach

                </div>

                <div class="upload-button-box">
                  <div class="row">
                      <div class="col-md-offset-4 col-md-4">

                        <a class="btn btn-warning m-r-sm my-btn" href="{{ URL::to('/') }}/admin/add-wedding-photos/{{$wedding_details[0]->id}}" role="button">Edit Images</a>

                      </div>
                  </div>
                </div>

            </div>
        </div>
      </div>
    </div>

</div>

@stop