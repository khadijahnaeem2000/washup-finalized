<link href="{{asset('libs/toastr/toastr.css')}}" rel="stylesheet"/>
<script src="{{asset('libs/toastr/toastr.js')}}"></script>

<link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet"/>



<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> -->
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<!-- <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script> -->








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

