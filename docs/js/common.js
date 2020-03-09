function show(obj){
	$(obj).slideToggle();
}

function showParent(obj, number){
	number = number-1;
	var obj = $(obj,parent.document);
	obj.css("display","block");
	obj.find("li:eq("+number+")").find("a:eq("+number+")").addClass("selecta");
}