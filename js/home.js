var form_visible = false;

function createAddTravelForm(){
	var parent = document.getElementById("dynAddTravelForm");
	
	customCreateElement("form", null, parent, undefined, {"method": "POST", "action": ""});
	
	parent = parent.children[0];
	
	customCreateElement("input", null, parent, undefined, {"type": "hidden", "name": "action", "value": "add_travel"});
	
	customCreateElement("label", "Nombre del viaje:", parent, undefined, {"for": "travel_name", "class": "block"});
	customCreateElement("input", null, parent, undefined, {"type": "text", "id": "travel_name", "name": "travel_name", "maxlength": "32", "required": 1});
	
	customCreateElement("label", "Descripción:", parent, undefined, {"for": "description", "class": "block"});
	customCreateElement("textarea", null, parent, undefined, {"name": "description", "maxlength": "255", "required": 1});
	
	customCreateElement("label", "Divisa principal:", parent, undefined, {"for": "description", "class": "block"});
	customCreateElement("select", null, parent, undefined, {"name": "currency", "id": "currency", "required": 1});
	
	createCurrenciesOptions();
	
	customCreateElement("input", null, parent, undefined, {"type": "submit", "value": "Enviar", "class": "button"});
}

function travelFormControl(){
	if(form_visible){
		// lets delete it
		//deleteThis(document.getElementById("dynAddTravelForm").children[0]);
		//form_visible = false;
	}
	else{
		// lets add it
		createAddTravelForm();
		form_visible = true;
	}
}

function main(){

	var btn = document.getElementById("addTravel");
	
	btn.addEventListener("click", travelFormControl);	
	
	var orderControl = document.getElementsByClassName("order")[0];
	var utilOrderForm = document.getElementById("utilOrderForm");
	
	orderControl.addEventListener("click", function(){
		utilOrderForm.children[0].value = orderControl.name;
		utilOrderForm.submit();
	});

}

window.onload = main();