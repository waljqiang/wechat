function show(obj){
	$(obj).slideToggle();
}

function showParent(obj, className){
	var obj = $(obj,parent.document);
	obj.css("display","block");
	$("li a",parent.document).removeClass("selecta");
	obj.find("li").each(function(){
		_obj = $(this).find("a");
		if(className == _obj.text()){

			_obj.addClass("selecta");
		}
	});
}

function showSelect(obj){
	$("li a").removeClass("selecta");
	$(this).find("a").addClass("selecta");
}