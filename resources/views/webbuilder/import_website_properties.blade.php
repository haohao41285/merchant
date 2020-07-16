@extends('layouts.master')
@section('title', 'Services | Import')
@section('styles')
 <link href="{{ asset('plugins/iCheck/skins/flat/green.css') }}" rel="stylesheet">  
 <link href="{{ asset('plugins/dropzone/dist/dropzone.min.css') }}" rel="stylesheet">
 <style>
     .dropzone {
        border: 2px solid #757575;
    }
 </style>    
@stop
@section('content')
@if (session('status'))
    <div class="alert alert-info">{{session('status')}}</div>
@endif
 <div class="x_panel">
     <div class="x_title">
         <h3>Import Website Properties</h3>
     </div>
    <div class="x_content">
        <form  method="post" id="service-import-form" enctype="multipart/form-data" name="service-import-form">
            {{ csrf_field() }}
            <div class="col-md-6">
                    <div class="row col-md-12">
                    <a href="{{route('templateWebsiteProperties')}}" class="blue">Download an import template spreadsheet</a>
                </div>
                <div class="row col-md-10">    
                    <input type="file" id="fileImport" name="fileImport" data-buttonText="Select a File" class="form-control form-control-sm">        
                    
                </div>                
               {{--  <div class="clear row  form-group" style="padding-top:20px;">    
                    <label class="col-auto">Begin Row Index</label>
                    <div class="col-auto">
                        <input type='number' name="begin_row" class="form-control form-control-sm" value="0"/>
                    </div>    
                    <label class="col-auto">End Row Index</label>
                    <div>
                        <input type='number' name="end_row" class="form-control form-control-sm" value="1000"/>
                     </div>     
                </div>  --}}
               {{--  <div class="clear row form-group">
                    <label class="col-auto">
                        <input type="checkbox" disabled="true" class="checkFlat"  checked="checked"> Ignore first row 
                    </label>
                </div>   
                <div class="clear row form-group">
                    <label class="col-auto">
                        <input type="checkbox" class="checkFlat" name="check_update_exist" checked="checked"> Update existing Categories & Services
                    </label>
                </div> --}}
                <div class="row col-md-10">   
                     <button class="btn btn-primary" >SUBMIT</button>                  
                </div>   
            </div>
         {{--    <div class="col-md-6">
                <div class="row form-group">
                 <div class="">
                    <span class="green">Drag multiple files to the box below for multi upload or click to select files. This is for demonstration purposes only, the files are not uploaded to any server.</span>
                    <div id="multiUploadImages" class="dropzone">
                        
                    </div>
                    <span class="list_image"></span>
                </div>    
            </div>
            </div> --}}
            

        </form>
    </div>        
</div>    
@stop
@section('scripts')
<script type="text/javascript" src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/dropzone/dist/dropzone.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/custom/bootstrap-filestyle.min.js') }}"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;    
    function initializeDropZone() {
    /*references: https://smarttutorials.net/ajax-image-upload-using-dropzone-js-normal-form-fields-button-click-using-php/*/
    myDropzone = new Dropzone('div#multiUploadImages', {
           url: '{{ route('upload-image-service') }}',
           headers: {
               'X-CSRF-TOKEN': '{!! csrf_token() !!}'
           },
           addRemoveLinks: true,
           autoProcessQueue: true,
           uploadMultiple: true,
           parallelUploads: 5,
           maxFiles: 10,
           maxFilesize: 2,
           acceptedFiles: ".jpeg,.jpg,.png,.gif",
           dictFileTooBig: 'Image is bigger than 2MB',
        init: function () {

            var myDropzone = this;
            
            this.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                var data = $("#service-import-form").serializeArray();
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                });
                // console.log(formData);

            });
        },
        error: function (file, response){
            try {
                var res = JSON.parse(response);
                if (typeof res.message !== 'undefined' && !$modal.hasClass('in')) {
                    $("#success-icon").attr("class", "fas fa-thumbs-down");
                    $("#success-text").html(res.message);
                    $modal.modal("show");
                } else {
                    if ($.type(response) === "string")
                        var message = response; //dropzone sends it's own error messages in string
                    else
                        var message = response.message;
                    file.previewElement.classList.add("dz-error");
                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        node = _ref[_i];
                        _results.push(node.textContent = message);
                    }
                    return _results;
                }
            } catch (error) {
                //console.log(error);
            }

        },
        successmultiple: function (file, response) {
            console.log(response);
            //response = JSON.parse(response);
            jQuery.each( response, function( i, val ) {
                var str = val.slice(val.lastIndexOf("/")+1);
                
                $('.list_image').append('<input type="hidden" name="multi_image_cate[]" id="'+str.replace(/[^A-Z0-9]+/ig,'_')+'" value="'+val+'">');
            });
            $("#success-icon").attr("class", "fas fa-thumbs-up");
            $("#success-text").html(response.message);
            //$modal.modal("show");
        },
        completemultiple: function (file, response) {
            //console.log(file, response, "completemultiple");
            //$modal.modal("show");
        },
        reset: function () {
            console.log("resetFiles");
            this.removeAllFiles(true);
        }
    });
}

function service(file_input)
{
    $(`#${file_input}`).click();
}

$(document).ready(function() {  
    if ($("input.checkFlat")[0]) {
        $('input.checkFlat').iCheck({
            checkboxClass: 'icheckbox_flat-green'
        });       
       
    }
     $('#fileImport').filestyle({ 
        text : 'Choose File',
        btnClass : 'btn-primary'
    }); 

    initializeDropZone();
    
    if ($("input.checkFlat")[0]) {
        $('input.checkFlat').iCheck({
            checkboxClass: 'icheckbox_flat-green'
        });       
       
    }
     $('#fileImport').filestyle({ 
        text : 'Choose File',
        btnClass : 'btn-primary'
    });     
    
}); 
</script>   
@stop

