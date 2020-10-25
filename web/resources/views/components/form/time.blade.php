<div class="form-group">
    <label class="col-md-2 control-label">{{$label}}</label>
    <div class="col-md-5">
        <input required class="wdate form-control" type="text" name="{{$name}}" value="{{old($name)}}"
               onFocus='WdatePicker({"maxDate": "2020-10-01", "dateFmt": "yyyy-MM-dd HH:mm:ss"})' placeholder="{{$label}}">
    </div>
</div>
