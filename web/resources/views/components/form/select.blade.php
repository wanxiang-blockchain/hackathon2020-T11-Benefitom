<div class="form-group">
    <label class="col-md-2 control-label">{{$label}}</label>
    <div class="col-md-2">
        <?php $old = old($name)?>
        <select name="{{$name}}" class="form-control select">
            @foreach($values as $v)
            <option value="{{$v['value']}}" @if($old == $v['value']) selected @endif>{{$v['name']}}</option>
             @endforeach
        </select>
    </div>
</div>