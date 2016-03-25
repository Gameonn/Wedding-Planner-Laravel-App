@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Wedding Photos</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/wedding-listing/0/0">Wedding Listing</a>
            </li>
            <li>
                <a href="{{ URL::to('/') }}/admin/wedding-details/{{ $wedding_id }}">Wedding Details</a>
            </li>
            <li class="active">
                <strong>Wedding Photos</strong>
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

			    <div class="ibox-content">

			        <h2>Wedding Images</h2>                        

			        <div class="lightBoxGallery clearfix">

			            @foreach($wedding_photos as $key => $value)
			            	{!! Form::open(array('url' => 'admin/remove-wedding-image', 'method' => 'post')) !!}
			            	<div class="light-box-img-cont">
			            		<input type="hidden" name="img_del_id" value="{{ $value->id }}" >
			            		<button class="btn btn-warning btn-circle my-img-del-btn" type="submit"><i class="fa fa-times"></i></button>
			                	<a href="{{ URL::to('/') }}/admin/view-wedding-image-by-id/{{$value->id}}"><img src="{{ $value->image }}/100"></a>                            
		                	</div>
		                	{!! Form::close() !!}
			            @endforeach
			            
			        </div>			        

			        <div class="upload-button-box">
			          <div class="row">
			              <div class="col-md-offset-4 col-md-4">
 
							{!! Form::open(array('url' => 'admin/upload-wedding-images', 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}
								<input type="file" id="file" name="files[]" multiple="multiple" accept="image/*" class="my-file-chooser" />
								<input type="hidden" name="user_id" value="{{ $user_id }}" />
								<input type="hidden" name="wedding_id" value="{{ $wedding_id }}" />
								<input type="submit" value="Upload!" class="btn btn-warning m-r-sm my-btn" />
							{!! Form::close() !!}

			              </div>
			          </div>
			        </div>

			    </div>
			</div>
		</div>
    </div>

</div>

@stop