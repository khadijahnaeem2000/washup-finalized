@if(!empty($time_slots))
  <td>{!! Form::select("time_slot[]", $time_slots,$state, array("class"=> "form-control")) !!}</td>
@endif