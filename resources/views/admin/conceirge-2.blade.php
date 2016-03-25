@extends('admin-layout')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Messages</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ URL::to('/') }}/admin/dashboard">Home</a>
            </li>
            <!-- <li>
                
            </li> -->
            <li class="active">
                <strong>Messages</strong>
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

          <div class="ibox-title">              
               Chats
          </div>


          <div class="ibox-content">

              <div class="row">

                  <div class="col-md-12 ">                  

                      <div class="chat-discussion"> 

                        @if($messages[0]->is_more_chat != null)
                          <input type="hidden" value="{{ $messages[0]->is_more_chat }}" class="is-more-chat" >
                          <button type="button" class="btn btn-w-m btn-default center-block prev-message-btn">View More</button>
                        @endif                                               
 
                        <div class="dyna-chat-cont">                          

                          @foreach($messages as $key => $value)  

                            @if($value->sent_by == 0)
                            <div class="chat-message right">
                                @if(strpos($value->image,'graph.facebook.com') !== false)
                                    <img class="message-avatar" src="{{ $value->image }}" alt="">
                                @else
                                    <img class="message-avatar" src="{{ $value->image }}/50" alt="">
                                @endif
                                <div class="message">                                  
                                    <strong>
                                    @if($value->user_name == "")                                    
                                      {{ $value->user_phone_no }}
                                    @else                       
                                      {{ $value->user_name }}             
                                    @endif
                                    </strong>
                                    <span class="message-date"> {{ $value->time_since }} </span>
                                    <span class="message-content">
                                      @if($value->message_type == 0)
                                        {{ $value->message }}
                                      @elseif($value->message_type == 1)
                                        <div class="chat-image-box">
                                          <img src="{{ $value->chat_image }}/300">
                                        </div>                           
                                      @elseif($value->message_type == 2)
                                        <div class="row">
                                          <div class="col-lg-offset-8 col-lg-4">
                                              <div class="widget-head-color-box navy-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      {{ $value->wedding_name }}
                                                  </h2>
                                                      <small>Wedding</small>
                                                  </div>
                                                  <img src="{{ $value->wedding_image }}/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>{{ $value->wedding_type }}</span>
                                                  </div>                                                  
                                              </div>                                              
                                          </div>                                          
                                        </div>
                                      @elseif($value->message_type == 3)
                                        <div class="row">
                                          <div class="col-lg-offset-8 col-lg-4">
                                              <div class="widget-head-color-box lazur-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      {{ $value->business_name }}
                                                  </h2>
                                                      <small>Vendor</small>
                                                  </div>
                                                  <img src="{{ $value->vendor_portfolio_image }}/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>{{ $value->business_type }}</span>      
                                                  </div>
                                                  <div>
                                                      <span> <strong>Rating</strong> {{ $value->vendor_rating }}</span>
                                                  </div>
                                              </div>                                              
                                          </div>                                          
                                        </div>
                                      @else                                        
                                      @endif                                      
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="chat-message left">
                                @if(strpos($value->image,'graph.facebook.com') !== false)
                                    <img class="message-avatar" src="{{ $value->image }}" alt="">
                                @else
                                    <img class="message-avatar" src="{{ $value->image }}/50" alt="">
                                @endif                                
                                <div class="message">                                  
                                    <strong>
                                    @if($value->user_name == "")                                    
                                      {{ $value->user_phone_no }}
                                    @else                       
                                      {{ $value->user_name }}             
                                    @endif
                                    </strong>
                                    <span class="message-date"> {{ $value->time_since }} </span>
                                    <span class="message-content">
                                      @if($value->message_type == 0)
                                        {{ $value->message }}
                                      @elseif($value->message_type == 1)
                                        <div class="chat-image-box">
                                          <img src="{{ $value->chat_image }}/300">
                                        </div>                           
                                      @elseif($value->message_type == 2)
                                        <div class="row">
                                          <div class="col-lg-4">
                                              <div class="widget-head-color-box navy-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      {{ $value->wedding_name }}
                                                  </h2>
                                                      <small>Wedding</small>
                                                  </div>
                                                  <img src="{{ $value->wedding_image }}/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>{{ $value->wedding_type }}</span>
                                                  </div>                                                  
                                              </div>                                              
                                          </div>                                          
                                        </div>
                                      @elseif($value->message_type == 3)
                                        <div class="row">
                                          <div class="col-lg-4">
                                              <div class="widget-head-color-box lazur-bg p-lg text-center my-chat-widget">
                                                  <div class="m-b-md">
                                                  <h2 class="font-bold no-margins">
                                                      {{ $value->business_name }}
                                                  </h2>
                                                      <small>Vendor</small>
                                                  </div>
                                                  <img src="{{ $value->vendor_portfolio_image }}/300" class="m-b-md" alt="profile">
                                                  <div>
                                                      <span>{{ $value->business_type }}</span>      
                                                  </div>
                                                  <div>
                                                      <span> <strong>Rating</strong> {{ $value->vendor_rating }}</span>
                                                  </div>
                                              </div>                                              
                                          </div>                                          
                                        </div>
                                      @else                                        
                                      @endif                                      
                                    </span>
                                </div>
                            </div>
                            @endif                                                  

                          @endforeach                                                    

                        </div>

                      </div>
                  </div>                  

              </div>
              <div class="row">
                  <div class="col-lg-12">
                      <div class="chat-message-form">                      
                          <div class="form-group">
                              <input type="hidden" id="user_id" value="{{ $user_id }}" >
                              <input type="hidden" id="user_id_2" value="{{ $user_id_2 }}" >
                              <input type="hidden" id="message_type" value="0" >
                              <div class="row">
                                
                                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
                                  <textarea class="form-control message-input" id="send-message" name="message" placeholder="Enter message text"></textarea>
                                </div>

                                <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
                                  <button type="button" class="btn btn-warning my-btn my-btn-2" data-toggle="modal" data-target="#myWeddingModal"><i class="fa fa-university"></i></button> 
                                  <button type="button" class="btn btn-warning my-btn" data-toggle="modal" data-target="#myVendorModal"><i class="fa fa-user"></i></button>                                                                

                                  <div class="modal inmodal" id="myWeddingModal" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content animated bounceInRight">
                                              <div class="modal-header">                                                  
                                                  <h4 class="modal-title">Weddings</h4>                                                   
                                              </div>                                              
                                              <div class="modal-body">                                              
                                                <div class="form-group">
                                                  <select class="select2-select form-control" id="select-wedding-id">
                                                    @foreach($wedding_listing as $key => $value)
                                                      <option value="{{ $value->wedding_id }}">{{ $value->name }}</option>
                                                    @endforeach
                                                  </select>
                                                </div>
                                              </div>             
                                              <div class="modal-footer">                                                  
                                                  <button type="submit" class="btn btn-warning send-wedding-btn">Share</button>
                                              </div>                                               
                                          </div>
                                      </div>
                                  </div>

                                  <div class="modal inmodal" id="myVendorModal" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content animated bounceInRight">
                                              <div class="modal-header">                                                  
                                                  <h4 class="modal-title">Vendors</h4>           
                                              </div>  
                                              <div class="modal-body">
                                                
                                                <select class="select2-select form-control" id="select-vendor-id">
                                                    @foreach($vendor_listing as $key => $value)
                                                      <option value="{{ $value->vendor_id }}">{{ $value->business_name }}</option>
                                                    @endforeach
                                                </select>

                                              </div>             
                                              <div class="modal-footer">                                                  
                                                  <button type="button" class="btn btn-warning send-vendor-btn">Share</button>
                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                </div>

                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                                  <button type="button" class="btn btn-w-m btn-primary my-btn my-btn-2 send-chat-btn">Send</button> 
                                  <button type="button" class="btn btn-w-m btn-info my-btn" data-toggle="modal" data-target="#myImageUploadModal"><i class="fa fa-camera"></i></button>                                                                

                                  <div class="modal inmodal" id="myImageUploadModal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content animated bounceInRight">
                                              <div class="modal-header">                                                  
                                                  <h4 class="modal-title">Upload Image</h4>                                                   
                                              </div>
                                              {!! Form::open(array('url' => 'admin/upload-chat-image', 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}
                                                <div class="modal-body">
                                                  <div class="chooser-image-box">
                                                    <img src="{{ URL::to('/') }}/images/default-profile.png" class="chooser-image">
                                                  </div>
                                                  <input type="hidden" name="user_id" value="{{ $user_id }}" />
                                                  <input type="hidden" name="user_id_2" value="{{ $user_id_2 }}" >
                                                  <input type="hidden" name="message_type" value="1" >
                                                  <input type="file" id="file" name="image" accept="image/*" class="my-file-chooser-2" />
                                                </div>             
                                                <div class="modal-footer">                                                  
                                                    <button type="submit" class="btn btn-warning">Upload!</button>
                                                </div>                                    
                                              {!! Form::close() !!}
                                          </div>
                                      </div>
                                  </div>

                                </div>

                              </div>                              
                          </div>                          
                      </div>
                  </div>
              </div>


          </div>

        </div>

      </div>
    </div>

</div>

<!-- My Admin Chat JS -->
<script src="{{ URL::to('/') }}/js/my-admin-chat.js"></script>

@stop