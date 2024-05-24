<!-- <option>--- Select State ---</option> -->
@if(!empty($addresses))
  @foreach($addresses as $key => $value)
  @if($value->status==0)
    <option value="{{ $value->id }}">{{ $value->name }} (Primary)</option>
    @else
    <option value="{{ $value->id}}">{{ $value->name }} (Secondary)</option>
  @endif
  
  @endforeach
@endif