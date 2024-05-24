@extends('layouts.master')
@section('title','Customers')
@section('content')

  <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
              <div class="card-header py-3">
                  <div class="card-title">
                      <h3 class="card-label">Show @yield('title')</h3>
                  </div>
                  <div class="card-toolbar">
                      <a  href="{{ route('customers.index') }}" class="btn btn-primary btn-sm ">
                      <i class="fas fa-arrow-left"></i></a>
                  </div>
              </div>
              <div class="card-body">
                  <div class="row">
                      <div class="col-12 col-md-12">
                          <div class="table-responsive">
                              <table class="table dt-responsive">
                                  <tr>
                                      <td width="50%">@yield('title') Name</td>
                                      <td>{{$data->name}}</td>
                                  </tr>
                                  <tr>
                                      <td>Email</td>
                                      <td>{{$data->email}}</td>
                                  </tr>
                                  <tr>
                                      <td>Current Amount</td>
                                      <td>@if($selected_wallets)
                                              {{$selected_wallets->net_amount}}
                                          @else
                                              {{ 0 }}
                                          @endif
                                      </td>
                                  </tr>
                                  <tr>
                                      <td>Contact#</td>
                                      <td>{{$data->contact_no}}</td>
                                  </tr>
                                  <tr>
                                      <td>Alt Contact#</td>
                                      <td>{{$data->alt_contact_no}}</td>
                                  </tr>
                                  <tr>
                                      <td>Customer Type</td>
                                      <td>{{$data->customer_type_name}}</td>
                                  </tr>
                                  <tr>
                                      <td>Permanent Note</td>
                                      <td>{{$data->permanent_note}}</td>
                                  </tr>
                                  <tr>
                                      <td>Email Notification</td>
                                      <td>
                                            @if((isset($data->email_alert)) && ($data->email_alert == 1))
                                                <label class="badge badge-success">Yes</span>
                                            @else
                                                <label class="badge badge-danger">No</span>
                                            @endif
                                      </td>
                                  </tr>
                                  <tr>
                                      <td>Messages</td>
                                      <td>
                                          @if($selected_messages)
                                              @foreach($selected_messages as $value)
                                                  <label class="badge badge-success">{{ $value->message_name }} </label>
                                              @endforeach
                                          @else
                                              <label class="badge badge-info">{{ "N/A" }} </label>
                                          @endif
                                      </td>
                                  </tr>
                              </table><br><br>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
            <!-- Showing Addresses -->
            <!-- <div class="card card-custom gutter-b example example-compact"> -->
            <div class="card card-custom card-collapsed" data-card="true" >
                <div class="card-header py-3">
                   <div class="card-title">
                       <h3 class="card-label">Address Details</h3>
                   </div>
                  
                   <div class="card-toolbar">
                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                            <i class="ki ki-arrow-down icon-nm"></i>
                        </button>
                    </div>
               </div>
             
               <!-- Showing Addresses -->
               <div class="card-body">
                   <div class="row">
                       <div class="col-12 col-md-12">
                           <div class="table-responsive">
                               <table class="table dt-responsive">
                                   <thead>
                                       <tr>
                                           <th width="90%">Address</th>
                                           <th width="10%">Status</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                        @if($selected_addresses)
                                            @foreach($selected_addresses as $value)
                                                <tr>
                                                    <td>{{ $value->address }}</td>
                                                    <td>{{ $value->status }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan=6><label class="badge badge-info">{{ "N/A" }} </label></td>
                                            </tr>
                                            
                                        @endif
                                   <tbody>
                               </table><br><br>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
            <br>

            <!-- Showing Retainer Days -->
            <div class="card card-custom card-collapsed" data-card="true" >
                <div class="card-header py-3">
                   <div class="card-title">
                       <h3 class="card-label">Retainer Days Details</h3>
                   </div>
                   <div class="card-toolbar">
                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                            <i class="ki ki-arrow-down icon-nm"></i>
                        </button>
                    </div>
               </div>
             
              
               <div class="card-body">
                    <div class="row">
                       <div class="col-12 col-md-12">
                           <div class="table-responsive">
                               <table class="table dt-responsive">
                                   <thead>
                                       <tr>
                                           <th width="30%">Day</th>
                                           <th width="30%">Timeslot</th>
                                           <th width="40%">Note</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                        @if($selected_days)
                                            @foreach($selected_days as $value)
                                                <tr>
                                                    <td>{{ $value->day_name }}</td>
                                                    <td>{{ $value->time_slot_name }}</td>
                                                    <td>{{ $value->day_note }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan=3><label class="badge badge-info">{{ "N/A" }} </label></td>
                                            </tr>
                                            
                                        @endif
                                   <tbody>
                               </table><br><br>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
            <br>

           <!-- Showing Retainer Days -->
            <div class="card card-custom card-collapsed" data-card="true" >
                <div class="card-header py-3">
                   <div class="card-title">
                       <h3 class="card-label">Transaction Details</h3>
                   </div>
                   <div class="card-toolbar">
                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                            <i class="ki ki-arrow-down icon-nm"></i>
                        </button>
                    </div>
               </div>
             
              
               <div class="card-body">
                    <div class="row">
                       <div class="col-12 col-md-12">
                           <div class="table-responsive">
                               <table class="table dt-responsive">
                                   <thead>
                                       <tr>
                                           <th width="5%">#</th>
                                           <th width="25%">Credit</th>
                                           <th width="25%">Debit (Bill)</th>
                                           <th width="20%">Ref: Order</th>
                                           <th width="25%">Entry date</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                        @if($wallet_transaction)
                                            @foreach($wallet_transaction as $key => $value)
                                                <tr>
                                                    <td>{{ ($key+1) }}</td>
                                                    <td>{{ $value->in_amount }}</td>
                                                    <td>{{ $value->out_amount }}</td>
                                                    <td>{{ $value->order_id }}</td>
                                                    <td>{{ $value->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan=5><label class="badge badge-info">{{ "N/A" }} </label></td>
                                            </tr>
                                            
                                        @endif
                                   <tbody>
                               </table><br><br>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           <br>

           <!-- Showing Services -->
           <div class="card card-custom card-collapsed" data-card="true" >
                <div class="card-header py-3">
                   <div class="card-title">
                       <h3 class="card-label">Services Details</h3>
                   </div>
                   <div class="card-toolbar">
                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                            <i class="ki ki-arrow-down icon-nm"></i>
                        </button>
                    </div>
               </div>

               <div class="card-body">
                    <div class="row">
                       <div class="col-12 col-md-12">
                           <div class="table-responsive">
                               <table class="table dt-responsive">
                                   <thead>
                                       <tr>
                                           <th width="60%">Service Name</th>
                                           <th width="40%">Service Rate</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                        @if($selected_services)
                                            @foreach($selected_services as $value)
                                            @if($value->status)
                                                <tr>
                                                    <td>{{ $value->service_name }}</td>
                                                    <td>{{ $value->service_rate }}</td>
                                                </tr>
                                            @else
                                                @continue
                                            @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan=2><label class="badge badge-info">{{ "N/A" }} </label></td>
                                            </tr>
                                            
                                        @endif
                                   <tbody>
                               </table><br><br>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           <br>

            <!-- Showing Special Services -->
            <div class="card card-custom card-collapsed" data-card="true" >
                
                <div class="card-header py-3">
                   <div class="card-title">
                       <h3 class="card-label">Special Service's Items Details</h3>
                   </div>
                   <div class="card-toolbar">
                        <button class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                            <i class="ki ki-arrow-down icon-nm"></i>
                        </button>
                   </div>
               </div>
             
              
               <div class="card-body">
                    <div class="row">
                       <div class="col-12 col-md-12">
                           <div class="table-responsive">
                               <table class="table dt-responsive">
                                   <thead>
                                       
                                   </thead>
                                   <tbody>
                                        @if($selected_items)
                                        <?php $id = $selected_items[0]->service_id;?>

                                            <tr>
                                                <th colspan=3><center> <h4>Special Service: {{ $selected_items[0]->service_name }}</h4></center></th>
                                            </tr>
                                            <tr>
                                                <th width="30%">Service Name</th>
                                                <th width="30%">Item Name</th>
                                                <th width="40%">Service Rate</th>
                                            </tr>
                                            

                                            @foreach($selected_items as $value)
                                                <?php if ($id == $value->service_id){ ?>
                                                    <tr>
                                                        <td>{{ $value->service_name }}</td>
                                                        <td>{{ $value->item_name }}</td>
                                                        <td>{{ $value->item_rate }}</td>
                                                    </tr>
                                                <?php }else{
                                                    $id = $value->service_id;
                                                    ?>
                                                    <tr>
                                                        <th  colspan=3><center> <h4>Special Service: {{ $value->service_name }}</h4></center></th>
                                                    </tr>
                                                    <tr>
                                                        <th width="30%">Service Name</th>
                                                        <th width="30%">Item Name</th>
                                                        <th width="40%">Service Rate</th>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $value->service_name }}</td>
                                                        <td>{{ $value->item_name }}</td>
                                                        <td>{{ $value->item_rate }}</td>
                                                    </tr>


                                                <?php }?>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan=2><label class="badge badge-info">{{ "N/A" }} </label></td>
                                            </tr>
                                            
                                        @endif
                                   <tbody>
                               </table><br><br>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>

   

@endsection