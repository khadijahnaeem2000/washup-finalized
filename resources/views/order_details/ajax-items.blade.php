<option value="" selected disabled> ---Select Item ---</option>
@if(!empty($items))
  @foreach($items as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
  @endforeach
@endif

