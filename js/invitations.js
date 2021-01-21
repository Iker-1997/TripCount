function addNewEmailBox(){
	var emailsContainer = document.getElementById("emails");
	var q = emailsContainer.children.length;
	var container = customCreateElement("div", null, emailsContainer, null, {id: "emailContainer" + (q+1)});
	customCreateElement("input", null, container, null, {name: "email[]", type: "email", placeholder: "email@example.com", class: "emailInput"});
	customCreateElement("button", "-", container, null, {id: "delBtn" + (q+1), onclick: "deleteEmailContainer(this)", class: "emailRemoveBtn"});
}

function deleteEmailContainer(element){
	element.parentElement.remove();
}

function main(){
	var addEmailBtn = document.getElementById("addParticipant");
	

	addEmailBtn.addEventListener("click", addNewEmailBox);
}

window.onload = main();