/**
 * Install:
 *
 * Copy filemanager folder to your www folder (responsive filemanager).
 * Include this file and use contents of init.latte
 *
 * It is required to declare global variables to make this code work properly.
 * Variables are:
 * var filemanager_basePath = '' - as in Nette's templates
 * var filemanager_title = 'My File Manager'
 * var filemanager_accessKey = 'key' - access key to filemanager
 * var filemanager_removeTitle = 'Remove'
*/

if (typeof filemanager_removeTitle == 'undefined')
    var filemanager_removeTitle = 'Remove';

if (typeof filemanager_title == 'undefined')
    var filemanager_title = 'Filemanager';

if (typeof filemanager_basePath == 'undefined')
    alert('Javascript variable filemanager_filemanager_basePath is not defined!');


var modal = '\
<div class="modal fade" id="myModal">\
    <div class="modal-dialog modal-lg">\
        <div class="modal-content">\
            <div class="modal-header">\
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                <h4 class="modal-title">'+filemanager_title+'</h4>\
            </div>\
            <div class="modal-body">\
                <iframe id="filemanager-modal-iframe" width="100%" height="500" src="" frameborder="0" style="overflow: scroll; overflow-x: hidden; overflow-y: scroll; "></iframe>\
            </div>\
        </div>\
    </div>\
</div>';

function responsive_filemanager_callback(field_id){
    $("#myModal .close").click();

    $(".picture-preview[data-id]").each(function(){
        create_preview_box(this);
    });

    $(".file-preview[data-id]").each(function(){
        create_preview_box(this);
    });
}

function create_preview_box(that)
{
    var path = $("#"+$(that).data('id')).val();

    if (path) {
        if ($(that).hasClass('picture-preview'))
            $(that).html("<img class='img-thumbnail' style='max-width:200px' src='"+filemanager_basePath+path+"'><button type='button' class='close remove-file'><span aria-hidden='true'>&times <small>"+filemanager_removeTitle+"</small></span></button>");
        else
            $(that).html("<button type='button' class='close remove-file'><span aria-hidden='true'>&times <small>"+filemanager_removeTitle+"</small></span></button>");
    }
}

jQuery(document).ready(
    function() {

        $(".picture-preview[data-id]").each(function(){
           create_preview_box(this);
        });

        $(".file-preview[data-id]").each(function(){
            create_preview_box(this);
        });

        $("body").delegate(".remove-file","click", function(e){
            $("#"+$(this).parent().attr('data-id')).val('');
            $(this).parent().html('');
        });


        $(".file-external").click(function(){
            var id = $(this).attr('id');
            var type = $(this).data('type');

            $("body").append(modal);
        $("#filemanager-modal-iframe").attr("src", filemanager_basePath+"/filemanager/dialog.php?type="+type+"&akey="+filemanager_accessKey+"&field_id="+id);

        $("#myModal").modal();
        $("#myModal").on('hidden.bs.modal', function (e) { $(this).remove(); })
    });

}
);
