<?php
if (!array_key_exists("ext", $attr)) {
    $attr["ext"] = "";
}
?>
<div class="form-group">
    <label class="col-md-2 control-label">上传图片</label>
    <div class="col-md-10">
        <input type="file" value="{{$attr['value']}}" name="{{$attr["name"]}}" {{$attr["ext"]}} class="file"  id="{{$attr["id"]}}" title="{{$attr["title"]}}" data-preview-file-type="any"/>
    </div>
</div>
@if($attr['url'] && isset($attr['url'][0]))
<div class="form-group show_thumb_box">
    <label class="col-md-2 control-label"></label>
    <div class="col-md-10">
        <div class="file-preview">
            <div class="file-drop-zone show_thumb">
                @foreach($attr['url'] as $url)
                <div class="file-preview-thumbnails">
                    <div class="file-preview-frame" id="preview-1488346730174-0" data-fileindex="0">
                        <img src="{{asset('storage/'.$url)}}" class="file-preview-image" style="width:auto;height:160px;">
                        {{-- <input type="hidden" name="_url[]" value="{{$url}}">--}}
                        <div class="file-thumbnail-footer">
                           <div class="file-actions">
                                <div class="file-footer-buttons">
                                    <button type="button" class="kv-file-remove btn btn-xs btn-default" title="删除文件"><i class="glyphicon glyphicon-trash text-danger"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
@endif