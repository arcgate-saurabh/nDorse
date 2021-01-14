/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var startdatestats ="";
var enddateestats="";
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
       $("#datepicker_start").datepicker(dateparameters);
       $("#datepicker_end").datepicker(dateparameters);
});

$(document).on("click", "#showdataall", function () {
    $("#datepicker_start").val("");
    $("#datepicker_end").val("");
    $("#chartseachform").submit();
  // window.location.href = siteurl+"endorse/charts";
});
$(document).on("click", "#chartsearch", function () {
    
    var start_date = $("#datepicker_start").val();
    var end_date = $("#datepicker_end").val();
    if(start_date==""){
         alertbootbox("Select a start date");
         return;
    }else if(start_date!=""){
        var dateobj = start_date.split("-");
        var starty = dateobj[2];
        var startm = dateobj[0];
        var startd = dateobj[1];
      //  alert(start_date);
        var d = new Date(starty,startm,startd);
        starttime = d.getTime();
        startdateendorse = start_date;
        if(end_date!=""){
          var dateobj = end_date.split("-");
          var endy = dateobj[2];
          var endm = dateobj[0];
          var endd = dateobj[1];
          var d = new Date(endy,endm,endd);
          endtime = d.getTime();
          if(starttime>endtime)
          {
            $("#datepicker_start").val("")
            startdateendorse ="";
            alertbootbox("End Date must be greater then the Strat Date");
            return;
          }
          enddateendorse =end_date;
        }
        
    }
	
	$("#chartseachform").submit();
    // endorse date search
//        curl = siteurl + 'cajax/getchartsearch';
//       var formData = {start_date:start_date,end_date:end_date};
//    
//                    $.ajax({  
//                            url :curl,
//                            type: "POST",
//							
//                            data : formData,
//                            success: function(data, textStatus, jqXHR)
//                            {
//
//								
//							   if($.trim(data)==""){
//                                     $(" <div >No Data available</div>").appendTo("endorse-stats");
//                                }else{
//                               
//                                $(data).appendTo("#endorse-stats");
//                                }
//						 
//                            },
//                            error: function (jqXHR, textStatus, errorThrown)
//                            {
//                         
//                            }
//                        });
			
    
});



    
		


        


