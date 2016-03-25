<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>What A Shaadi - Password Reset</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- JQuery min version online -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  </head>
  <body>

    <div class="container">
      
      <div class="row" style="margin-top : 50px">        
        <div class="col-sm-offset-4 col-sm-4">
          <div class="row" style="margin-bottom : 40px">
            <div class="col-sm-12">
              <img src="{{URL::to('/').'/images/logo.png'}}" class="center-block img-thumbnail">
            </div>
          </div>        

          @if(isset($confirm_token))
            @if($confirm_token==1)
              <form action="{{ URL::to('/').'/api/password-reset-2' }}" method="post">
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" class="form-control" placeholder="Password" name="password">
                </div>
                <div class="form-group">
                  <label>Confirm Password</label>
                  <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password">
                </div>                      
                <input type="hidden" value="{{ $user_id }}" name="user_id" />
                <input type="hidden" value="{{ $token }}" name="token" />
                <button type="submit" class="btn btn-danger center-block">Submit</button>
              </form>              
            @else 
              <p class="bg-danger" style="padding:30px; text-align:center; font-size:25px"> Invalid Token </p>  
            @endif            
          @else
            <p class="bg-danger" style="padding:30px; text-align:center; font-size:25px"> Invalid Token </p>    
          @endif     

          <?php
            $message = Session::get('message');          
          ?>  
          @if(isset($message))                
            <p class="bg-danger" style="margin-top:40px; padding:30px; text-align:center; font-size:15px font-weight:600"> {{ $message }} </p>    
          @endif     
        </div>      
      </div>

    </div>    

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>