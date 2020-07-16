@extends('layouts.master')
@section('title', (isset($id)&&$id>0)?'Website Builder | Banners | Edit Banner':'Website Builder | Banners | Add Banner')
@section('styles')
 <link href="{{ asset('plugins/iCheck/skins/flat/green.css') }}" rel="stylesheet">  
 <link href="{{ asset('plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">  
@stop
@section('content')
<div class='x_panel x_panel_form'>
     <div class="x_title">
      <h3>
       @if(isset($id))
       Edit Banner
       @else
       Add Banner
       @endif
     </h3>
     </div>    
    <div class="x_content">
       
        <form method="post" action="{{route('save-banner')}}" id="banner_form" class="form-horizontal form-label-left" enctype="multipart/form-data">  
        @csrf
        <input type="hidden" name="ba_id" value="{{$id}}"/>                    
            <div class="row form-group">
               <label class="control-label col-md-2 col-sm-2 col-xs-12">Name</label>
               <div class="col-md-9 col-sm-9 col-xs-12">
                 <input type='text' required class="form-control form-control-sm{{ $errors->has('ba_name') ? ' is-invalid' : '' }}" name="ba_name" value="{{isset($ba_item->ba_name)? $ba_item->ba_name:old('ba_name')}}" />
             <span style="color: red">{{$errors->first('ba_name')}}</span>
               </div>
             </div>
            <div class="row form-group">
               <label class="control-label col-md-2 col-sm-2 col-xs-12">Index</label>
               <div class="col-md-9 col-sm-9 col-xs-12">
                   <input type='number' class="form-control form-control-sm{{ $errors->has('ba_index') ? ' is-invalid' : '' }}" name="ba_index" value="{{isset($ba_item->ba_index)? $ba_item->ba_index:old('ba_index')}}" />
               <span style="color: red">{{$errors->first('ba_index')}}</span>
               </div>
             </div>    
             {{-- <div class="row form-group">
              <label class="control-label col-md-2 col-sm-2 col-xs-12">Status</label>
              <div class="col-sm-5 col-md-4 form-inline">
                   <div class="radio">
                        <label>
                          <input type="radio" class="flat checkFlat" {{(!isset($ba_item))?"checked":""}} value="1" @if(isset($ba_item->enable_status)&&$ba_item->enable_status ==1) checked @endif name="enable_status">&nbsp;Enabled
                        </label>
                    </div>
                    <div class="radio" style="margin-left:10px;">
                        <label>
                          <input type="radio" class="flat checkFlat" value="0" @if(isset($ba_item->enable_status)&&$ba_item->enable_status ==0) checked @endif name="enable_status">&nbsp;Disabled
                        </label>
                    </div>
                </div> 
             </div> --}}
             <div class="row form-group">
               <label class="control-label col-md-2 col-sm-2 col-xs-12">Image</label>
                <div class="col-md-9 col-sm-9 col-xs-12" style="overflow: hidden;">
                    <div class="catalog-image-upload">
                           <div class="catalog-image-edit">
                            <input type="hidden" name="ba_image_old" value="{{isset($ba_item->ba_image)? $ba_item->ba_image:old('ba_image')}}" hidden>
                               <input type='file' id="imageUpload1" name="ba_image" value="{{isset($ba_item->ba_image)}}" data-target="#catalogImagePreview1" accept=".png, .jpg, .jpeg" />
                               <label for="imageUpload1"></label>
                           </div>
                           <div class="catalog-image-preview">
                               <img id="catalogImagePreview1" style='display:{{(isset($ba_item)&&$ba_item->ba_image!="")?"":"none"}}' src="{{config('app.url_file_view')}}{{isset($ba_item->ba_image)? $ba_item->ba_image:old('ba_image')}}" height="100px"/>
                           </div>
                       </div>
                 </div>
             </div>
             <span style="color: red">{{$errors->first('ba_image')}}</span>
              <div class="row form-group">
               <label class="control-label col-md-2 col-sm-2 col-xs-12">Description</label>
              <div class="col-md-9 col-sm-9 col-xs-12">
                   <textarea id="message" class="form-control texteditor" name="ba_descript" >{{isset($ba_item->ba_descript)? $ba_item->ba_descript:old('ba_descript')}}</textarea>
                 <span style="color: red">{{$errors->first('ba_descript')}}</span>
                 </div>
             </div> 
             
            <div class="row form-group">
               <label class="control-label col-md-2 col-sm-2 col-xs-12">&nbsp;</label>
               <div class="col-sm-6 col-md-6  form-group">
                   <button class="btn btn-sm btn-primary" id="submit">SUBMIT</button>
                   <button class="btn btn-sm btn-default" onclick="window.location='{{route('banners')}}'" type="button">CANCEL</button>
                </div>  
             </div>  
        </form>
    </div>        
</div>
@stop
@section('scripts')
<script type="text/javascript" src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/summernote/summernote-bs4.js') }}"></script>
<script type="text/javascript">
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
     if ($("input.checkFlat")[0]) {
        $('input.checkFlat').iCheck({
            radioClass: 'iradio_flat-green',
            checkboxClass: 'icheckbox_flat-green'
        });       
       
    }
    $('textarea.texteditor').summernote({height: 150});
    $("input[type=file]").change(function() {
        readURL(this);
    });
}); 
 $(document).ready(function(){
        $("#submit").on( "click", function(event){
            // validate form
            var validatorResult = $("#banner_form")[0].checkValidity();
            $("#banner_form").addClass('was-validated');
            if(!validatorResult){
                event.preventDefault();
                event.stopPropagation();           
                return;
            }else
            //form = document.createElement('#customer_form');
            $('#banner_form').submit();
        });

    });
</script>      

<script>
    //check validate
    $(document).ready(function(){

        var check = 0;
        
        $("input[name='ba_name']").on("blur",function(e){
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
                $("#submit").attr('disabled',true);
            } else {
                $("#submit").attr('disabled',false);
            }
        }

    });
</script> 
@stop

