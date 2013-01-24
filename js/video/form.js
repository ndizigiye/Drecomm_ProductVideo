/* ------------------------------------------------------------------------
Class: Form.php
Use: javascript for the video edit form in de backend
Author: Armand
Version: 0.0.1
------------------------------------------------------------------------- */

var $j = jQuery.noConflict();

/*change the div value*/
function changeDivVisibility() {
    if (!isValidVideo($j('#file').val())) {
        alert("please choose a valid file");
    } else {
    	$j("#uploading").css("display", "block");
    }
}

/* display ajax loader */
function loading() {
    $j("#loading-mask").css("display", "block");
}

/* hide ajax loader */
function hide_loading() {
    $j("#loading-mask").css("display", "none");
}

/* check if the file format is valid */
function isValidVideo(file) {
    var ext = getExtension(file);
    switch (ext.toLowerCase()) {
    case 'swf':
    case 'mov':
    case 'flv':
    case 'wmv':
    case 'avi':
    case '3gp':
    case 'mp4':
    case 'mpegs':
    case '3g2':
    case 'mpg':
        return true;
    }
    return false;
}

/* alert if format is not supported and reset the value of #file*/
function onChange(file) {
    if (!isValidVideo(file)) {
        alert('sorry this format is not supported');
        $j('#file').val("");
    }
}

/* get the file extension */
function getExtension(file) {
    return file.split('.').pop();
}

/* set the url input field with the url of the uploaded video */
function setUrl() {
    var valueToInsert = $j('#youtubelink').text();
    $j('#url').text(valueToInsert);
}