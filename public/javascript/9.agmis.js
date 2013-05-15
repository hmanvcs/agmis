// Make columns heights equal
function equalHeight(group) {
    tallest = 0;
    group.each(function() {
        thisHeight = $(this).height();
        if(thisHeight > tallest) {
            tallest = thisHeight;
        }
    });
    group.height(tallest);
}
// function to open a pop-up window with specific width
function openPopUpWindow(URL) {
	aWindow=window.open(URL,"newwindow","toolbar=no,scrollbars=no,status=no,location=no,resizable=no,menubar=no,width=650,height=500,left=400,top=100");
}

// function to open a pop-up window with specific width
function openPopUpWindowWithOptions(URL, width, height) {
	aWindow=window.open(URL,"newwindow","toolbar=no,scrollbars=no,status=no,location=no,resizable=no,menubar=no,width="+width+",height="+height+",left=400,top=100");
}
// function to obsfuscate email addresses from spammers
function contactUnobsfuscate() {
	
	// find all links in HTML
	var link = document.getElementsByTagName && document.getElementsByTagName("a");
	var contact, e;
	
	// examine all links
	for (e = 0; link && e < link.length; e++) {
	
		// does the link have use a class named "contact"
		if ((" "+link[e].className+" ").indexOf(" contact ") >= 0) {
		
			// get the obfuscated contact address
			contact = link[e].firstChild.nodeValue.toLowerCase() || "";
			
			// transform into real contact address
			contact = contact.replace(/dot/ig, ".");
			contact = contact.replace(/\(at\)/ig, "@");
			contact = contact.replace(/\s/g, "");
			
			// is contact valid?
			if (/^[^@]+@[a-z0-9]+([_\.\-]{0,1}[a-z0-9]+)*([\.]{1}[a-z0-9]+)+$/.test(contact)) {
			
				// change into a real mailto link
				link[e].href = "mailto:" + contact;
				link[e].firstChild.nodeValue = contact;
		
			}
		}
	}
}
window.onload = contactUnobsfuscate;

//close popup window and reload parent
function closeWindowAndReloadParent(){
	$(window).unload(function () { 
		//alert("Bye now!"); 
		window.opener.location.reload();			
	});
}

//if the default search term is present, clear it
function clearValue() {
	if($("#searchterm").val() == "Enter search term") {
		$("#searchterm").val("");
	} 
}
// if the default search term is empty, set it
function setValue() {
	if($("#searchterm").val() == "") {
		$("#searchterm").val("Enter search term");
	} 
}

/*
 * Disable the fields specified and add the class disabledfield
 * 
 * @param fieldid - The ID of the field to be disabled
 */
/*
 * Disable the fields specified and add the class disabledfield
 * 
 * @param fieldid - The ID of the field to be disabled
 */
function disableField(fieldid) {
	// disable the respective field and add the class disabled field		
	$("#" + fieldid).attr("disabled", "disabled").addClass('disabledfield');
} 
/*
 * Enable the fields specified and remove the class disabledfield
 * 
 * @param fieldid - The ID of the field to be enabled
 */
function enableField(fieldid) {
	// // hide the respective field and remove the class disabled field
	$("#" + fieldid).attr("disabled", false).removeClass('disabledfield');	
} 

/*
 * Hide the container with the specified id and disable all input fields in it
 * 
 * @param fieldid - The ID of the container to be hidden
 */
function disableContainerByID(fieldid) {
	// hide the respective tbody and disable any HTML controls
	$("#" + fieldid).hide();
	$("#" + fieldid + " input, #" + fieldid + " select, #" + fieldid + " textarea").attr("disabled", true);
} 
/*
 * Show the container with the specified id and enable all input fields in it
 * 
 * @param fieldid - The ID of the container to be shown
 */
function enableContainerByID(fieldid) {
	// hide the respective tbody and disable any HTML controls
	$("#" + fieldid).show();
	$("#" + fieldid + " input, #" + fieldid + " select, #" + fieldid + " textarea").attr("disabled", false);
}
 /*
 * Hide the container with the specified class and disable all input fields in it
 * 
 * @param fieldid - The class of the container to be hidden
 */
function disableContainerByClass(classid) {
	// hide the respective tbody and disable any HTML controls
	$("." + classid).hide();
	$("." + classid + " input, ." + classid + " select, ." + classid + " textarea").attr("disabled", true);
} 
/*
 * Show the container with the specified class and enable all input fields in it
 * 
 * @param fieldid - The class of the container to be shown
 */
function enableContainerByClass(classid) {
	// hide the respective tbody and disable any HTML controls
	$("." + classid).show();
	$("." + classid + " input, ." + classid + " select, ." + classid + " textarea").attr("disabled", false);
}
// function to do nothing
function doNothing(){	
}
// check that price is a valid number
function IsValidAmount(value){
	var ValidChars = "0123456789";
	var valid=true;
	var Char;
	
	for (i = 0; i < value.length && valid == true; i++){ 
		Char = value.charAt(i); 
		if (ValidChars.indexOf(Char) == -1) {
			valid = false;
		}
	}
	return valid;
}