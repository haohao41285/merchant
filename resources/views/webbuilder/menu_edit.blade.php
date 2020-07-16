@extends('layouts.master')
@section('title', (isset($id)&&$id>0)?'Website Builder | Menus | Edit Menu':'Website Builder | Menus | Add Menu')
@section('styles')
 <link href="{{ asset('plugins/iCheck/skins/flat/green.css') }}" rel="stylesheet">    
 <link href="{{ asset('plugins/dropzone/dist/dropzone.min.css') }}" rel="stylesheet"> 
 <link href="{{ asset('plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">  
 <style>
     .dropzone {
        border: 2px solid #757575;
    }
    .glyphicon-remove:hover{
        color: red;
    }
 </style>
@stop
@section('content')
<div class='x_panel x_panel_form'>
    <div class="x_title">
         <h3>@if (isset($id)&&$id>0)
                    Edit Menu
            @else 
                    Add Menu
            @endif
        </h3>
     </div>
    <div class="x_content">
         
        <form method="post" action="{{route('save-menu')}}" class="form-horizontal form-label-left" id="menu_form" enctype="multipart/form-data" > 
        @csrf
        <input type="hidden" name="menu_id" value="{{$id}}"/>
            <div class="row form-group">
                <label class="col-sm-3 col-md-2">Menu Title</label>
                <div class="col-sm-5 col-md-4">
                   <input type='text' name="menu_name" required class="form-control form-control-sm{{ $errors->has('menu_name') ? ' is-invalid' : '' }}" value="{{isset($menu_item->menu_name)? $menu_item->menu_name:old('menu_name')}}"/>
                </div>            
                <label class="col-sm-3 col-md-2">Menu Parent</label>
                <div class="col-sm-5 col-md-4">
                   <select class="form-control form-control-sm"  name="menu_parent_id">
                        <option>-- Select Parent-- </option>
                        @foreach($list_menu as $menu_parent)
                            <option
                            @if(isset($menu_item->menu_id)&&$menu_item->menu_parent_id == $menu_parent->menu_id) selected @endif
                                
                             value="{{$menu_parent->menu_id}}">{{$menu_parent->menu_name}} </option>
                        @endforeach
                      </select>
                </div>             
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-md-2">Menu URL</label>
                <div class="col-sm-5 col-md-4">
                   <input type='text' name="menu_url" value="{{isset($menu_item->menu_url)? $menu_item->menu_url:old('menu_url')}}" class="form-control form-control-sm{{ $errors->has('menu_url') ? ' is-invalid' : '' }}"/>
                </div>  
                          
                <label class="col-sm-3 col-md-2">Index</label>
                <div class="col-sm-5 col-md-4">
                   <input required="" type='number' name="menu_index" class="form-control form-control-sm{{ $errors->has('menu_index') ? ' is-invalid' : '' }}" value="{{isset($menu_item->menu_index)? $menu_item->menu_index:old('menu_index')}}"/>
                </div>             
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-md-2">Status</label>
                <div class="col-sm-5 col-md-4 form-inline">
                   <div class="radio">
                        <label>
                          <input type="radio" {{(!isset($menu_item))?"checked":""}} class="flat checkFlat" value="1" @if(isset($menu_item->menu_type)&&$menu_item->menu_type ==1) checked @endif name="menu_type">&nbsp;Enabled
                        </label>
                    </div>
                    <div class="radio" style="margin-left:10px;">
                        <label>
                          <input type="radio"  class="flat checkFlat" value="0" @if(isset($menu_item->menu_type)&&$menu_item->menu_type ==0) checked @endif name="menu_type">&nbsp;Disabled
                        </label>
                    </div>
                </div> 
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-md-2">Menu Image</label>
                <div class="col-sm-10 col-md-10" style="overflow: hidden;">
                   <div class="catalog-image-upload ">
                          <div class="catalog-image-edit">
                            <input type="hidden" name="menu_image_old" value="{{isset($menu_item->menu_image)? $menu_item->menu_image:old('menu_image')}}" hidden>
                              <input type='file' id="menu_image" value="{{isset($menu_item->menu_image)? $menu_item->menu_image:old('menu_image')}}" name="menu_image" data-target="#catalogImagePreview2" accept=".png, .jpg, .jpeg" />
                              <label for="menu_image"></label>
                          </div>
                          
                          <div class="catalog-image-preview">
                              <img style='display:{{(isset($menu_item)&&$menu_item->menu_image!="")?"":"none"}}' id="catalogImagePreview2" src="{{config('app.url_file_view')}}{{isset($menu_item->menu_image)? $menu_item->menu_image:old('menu_image')}}" />
                          </div>
                      </div>
                </div> 
                <span style="color: red">{{$errors->first('menu_image')}}</span> 
            </div>
             <div class="row form-group">
                <input type="hidden" name="multi_image" id="multi_image" value="{{isset($menu_item->menu_list_image)? $menu_item->menu_list_image:old('menu_list_image')}}">
                <span class="list_image"></span>
                <label class="col-sm-3 col-md-2">List Image Show</label>
                <div class="col-sm-10 col-md-10 ">
                    @if(isset($menu_item)  && $menu_item->menu_list_image)
                    @foreach(explode(";",$menu_item->menu_list_image) as $key => $image)
                    <span id="{{$key}}">
                    <image class="img-rounded" style="max-width:100px;max-height:100px" src={{config('app.url_file_view')}}{{$image}}><i class="glyphicon glyphicon-remove fa-lg" onclick="remove_image('{{$image}}','{{$key}}','{{$id}}',event)" style="position: relative;z-index:1111;top: -20px;right: 9px"></i></image>
                    </span>
                    @endforeach
                    @endif
                </div>
                <div class="col-md-2 col-sm-2"></div> 
                 <div class="col-sm-10 col-md-10 ">
                    <span class="green">Drag multiple files to the box below for multi upload or click to select files. This is for demonstration purposes only, the files are not uploaded to any server.</span>
                    <div id="multiUploadImages" required class="dropzone">
                        
                    </div>
                </div>    
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-md-2">Description</label>
                <div class="col-sm-10 col-md-10">                
                    <textarea id="description"  class=" texteditor form-control" name="menu_descript">{{isset($menu_item->menu_descript)? $menu_item->menu_descript:old('menu_descript')}}</textarea>
                    <span style="color: red">{{$errors->first('menu_descript')}}</span>             
                </div> 
            </div>
            <div class="row form-group">
                <label class="col-sm-3 col-md-2">&nbsp;</label>
                <div class="col-sm-10 col-md-10">                
                    <button class="btn btn-sm btn-primary" id="meu_submit">SUBMIT</button>
                  <button class="btn btn-sm btn-default" onclick="window.location='{{route('menus')}}'" type="button">CANCEL</button>
                </div> 
            </div>
        
        </form>     
       
    </div>        
</div>
@stop
@section('scripts')
<script type="text/javascript" src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/dropzone/dist/dropzone.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/summernote/summernote-bs4.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/custom/bootstrap-filestyle.min.js') }}"></script>
<script type="text/javascript">
Dropzone.autoDiscover = false;    
function initializeDropZone() {
    /*references: https://smarttutorials.net/ajax-image-upload-using-dropzone-js-normal-form-fields-button-click-using-php/*/
    myDropzone = new Dropzone('div#multiUploadImages', {
           url: '{{ route('upload-multi-images') }}',
           headers: {
               'X-CSRF-TOKEN': '{!! csrf_token() !!}'
           },
           addRemoveLinks: true,
           autoProcessQueue: true,
           uploadMultiple: true,
           parallelUploads: 10,
           maxFiles: 20,
           maxFilesize: 2,
           acceptedFiles: ".jpeg,.jpg,.png,.gif",
           dictFileTooBig: 'Image is bigger than 2MB',
           addRemoveLinks: true,
            removedfile: function(file) {
                var name = file.name;
                $('#'+name.replace(/[^A-Z0-9]+/ig,'_')).val('');
                var _ref;
                 return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
              },
             init: function () {

            var myDropzone = this;

            this.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                var data = $("#menu_form").serializeArray();
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                });
                console.log(formData);

            });
        },
        successmultiple: function (file, response) {
            console.log(response);
            jQuery.each( response, function( i, val ) {
                var str = val.slice(val.lastIndexOf("/")+1);
                
            $('.list_image').append('<input type="hidden" name="multi_image_add[]" id="'+str.replace(/[^A-Z0-9]+/ig,'_')+'" value="'+val+'">');
            });

            $("#success-icon").attr("class", "fas fa-thumbs-up");
            $("#success-text").html(response.message);
        },
    });
}
function remove_image(src_image,id,menu_id,e){
    if(window.confirm("Are you sure you want to delete this service ?")){
        $('#'+id).remove();
        $.ajax({
            url: '{{route('remove-image-menu')}}',
            type: 'GET',
            dataType: 'html',
            data: {menu_id: menu_id,src_image:src_image},
        })
        .done(function(response) {
            $('#multi_image').val(response);
            toastr.success('Remove Image Menu Success!');
            //console.log(response);
        })
        .fail(function() {
            toastr.error('Remove Image Menu Error!')
            //console.log("error");
        });
    }
    else{
        e.preventDefault();
    }
    
}
function readURL(input) {
    if (input.files && input.files[0]) {
        $('img').show();
        var reader = new FileReader();
        reader.onload = function(e) {
            $($(input).attr("data-target")).attr('src', e.target.result);
            $($(input).attr("data-target")).hide();
            $($(input).attr("data-target")).fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }    
}
$(document).ready(function() { 
    $('textarea.texteditor').summernote({height: 150});
     if ($("input.checkFlat")[0]) {
        $('input.checkFlat').iCheck({
            radioClass: 'iradio_flat-green',
            checkboxClass: 'icheckbox_flat-green'
        });       
    }        
    initializeDropZone();
    $("input[type=file]").change(function() {
        readURL(this);
    });
});
 $(document).ready(function(){
        $("#meu_submit").on( "click", function(event){
            // validate form
            var validatorResult = $("#menu_form")[0].checkValidity();
            $("#menu_form").addClass('was-validated');
            if(!validatorResult){
                event.preventDefault();
                event.stopPropagation();           
                return;
            }else
            //form = document.createElement('#customer_form');
            $('#menu_form').submit();

        });

    });
</script>      


<script>
    //check validate
    $(document).ready(function(){

        var check = 0;
        $("input[name='menu_index']").on("blur",function(e){
            var str = $(this).val();
            if(str.length <=0){
                $(this).addClass('is-invalid');
                check = 1;
            }else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                check = 0;
            }
            checkSubmit(check);
        });
        
        $("input[name='menu_name']").on("blur",function(e){
            var str = $(this).val();
            if(str.length <=0){
                $(this).addClass('is-invalid');
                check = 1;
            }else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                check = 0;
            }
            checkSubmit(check);
        });


        function checkSubmit(check){
            if(check == 1){
                $("#meu_submit").attr('disabled',true);
            } else {
                $("#meu_submit").attr('disabled',false);
            }
        }

    });
</script> 
@stop

