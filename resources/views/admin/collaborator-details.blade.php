@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Collaborator Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/collaborator-listing/0/0">Collaborator Listing</a>
            </li>
            <li class="active">
                <strong>Collaborator Details</strong>
            </li>            
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight"> 

    <div class="row">
      <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Collaborator Details</h5>
            </div>
            <div>  
                <div class="ibox-content no-padding border-left-right">
                    <img alt="image" class="img-responsive center-block" src="{{$collaborator_details[0]->image}}">
                </div>              
                <div class="ibox-content profile-content">

                    <h4><strong>Name</strong></h4>
                    <p> {{$collaborator_details[0]->name}} </p>

                    <h4><strong>Email</strong></h4>
                    <p> {{$collaborator_details[0]->email}} </p>
 
                    <h4><strong>Phone Number</strong></h4>
                    <p> {{$collaborator_details[0]->phone_no}} </p>

                    <h4><strong>Role</strong></h4>
                    <p> Collaborator </p>
                    
                </div>
            </div>
        </div>
      </div>      

    </div>

</div>

@stop