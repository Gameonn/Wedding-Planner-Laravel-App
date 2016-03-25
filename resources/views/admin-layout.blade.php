<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>What A Shaadi | Admin Panel</title>

    <link href="{{ URL::to('/') }}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ URL::to('/') }}/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Sweet Alert -->
    <link href="{{ URL::to('/') }}/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">

    <link href="{{ URL::to('/') }}/css/animate.css" rel="stylesheet">
    <link href="{{ URL::to('/') }}/css/style.css" rel="stylesheet">

    <link href="{{ URL::to('/') }}/css/my-admin-style.css" rel="stylesheet">    

    <!-- Select 2 -->
    <link href="{{ URL::to('/') }}/css/plugins/select2/select2.min.css" rel="stylesheet">

    <!-- Tag it -->
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
    <link href="{{ URL::to('/') }}/aehlke-tag-it-6ccd2de/css/jquery.tagit.css" rel="stylesheet" type="text/css">

    <!-- Main Js Script -->
    <script src="{{ URL::to('/') }}/js/jquery-2.1.1.js"></script>    

</head>

<body>
    <?php $current_url = Request::url() ?>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> 
                            <div class="my-img-box">
                                <img alt="image" class="img-circle" src="{{ URL::to('/') }}/photos/thumb/pic5/jpg/50" />
                            </div>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">{{ Session::get('admin_username') }}</strong>
                             </span> <span class="text-muted text-xs block">Options<b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="{{ URL::to('/') }}/admin/dashboard">Dashboard</a></li>
                                <!-- <li><a href="{{ URL::to('/') }}/contacts.html">Contacts</a></li>
                                <li><a href="{{ URL::to('/') }}/mailbox.html">Mailbox</a></li> -->
                                <li class="divider"></li>
                                <li><a href="{{ URL::to('/') }}/admin/logout">Logout</a></li>
                            </ul>
                        </div>
                        <div class="logo-element">
                            IN+
                        </div>
                    </li>      
                    @if(strpos($current_url,'admin/dashboard')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif                                              
                        <a href="{{ URL::to('/') }}/admin/dashboard"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/vendor-listing')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/vendor-listing/0/0"><i class="fa fa-users"></i> <span class="nav-label">Vendor Listing</span></a>
                    </li>
                    @if(strpos($current_url,'admin/add-vendor')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/add-vendor"><i class="fa fa-users"></i> <span class="nav-label">Add Vendor</span></a>
                    </li>
                    @if(strpos($current_url,'admin/vendor-businesses')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/vendor-businesses"><i class="fa fa-diamond"></i> <span class="nav-label">Vendor Businesses</span></a>
                    </li>
                    @if(strpos($current_url,'admin/conceirge')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/conceirge"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Conceirge</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/new-wedding')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/new-wedding"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Create Wedding</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/wedding-listing')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/wedding-listing/0/0"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Wedding Listing</span></a>                        
                    </li>    
                    @if(strpos($current_url,'admin/wedding-types')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/wedding-types"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Wedding Types</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/user-listing')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/user-listing/0/0"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Users</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/collaborator-listing')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/collaborator-listing/0/0"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Collaborator Members</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/collaborator-group-listing')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/collaborator-group-listing/0/0"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Collaborator Groups</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/city-listing')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/city-listing"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">City</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/get-sponsor')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/get-sponsor"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Sponsors</span></a>                        
                    </li>
                    @if(strpos($current_url,'admin/admin-listing')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/admin-listing"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Admin Listing</span></a>
                    </li>
                    @if(strpos($current_url,'admin/auto-authorization')) 
                        <li class="active">       
                    @else
                        <li>       
                    @endif 
                        <a href="{{ URL::to('/') }}/admin/auto-authorization"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Auto Authorization</span></a>
                    </li>
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>

                        @if(strpos($current_url,'admin/dashboard')) 
                            
                        @elseif(strpos($current_url,'admin/vendor-listing')) 

                            <div class="navbar-form-custom">
                                <div class="form-group">
                                    <?php
                                        $url_arr = explode('/', $current_url);
                                        if($url_arr[5])
                                            $keyword = $url_arr[5];
                                        else
                                            $keyword = "";
                                    ?>
                                    <input type="text" placeholder="Search for something..." class="form-control" name="search-vendor-listing" id="search-vendor-listing" value="{{ $keyword }}">                                    
                                </div>
                            </div>

                        @elseif(strpos($current_url,'admin/vendor-businesses')) 

                        @elseif(strpos($current_url,'admin/conceirge')) 

                        @elseif(strpos($current_url,'admin/new-wedding')) 

                        @elseif(strpos($current_url,'admin/wedding-listing')) 

                            <div class="navbar-form-custom">
                                <div class="form-group">
                                    <?php
                                        $url_arr = explode('/', $current_url);
                                        if($url_arr[5]) {
                                            $keyword = $url_arr[5];
                                            $keyword = str_replace("%20"," ", $keyword);
                                        }
                                        else
                                            $keyword = "";
                                    ?>
                                    <input type="text" placeholder="Search for something..." class="form-control" name="search-wedding-listing" id="search-wedding-listing" value="{{ $keyword }}">                                    
                                </div>
                            </div>

                        @elseif(strpos($current_url,'admin/wedding-types')) 

                        @elseif(strpos($current_url,'admin/user-listing')) 

                            <div class="navbar-form-custom">
                                <div class="form-group">
                                    <?php
                                        $url_arr = explode('/', $current_url);
                                        if($url_arr[5]) {
                                            $keyword = $url_arr[5];
                                            $keyword = str_replace("%20"," ", $keyword);
                                        }
                                        else
                                            $keyword = "";
                                    ?>
                                    <input type="text" placeholder="Search for something..." class="form-control" name="search-user-listing" id="search-user-listing" value="{{ $keyword }}">                                    
                                </div>
                            </div>

                        @elseif(strpos($current_url,'admin/collaborator-listing')) 

                            <div class="navbar-form-custom">
                                <div class="form-group">
                                    <?php
                                        $url_arr = explode('/', $current_url);
                                        if($url_arr[5]) {
                                            $keyword = $url_arr[5];
                                            $keyword = str_replace("%20"," ", $keyword);
                                        }
                                        else
                                            $keyword = "";
                                    ?>
                                    <input type="text" placeholder="Search for something..." class="form-control" name="search-collaborator-listing" id="search-collaborator-listing" value="{{ $keyword }}">                                    
                                </div>
                            </div>

                        @elseif(strpos($current_url,'admin/collaborator-group-listing')) 

                            <div class="navbar-form-custom">
                                <div class="form-group">
                                    <?php
                                        $url_arr = explode('/', $current_url);
                                        if($url_arr[5]) {
                                            $keyword = $url_arr[5];
                                            $keyword = str_replace("%20"," ", $keyword);
                                        }
                                        else
                                            $keyword = "";
                                    ?>
                                    <input type="text" placeholder="Search for something..." class="form-control" name="search-collaborator-group-listing" id="search-collaborator-group-listing" value="{{ $keyword }}">                                    
                                </div>
                            </div>

                        @elseif(strpos($current_url,'admin/city-listing')) 

                        @else
                            
                        @endif                                                                      

                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">What A Shaadi Admin Panel</span>
                        </li>
                        <li class="dropdown my-header-dropdown">
                            <!-- Jquery Content -->                            
                        </li>
                        <!-- <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-bell"></i>  <span class="label label-primary">8</span>
                            </a>
                            <ul class="dropdown-menu dropdown-alerts">
                                <li>
                                    <a href="{{ URL::to('/') }}/mailbox.html">
                                        <div>
                                            <i class="fa fa-envelope fa-fw"></i> You have 16 messages
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ URL::to('/') }}/profile.html">
                                        <div>
                                            <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                            <span class="pull-right text-muted small">12 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ URL::to('/') }}/grid_options.html">
                                        <div>
                                            <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                            <span class="pull-right text-muted small">4 minutes ago</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a href="{{ URL::to('/') }}/notifications.html">
                                            <strong>See All Alerts</strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li> -->


                        <li>
                            <a href="{{ URL::to('/') }}/admin/logout">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                        <li>
                            <a class="right-sidebar-toggle">
                                <i class="fa fa-tasks"></i>
                            </a>
                        </li>
                    </ul>

                </nav>
            </div>
                    
            @yield('content')

            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content"></div>
                    <div class="footer">
                        <div class="pull-right">
                            10GB of <strong>250GB</strong> Free.
                        </div>
                        <div>
                            <strong>Copyright</strong> What A Shaadi &copy; 2014-2015
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Mainly scripts -->    
    <script src="{{ URL::to('/') }}/js/bootstrap.min.js"></script>
    <script src="{{ URL::to('/') }}/js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- jQuery UI -->
    <script src="{{ URL::to('/') }}/js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Tag It -->
    <script src="{{ URL::to('/') }}/aehlke-tag-it-6ccd2de/js/tag-it.js" type="text/javascript" charset="utf-8"></script>

    <!-- Bootstrap File Style -->
    <script type="text/javascript" src="{{ URL::to('/') }}/bootstrap-filestyle-1.2.1/src/bootstrap-filestyle.min.js"> </script>

    <!-- Select 2 -->
    <script src="{{ URL::to('/') }}/js/plugins/select2/select2.full.min.js"></script>

    <!-- My Admin JS -->
    <script src="{{ URL::to('/') }}/js/my-admin.js"></script>        

    <!-- Custom and plugin javascript -->

    <!-- <script src="{{ URL::to('/') }}/js/plugins/slimscroll/jquery.slimscroll.min.js"></script> -->
    <script src="{{ URL::to('/') }}/js/inspinia.js"></script>
    <!-- <script src="{{ URL::to('/') }}/js/plugins/pace/pace.min.js"></script> -->       

    <!-- GITTER -->
    <script src="{{ URL::to('/') }}/js/plugins/gritter/jquery.gritter.min.js"></script>

    <!-- Sparkline -->
    <script src="{{ URL::to('/') }}/js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="{{ URL::to('/') }}/js/demo/sparkline-demo.js"></script>

    <!-- ChartJS-->
    <script src="{{ URL::to('/') }}/js/plugins/chartJs/Chart.min.js"></script>

    <!-- Toastr -->
    <script src="{{ URL::to('/') }}/js/plugins/toastr/toastr.min.js"></script>    

    <!-- blueimp gallery -->
    <script src="{{ URL::to('/') }}/js/plugins/blueimp/jquery.blueimp-gallery.min.js"></script>

    <!-- slick carousel-->
    <script src="{{ URL::to('/') }}/js/plugins/slick/slick.js"></script>

    <!-- Sweet alert -->
    <script src="{{ URL::to('/') }}/js/plugins/sweetalert/sweetalert.min.js"></script>        

    <!-- Additional style only for demo purpose -->
    <style>
        .slick_demo_2 .ibox-content {
            margin: 0 10px;
        }
    </style>

    <style>
        /* Local style for demo purpose */

        .lightBoxGallery {
            text-align: center;
        }

        .lightBoxGallery img {
            margin: 5px;
        }

    </style>


    <script>
        $(document).ready(function() {                                                
            
        });
    </script>
</body>
</html>
