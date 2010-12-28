$(document).ready(function(){
	
	$("a#"+window.selectItemId).addClass('active_menu-nav');

	$("input:first").focus();
	
	if(window.errorId != '0'){
		$("#"+ errorId).focus();
	}

	//set window with in ie6
	var h = $(".componentLeft").height() > $(".componentRight").height() ? $(".componentLeft").height() : $(".componentRight").height();
	$("#mediaPosts").height(h);
	
	//set more link of index left of image news
	$("span.rightLink").each(function(i){
		this.style.top = $(this).parent().height() -5;
	});
	
	// sub class
	$(".conSub").mouseover(function(){
		$(this).siblings().show();
		$(this).addClass("selected"); 
	}).bind("mouseleave click",function(){
		$(".subClass").hide();
		$(this).removeClass("selected");
	});
	$(".subClass").mouseover(function(){
		$(this).show();
		$(this).siblings().addClass("selected");
	}).bind("mouseleave click",function(){
		$(this).hide();
		$(this).siblings().removeClass("selected");
	});
	
	var photoWidth=520;
	//load resize photo
	if(curImgUrl)
	    $("#myphoto").loadthumb(baseUrl+photoPath+curImgUrl, imgComplete);

	//resizeMyphoto();
	//load pre photo
	$("#leftHandle, #btnPre").click(function(){
		prePhoto();
	});
	//load next photo
	$("#rightHandle, #btnNext").click(function(){
		nextPhoto();
	});

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
		url : baseUrl + '/photos/prephoto',
		beforeSend: function(){
		    return true;
	    },
	    data: { id:photoId },
		success : function(data) {
	    	if(data.photo_id == "last"){
	    		alert("已经到第一张了");
	    	}
	    	else{	
	    		$("#myphoto").fadeOut();
	    		$("#cacheDiv").fadeIn();
	    		$("#myphoto").loadthumb(baseUrl+photoPath+data.string, imgComplete);
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
		url : baseUrl + '/photos/nextphoto',
		beforeSend: function(){
		    return true;
	    },
	    data: { id:photoId },
		success : function(data) {
	    	if(data.photo_id == "last"){
	    		alert("已经到最后一张了");
	    	}
	    	else{	
	    		$("#myphoto").fadeOut();
	    		$("#cacheDiv").fadeIn();
	    		$("#myphoto").loadthumb(baseUrl+photoPath+data.string, imgComplete);
	    		$("#photoName").html(data.photo_name);
	    		photoId = data.photo_id;
	    	}
		}
	});
}
function imgComplete() 
{ 
	var width = this.width;
	if(width > 520) {
		width = 520;
	}
	
	$("#myphoto").stop(true,true).attr("src",this.src).fadeIn('fast'); 
	$("#cacheDiv").stop(true,true).fadeOut();
	resizeMyphoto(width);
	return true;
	
}

//preload photo
jQuery.fn.loadthumb = function(url,callback) {
	var img = new Image();
	var timeImg;
	img.src = url;
	if(img.complete) { 
		callback.call(img);
	}
	
	img.onload = function(){
		callback.call(img);
	
	};

	
};
function SetHome(obj,vrl)
{
    try
    {
            obj.style.behavior='url(#default#homepage)';obj.setHomePage(vrl);
    }
    catch(e){
            if(window.netscape) {
                    try {
                            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect"); 
                    } 
                    catch (e) { 
                            alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将[signed.applets.codebase_principal_support]设置为'true'"); 
                    }
                    var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
                    prefs.setCharPref('browser.startup.homepage',vrl);
             }
    }
}
function MM_preloadImages() { //v3.0
	  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
	    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
	    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}