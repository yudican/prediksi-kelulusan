<div class="form-group">
  <div class="text-left">
    <label>{{$label}}</label><br>
    <label for="image-picker">
      @if ($foto)
      @if ($path)
      <img id="image-preview" width="150" src="{{ $path }}" alt="your image" />
      @else
      <img id="image-preview" width="150" src="{{ asset('storage/'.$foto) }}" alt="your image" />
      @endif

      @else
      @if ($path)
      <img id="image-preview" width="150" src="{{ $path }}" alt="your image" />
      @else
      <img id="image-preview" width="150" src="{{asset('assets/img/card.svg')}}" alt="your image" />
      @endif
      @endif
    </label>
    <input id="image-picker" class="d-none" wire:model="{{$name}}" type="file" accept="image/*" />
    <br>
    <small id="helpId" class="text-danger">{{ $errors->has($name) ? $errors->first($name) : '' }}</small>
  </div>
</div>