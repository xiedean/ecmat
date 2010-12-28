$(document).ready(function(){
	$("table#data tr:even").removeClass('odd').addClass('even');
	$("table#data tr:odd").removeClass('even').addClass('odd');

	$("ul.data-narrow>li:even").removeClass('odd').addClass('even');
	$("ul.data-narrow>li:odd").removeClass('even').addClass('odd');

	$("#content_body_tbl").addClass("resizable");
	
	$("input:first").focus();
	if( errorId ){
		$("#"+errorId).focus();
	}
	
	// check all checkbox
	$(function () { 	
		$('#checkAll').click(function () {
			$(this).parents('table').find(':checkbox').attr('checked', this.checked);
		});

	});
	
	/**
	 * check any chckbox was checked
	 */
	var isSubmit = true;
	$(':submit[id="setGroup"]').bind("click",function(){
		if(!$(':checkbox[name="idsToDelete[]"]:checked').val()) {
			alert("请选择一个用户.");
			isSubmit = false;
			return;
		}
		if($("select#user_group").val() == 0) {
			alert("请选择一个分组.");
			isSubmit = false;
			return;
		}
		else {
			isSubmit = true;
		}
	});
	var form = $("#data_list_form");
	form.submit(function(){
		return isSubmit;
	});
	
	//delete photo
	$("a.delete").click(function(){
		var name = $(this).attr('name');
		var id = name.substr(name.indexOf('_')+1);
		$.ajax( {
			type : "POST",
			dataType: 'html',
			url : baseUrl + '/admin/albumns/delphoto',
			beforeSend: function(){
			    return confirm("您确定要删除 " + $("input[name='"+name+"']").val() + " ?");
		    },
		    data: { id:id },
			success : function(data) {
		    	if(data == "success" ){
		    		$("a[name='"+name+"']").parent().parent().remove();
		    	}
			}
		});
	});
	var photoWidth=520;
	//load resize photo
	resizeMyphoto();
	//load pre photo
	$("#leftHandle, #btnPre").click(function(){
		prePhoto();
	});
	//load next photo
	$("#rightHandle, #btnNext").click(function(){
		nextPhoto();
	});
	
	$("#class_id").change(function(){
		var hiddenValue = $("li.hidden").css("display");
		if( ($(this).val()=='42'|| $(this).val()=='43') && hiddenValue == "none") { 
			$("li.hidden").css("display","block");
		} else if(hiddenValue == "block" && $(this).val() != '42' && $(this).val() != '43') {
			$("li.hidden").css("display",'none');
		}
		
	});
	if($("#class_id").val() == '42' || $("#class_id").val() == '43') {
		$("li.hidden").css("display","block");
	}
});
function resizeMyphoto( width )
{
	if(!width){
		width = $("img#myphoto").width();
	}
	if(width){
	    $("img#myphoto").width(width);
	}

	if($("img#myphoto").width() > 520){
		$("img#myphoto").width(520);
		width = 520;
	}
	
	$("#leftHandle").css({
		"left": (520-width)/2+"px",
		"height": $("img#myphoto").height()+"px",
		"width": $("img#myphoto").width()/2-5+"px"
	});
	$("#rightHandle").css({
		"right": (520-$("img#myphoto").width())/2+"px",
		"height": $("img#myphoto").height()+"px",
		"width": $("img#myphoto").width()/2-5+"px"
	});

}

function prePhoto()
{
	$.ajax( {
		type : "POST",
		dataType: 'json',
		url : baseUrl + '/admin/albumns/prephoto',
		beforeSend: function(){
		    $("#cacheDiv").show();
		    return true;
	    },
	    data: { id:photoId },
		success : function(data) {
	    	if(data.photo_id == "last"){
	    		$("#cacheDiv").hide();
	    		alert("已经到第一张了");
	    	}
	    	else{	
	    		$("#cacheDiv").hide();
	    		$("#myphoto").loadthumb({"src":baseUrl+photoPath+data.string});
	    		$("#photoName").html(data.photo_name);

	    	    photoId = data.photo_id;
	    	}
		}
	});
}

function nextPhoto()
{
	$.ajax( {
		type : "POST",
		dataType: 'json',
		url : baseUrl + '/admin/albumns/nextphoto',
		beforeSend: function(){
		    $("#cacheDiv").show();
		    return true;
	    },
	    data: { id:photoId },
		success : function(data) {
	    	if(data.photo_id == "last"){
	    		$("#cacheDiv").hide();
	    		alert("已经到最后一张了");
	    	}
	    	else{	
	    		$("#cacheDiv").hide();
	    		$("#myphoto").loadthumb({"src":baseUrl+photoPath+data.string});
	    		$("#photoName").html(data.photo_name);
	    		photoId = data.photo_id;
	    	}
		}
	});
}
//preload photo
jQuery.fn.loadthumb = function(options) {
	options = $.extend({
		 src : ""
	},options);
	var _self = this;
	_self.hide();
	
	var img = new Image();
	$(img).load(function(){
		_self.attr("src", options.src);
		_self.show();
	}).attr("src", options.src);  //.atte("src",options.src)要放在load后面，
	var width = img.width;
	if(width > 520 ){
		width = 520;
	}
	resizeMyphoto(width);
	return _self;
};



