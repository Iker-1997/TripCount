function customCreateElement(tag, text, parent, beforeElement, attributes) {
    let element = document.createElement(tag);
    if (text) {
        let txtNode = document.createTextNode(text);
        element.appendChild(txtNode);
    }
    if (attributes != null) {
        for (var key in attributes){
            element.setAttribute(key, attributes[key]);
        }
    }
    if (beforeElement != undefined) {
        customNextElement = beforeElement.nextElementSibling;
        insertAfter(element,lastCurso);
    } else {
        parent.appendChild(element);
    }
	
	return element;
}

function deleteThis(element){
	element.remove();
}