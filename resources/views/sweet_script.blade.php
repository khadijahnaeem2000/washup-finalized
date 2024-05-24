<link href="{{asset('libs/toastr/toastr.css')}}" rel="stylesheet"/>
<script src="{{asset('libs/toastr/toastr.js')}}"></script>

<script src="{{asset('libs/datatable/jquery.dataTables.min.js')}}" defer></script>
<script src="{{asset('libs/datatable/dataTables.bootstrap4.min.js')}}" defer></script>







@if ($message = Session::get('success'))
  <script>
    var text = "<?php echo $message;?>";
    toastr.success(text)
  </script>
  <?php session()->forget('success');?>
@endif



@if ($message = Session::get('permission'))
  <script>
    var text = "<?php echo $message;?>";
    toastr.error(text)
  </script>
  <?php session()->forget('success');?>

@endif

@if (count($errors) > 0)

<div class="alert alert-custom alert-outline-danger fade show mb-5" role="alert">
  <div class="alert-icon">
    <i class="flaticon-warning"></i>
  </div>
  <div class="alert-text">
    <strong>Whoops!</strong> 
    Something went wrong.
    <br><br>
      <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
  <div class="alert-close">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">
        <i class="ki ki-close"></i>
      </span>
    </button>
  </div>
</div>
    <!-- <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="alert alert-danger">
               
            </div>         
        </div>
    </div> -->
@endif

