/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var dateparameters = {
    showOn: "button",
    buttonImage: siteurl + "img/calendar.png",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    required: true,
    dpDate: true,
    maxDate: '0d',
    showAnim: "fadeIn",
    yearRange: "-100:+0",
    //dateFormat: 'yy-mm-dd'
    dateFormat: 'mm-dd-yy'
};

$(document).ready(function () {
    //===nano scroller pain effect
	
	$(".nano").nanoScroller({ alwaysVisible: false });
	$(".submenu").addClass("hidden");
	$(".pane").css("display","block");
	$(".slider").css("display","block");
		
    //==========functionality when user clicks on admin menus
    $(".popup-for-admin").click(function(){
        alertbootbox("For this functionality, go to Admin Portal in www.ndorse.net");
        return false;
    })
    
    $("#datepicker_startdate").datepicker(dateparameters);
    $("#datepicker_enddate").datepicker(dateparameters);
    //=============refreshing the page when refresh is clicked
    $("#refresh").click(function () {
        window.location.reload();
    });

    $(".sidebar-brand a").mouseover(function () {
        if ($(this).attr("href").indexOf("javascript") > -1 == true) {
            console.log("asd");
            $(this).css("color", "#fff")
        }
    });


})
