/*
 * Author: Joshua Boley
 *
 * simFormSubmit(path, method, data[, callback]) : void
 *
 * Simulates a form submission, allowing a page to send data to the server as POST form field data.
 * If a callback method is given, assumes data should be sent asynchronously and will not trigger
 * a page (re)load.
 *
 * Args:
 *                 path     Path to data processing page
 *               target     Target element, usually document but could be iframe child document
 *                 data     Associate array object with 'key': value pairs; e.g.
 *                              { 'data1': value1, 'data2': value2, ... }
 *  (optional) callback     Function called when server results are received; asynchronous
 */
var simFormSubmit = function (path, target, data, callback) {
    var $doc = target;      // Proxy for target document, switched out with iframe child document if callback given

    // If a callback is given, assume call should be asynchronous
    if (typeof (callback) !== 'undefined') {
        // Create hidden iframe for asynchronous interaction with server, set onload event to callback
        var $iframe = document.createElement("iframe");
        $iframe.setAttribute("style", "display: none;");
        $iframe.contentWindow.onload = callback;

        // Attach iframe to document and replace $doc object with iframe child document object
        document.body.appendChild($iframe);
        $doc = $iframe.contentDocument;
    }

    // Create hidden form
    var form = $doc.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", path);

    // Create, populate and attach hidden form fields
    for(var fieldName in data) {
        if(data.hasOwnProperty(fieldName)) {
            var hiddenField = $doc.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "sub-" + fieldName);
            hiddenField.setAttribute("value", data[fieldName]);

            form.appendChild(hiddenField);
        }
    }

    // Append to doc and force submit
    $doc.body.appendChild(form);
    form.submit();
}