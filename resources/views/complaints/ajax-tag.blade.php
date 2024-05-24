@if(!empty($tags))
  @foreach($tags as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
  @endforeach
@endif