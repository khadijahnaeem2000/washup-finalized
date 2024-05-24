@if(!empty($zones))
  @foreach($zones as $key => $value)
    <option value="{{ $value->id }}">{{ $value->name }} </option>
  @endforeach
@endif