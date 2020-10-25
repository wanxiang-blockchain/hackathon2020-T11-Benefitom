<?php
if (!array_key_exists("class", $attr)) {
    $attr["class"] = "";
}
if (!array_key_exists("ext", $attr)) {
    $attr["ext"] = "";
}
?>
<div class="form-group">
    <label class="col-md-2 control-label">{{$attr["label"]}}</label>
    <div class="col-md-10">
        <input type="number" name="{{$attr["name"]}}" class="form-control {{$attr["class"]}}" {{$attr["ext"]}} value="{{$attr["value"]}}" placeholder="{{$attr["placeholder"]}}"/>
    </div>
</div>