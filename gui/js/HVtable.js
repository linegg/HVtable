$(document).ready(function(){
    enhance = $("input[name='editMode']:checked").val();
    value = '';

    $(".modal").modal({backdrop: 'static', keyboard: false});
    //height =  $(this).height()/7;
    $(".modal").modal().css({
        //"margin-top":height
    });
    $(".cancelUpdate").click(function(){
        $(".modal").modal('hide');
        $('td').removeClass('edit');
        $('th').removeClass('edit');
    });
    $("#cancelCreate").click(function(){
        $(".modal").modal('hide');
    });

    $(".deleteRow").click(function(){
        var tableId = $("#row_table_id").val();
        var rowId = $("#row_id").val();

        $.post('HVtableRes.php?action=deleteRow',{"table_id":tableId,"row_id":rowId},function(msg){
            window.location.reload();
        },'json');
    });

    $.fn.modal.Constructor.prototype.enforceFocus = function(){
        $(".inputFocus").select();
    }

    $("input[name='editMode']").change(function(){
        if($(this).val() == 0){
            enhance = 0;
            window.location.reload();
        }else{
            enhance = 1;
        }
    });

    $(".deleteColumn").click(function(){
        var tableId = $("#column_table_id").val();
        var columnId = $("#column_id").val();
        var tdIndex = $('#tdIndex').val();
        var trIndex = $('#trIndex').val();
        var maxGrade = $('#max_grade').val();
        var parentTrId = trIndex - 1;
        var parentTdId = columnId - 1;
        var pMethod = 0;
        
        if(maxGrade > 1)
        {
            while(1)
            {
                if($('tr').eq(parentTrId).find("#"+ parentTdId).length == 0){
                    pMethod = 1;
                    parentTdId --;
                    continue;
                }
                break;
            }
        }
       	
        $.post('HVtableRes.php?action=deleteColumn',{"table_id":tableId,"column_id":columnId,"p_column_id":parentTdId,"p_method":pMethod,"max_grade":maxGrade},function(msg){
            window.location.reload();
        },'json');
    });

    $(".tabBtn").click(function(){
        var tableId = $(this).attr("id");
        window.open('showTables.php?table=' + tableId);
    });

    $("#createTabBtn").click(function(){
        $("#createTab").modal('show');
    })

    $("#addRow").click(function(){
        var tableId = $("table").attr("id");
        $.post('HVtableRes.php',{"table_id":tableId,"action":"addRow"},function(msg){
            window.location.reload();
        },'json');
    });

    $("#addColumn").click(function(){
        var tableId = $("table").attr("id");
        $.post('HVtableRes.php',{"table_id":tableId,"action":"addColumn"},function(msg){
            window.location.reload();
        },'json');
    });

    $("#empty").click(function(){
        $.post('HVtableRes.php',{"table_id":0,"action":"emptyHV"},function(msg){
            window.location.reload();
        },'json');
    });

    $("#row_grade").change(function(){
        var rowGrade = $("#row_grade").val();
        if(rowGrade > 3){
            $("#row_grade").val(3);
        }
        else if(rowGrade < 1){
            $("#row_grade").val(1);
        }
    });

    $("#column_grade").change(function(){
        var columnGrade = $("#column_grade").val();
        if(columnGrade > 2){
            $("#column_grade").val(2);
        }
        else if(columnGrade < 1){
            $("#column_grade").val(1);
        }
    });

    $("form").submit(function(e){
        e.preventDefault();
        if(this.id == "createNewForm"){
            var tableName = $("#table_name").val();
            var rowGrade = $("#row_grade").val();
            var columnGrade = $("#column_grade").val();
            var template = $("input[name=templateUse]:checked").val();

            $.post('HVtableRes.php',{"table_id":0,"table_name":tableName,"row_grade":rowGrade,"column_grade":columnGrade,"action":"createNew","template":template},function(msg){
                window.location.reload();
            },'json');
        }
        else if(this.id == "updateColumnForm")
        {
            $.post('HVtableRes.php?action=updateColumn',$(this).serialize(),function(msg){
                window.location.reload();
            },'json');
        }
        else if(this.id == "updateRowForm")
        {
            $.post('HVtableRes.php?action=updateRow',$(this).serialize(),function(msg){
                window.location.reload();
            },'json');
        }
        else if(this.id == "updateValueForm")
        {
            if(enhance == 1){
                $("#value_name").val($(".note-editable").html());
            }
            $.post('HVtableRes.php?action=updateValue',$(this).serialize(),function(msg){
                window.location.reload();
            },'json');
        }
        else if(this.id == "updateRemarkForm")
        {
            if(enhance == 1){
                $("#remark").val($(".note-editable").html());
            }
            $.post('HVtableRes.php?action=updateRemark',$(this).serialize(),function(msg){
                window.location.reload();
            },'json');
        }
        else if(this.id == "importExcelForm")
        {
            var tableId = $("#import_table_id").val();
            var rowGrade = $("#row_grade").val();
            var columnGrade = $("#column_grade").val();
            var filename = $("#importFile").val();	
            
            checkExName(filename);
            $.ajaxFileUpload
            (
                {
                    url: './importExcel.php?table_id='+tableId+"&row_grade="+rowGrade+"&column_grade="+columnGrade, //用于文件上传的服务器端请求地址
                    secureuri:false, //是否需要安全协议，一般设置为false
                    fileElementId:'importFile', //文件上传域的ID
                    dataType: 'json', //返回值类型 一般设置为json
                    success: function (data,status)  //服务器成功响应处理函数
                    {
                    	//window.location.reload();	
                    },
                    error: function (data,status)//服务器响应失败处理函数
                    {
                    	window.location.reload();	
                    }
                }
            )
            return false;
        }
    });

    //编辑表头
    $(".columnStyle").dblclick(function()
    {
        var columnId = $(this).attr("id");
        var tableId = $(this).parents("table").attr("id");
        var trIndex = $(this).parent("tr").index();
        $(this).addClass('edit');

        $.post('HVtableRes.php?action=checkColumn',{"column_id":columnId,"table_id":tableId},function(msg){

            $(".deleteColumn").hide();
            $("#column_merge").removeClass("readonlyStyle");
            $("#column_merge").remove("readonly","readonly");

            $("#column_name").val(msg.column_name);
            $("#column_merge").val(msg.merge);

            if(msg.grade == 1){
                $("#column_merge").attr("readonly","readonly");
                $("#column_merge").addClass("readonlyStyle");
                $("#column_merge").val(msg.merge);
                $(".deleteColumn").show();

                $("#trIndex").val(trIndex);
                $("#max_grade").val(msg.max_grade);
            }
        },'json');

        $("#column_table_id").val(tableId);
        $("#column_id").val(columnId);

        $("#updateColumn").modal('show');
        $("#column_name").focus();
    });

    $(".columnStyle").mousedown(function(e){
        if(e.which == 3)
        {
            var tdIndex = $(this).index();
            var trIndex = $(this).parent("tr").index();
            var maxTrIndex = $("table").find("tr:last").index();
            $('.hvTable tr').show();
            for(i = trIndex + 1;i<=maxTrIndex;i++){
                if($('.hvTable tr').eq(i).find("td").eq(tdIndex).html() == "")
                {
                    $('td').removeAttr("rowspan");
                    $('td').removeClass("displayNone");
                    $('.hvTable tr').eq(i).hide();
                }
            }
        }
    });

    $(".rowStyle").dblclick(function(){
        var rowId = $(this).attr("id");
        var tableId = $(this).parents("table").attr("id");
        $(this).addClass('edit');

        $.post('HVtableRes.php?action=checkRow',{"row_id":rowId,"table_id":tableId},function(msg){
            $(".deleteRow").hide();
            $("#row_merge").removeClass("readonlyStyle");
            $("#row_merge").remove("readonly","readonly");

            $("#row_name").val(msg.row_name);
            $("#row_merge").val(msg.merge);

            if(msg.mergeL == 1){
                $("#row_merge").attr("readonly","readonly");
                $("#row_merge").addClass("readonlyStyle");
                $("#row_merge").val(msg.merge);
                $(".deleteRow").show();
            }
        },'json');

        $("#row_table_id").val(tableId);
        $("#row_id").val(rowId);
        $("#updateRow").modal('show');
    });

    $(".valueStyle").dblclick(function(){
        var valueId = $(this).attr("id");
        var tableId = $(this).parents("table").attr("id");
        $('.inputStyle').val('');

        $(this).addClass('edit');

        $.post('HVtableRes.php?action=checkValue',{"value_id":valueId,"table_id":tableId},function(msg){
            var v;
            var p;
            if(msg == null){
                v = '';
                p = '';
            }else{
                v = msg.value;
                p = msg.postil;
            }

            $("#value_name").val(v);
            $("#value_postil").val(p);

            $("#value_id").val(valueId);
            $("#value_table_id").val(tableId);

            if(enhance == 1)
            {
                $("#value_name").summernote({
                    toolbar:[
                        ['style',['clear','bold','italic','underline']],
                        ['fontsize',['fontname','fontsize']],
                        ['color',['color']],
                        ['height',['height']],
                        ['insert',['link']]
                    ],
                    disableDragAndDrop:false
                });
                $(".note-editable").html(v);
            }
            $("#updateValue").modal('show');
        },'json');
    });

    $(".remarkStyle").dblclick(function(){
        var tableId = $(this).parents("table").attr("id");
        var notes;
        $(this).addClass('edit');
        $("#remark").text("");

        $.post('HVtableRes.php?action=checkRemark',{"table_id":tableId},function(msg){
            if(msg == null)
            {
                msg.notes = "NONE";
            }
            $("#remark").text(msg.notes);
            notes = msg.notes;

            $("#remark_table_id").val(tableId);
            if(enhance == 1){
                $("#remark").summernote({
                    toolbar:[
                        ['style',['clear','bold','italic','underline']],
                        ['fontsize',['fontname','fontsize']],
                        ['color',['color']],
                        ['height',['height']],
                        ['insert',['link']]
                    ],
                    disableDragAndDrop: false
                });
                $(".note-editable").html(notes);
            }
            $('#updateRemark').modal('show');
        },'json');
    });

    $("#importExcelBtn").click(function(){
        var tableId = $('.hvTable').attr("id");
        $("#import_table_id").val(tableId);
        $("#importExcel").modal('show');
    });
    
});

function checkExName(filename){	
	filename = filename.split("\\");//这里要将 \ 转义一下
	var name = filename[filename.length - 1];
	if(name == '')
	{
		alert('请选择文件');
		throw new Error("Nothing file");
	}
	pos = name.lastIndexOf(".");
	exname = name.substring(pos,name.length);
	if(!(exname.toLowerCase() == ".xls"))
	{
		alert("上传的文件必须为xls格式");
		throw new Error("Illegal file fomat");
	}
}