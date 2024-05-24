@if(!empty($services))
  @foreach($services as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
  @endforeach
@endif