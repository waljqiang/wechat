function show(obj){
	$(obj).slideToggle();
}

function showParent(obj, number){
	number = number-1;
	var obj = $(obj,parent.document);
	obj.css("display","block");
	$("li a",parent.document).removeClass("selecta");
	obj.find("li:eq("+number+")").find("a").addClass("selecta");
}

function showSelect(obj){
	$("li a").removeClass("selecta");
	$(this).find("a").addClass("selecta");
}