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
	
	customCreateElement("input", null, parent, undefined, {"type": "submit", "value": "Crear viaje", "class": "mt8"});
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

function ddd(targetID){
	var myElement = document.getElementById(targetID);
	var parent = myElement.parentElement;

	if(myElement.nextElementSibling !== null){
		// Untoggle now
		if(myElement.nextElementSibling.id === "sub"+targetID){
			document.getElementById("sub"+targetID).remove();
			return;
		}
	}

	// Clear old subtravels
	var subtravels = document.getElementsByClassName("subtravel");
	for(var i = 0; i < subtravels.length; i++){
		subtravels[i].remove();
	}
	
	// Show new subtravels
	var element = document.createElement("tr");
	element.setAttribute("id", "sub"+targetID);
	element.classList.add("subtravel", "faded-out");

	requestAnimationFrame(() => {
		element.classList.remove("faded-out");
	})

	//console.log(parent);

	if(parent.lastElementChild.id === targetID){
		// append
		parent.append(element);
	}
	else{
		parent.insertBefore(element, myElement.nextElementSibling);
	}

	// añadir td con colspan
	var tdFather = customCreateElement("td", null, element, undefined, {"colspan": "5"});

	// add buttons
	var tmpBtn = customCreateElement("button", "Añadir gasto ", tdFather, undefined, {class: "customBtn btnMargin"});
	customCreateElement("i", null, tmpBtn, undefined, {class: "fas fa-plus"});

	// add event 
	tmpBtn.addEventListener("click", function(){
		document.getElementsByName("travel_id")[0].value = targetID;
		document.getElementById("add_expense").submit();
	});

	var tmpBtn = customCreateElement("button", "Balance ", tdFather, undefined, {class: "customBtn btnMargin"});
	customCreateElement("i", null, tmpBtn, undefined, {class: "fas fa-coins"});

	var tmpBtn = customCreateElement("button", "Gestionar usuarios ", tdFather, undefined, {class: "customBtn btnMargin"});
	customCreateElement("i", null, tmpBtn, undefined, {class: "fas fa-users-cog"});

	// add table
	// TO DO
	if(travel_data[targetID].length !== 0){
		var expensesTable = customCreateElement("table", null, tdFather, undefined, {style: "width: 100%; border: 1px solid #01161e;"});

		var tmp = customCreateElement("tr", null, expensesTable, undefined, {});
		customCreateElement("th", "Fecha", tmp, undefined, {});
		customCreateElement("th", "Concepto", tmp, undefined, {});
		customCreateElement("th", "Pagador", tmp, undefined, {});
		customCreateElement("th", "Cantidad", tmp, undefined, {});


		for(var i = 0; i < travel_data[targetID].length; i++){

			var tmp = customCreateElement("tr", null, expensesTable, undefined, {});

			var date = new Date(travel_data[targetID][i]["date"]);

			customCreateElement("td", date.getDate() + "/" + (date.getMonth()+1) + "/" + date.getFullYear() + " " + date.getHours() + ":" + date.getMinutes(), tmp, undefined, {});
			customCreateElement("td", travel_data[targetID][i]["concept"], tmp, undefined, {});
			customCreateElement("td", travel_data[targetID][i]["username"], tmp, undefined, {}); // change to username
			customCreateElement("td", travel_data[targetID][i]["quantity"], tmp, undefined, {});

		}
	}
	else{
		customCreateElement("p", "No hay ningun gasto en este viaje, haz clic en Añadir gasto para empezar a gestionar tus gastos.", tdFather, undefined, {});
	}
}

function main(){

	var btn = document.getElementById("addTravel");
	
	btn.addEventListener("click", travelFormControl);	
	
	var utilOrderForm = document.getElementById("utilOrderForm");
	var orderElements = document.getElementsByClassName("order");
	
	orderElements[0].addEventListener("click", function(){
		utilOrderForm.children[0].value = orderElements[0].name;
		utilOrderForm.submit();
	});

	orderElements[1].addEventListener("click", function(){
		utilOrderForm.children[0].value = orderElements[1].name;
		utilOrderForm.submit();
	});

}

window.onload = main();