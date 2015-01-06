//Applies cascading behavior for the specified dropdowns
function applyCascadingDropdown(sourceId, targetId) {
    var source = document.getElementById(sourceId);
    var target = document.getElementById(targetId);
    if (source && target) {
        source.onchange = function() {
            displayOptionItemsByClass(target, source.value);
        }
        displayOptionItemsByClass(target, source.value);
    }
}

//Displays a subset of a dropdown's options
function displayOptionItemsByClass(selectElement, className) {
    if (!selectElement.backup) {
        selectElement.backup = selectElement.cloneNode(true);
    }
    var options = selectElement.getElementsByTagName("option");
    for(var i=0, length=options.length; i<length; i++) {
        selectElement.removeChild(options[0]);
    }
    var options = selectElement.backup.getElementsByTagName("option");
    for(var i=0, length=options.length; i<length; i++) {
        if (options[i].className==className)
            selectElement.appendChild(options[i].cloneNode(true));
    }
}

//Applies cascading behavior for the specified dropdowns
function applyCascadingDropdown_optgp(sourceId, targetId) {
    var source = document.getElementById(sourceId);
    var target = document.getElementById(targetId);
    if (source && target) {
        source.onchange = function() {
            displayOptionItemsByClassn_optgp(target, source.value);
        }
        displayOptionItemsByClassn_optgp(target, source.value);
    }
}

//Displays a subset of a dropdown's options
function displayOptionItemsByClassn_optgp(selectElement, className) {
    if (!selectElement.backup) {
        selectElement.backup = selectElement.cloneNode(true);
    }
    var options = selectElement.getElementsByTagName("optgroup");
    for(var i=0, length=options.length; i<length; i++) {
        selectElement.removeChild(options[0]);
    }
    var options = selectElement.backup.getElementsByTagName("optgroup");
    for(var i=0, length=options.length; i<length; i++) {
        if (options[i].className==className)
            selectElement.appendChild(options[i].cloneNode(true));
    }
}// JavaScript Document