$(document).ready(function() {  
	var pk_path = $('#datapath').data('path');
    $(".manually_method .categList").click(function() {    	
		var cID = $(".manually_method .categList").val();
		$.ajax({
		    type: "POST",
		    url: pk_path+"/ajax.php?cID="+cID,
		    success: function(result){
		      if (result == "0") {
		        console.log("no data")
		      } else {
				$("#allprdcts .contain").html(result);
		      }
		    }
		});				
    });
	$(".catlist .categList").click(function() {    	
		var catID = $(".catlist .categList").val();
		$.ajax({
		    type: "POST",
		    url: pk_path+"/ajax.php?catID="+catID,
		    success: function(result){
		      if (result == "0") {
		        console.log("no data")
		      } else {
				$(".tabcategories ul").append(result);
				var current = "";
		      	$(".tabcategories li").each(function(){
		      		current = current+($(this).data("id"))+",";
		      	});
				$(".isotope_cat").attr("value", current);
		      }
		    }
		});				
    });
	$(".catlist .tabcategories li").click(function() {    	
		var catID = $(this).data("id");
		var current = $(".isotope_cat").val().replace(catID+",", "");
		$(".isotope_cat").attr("value", current);
		$(".catlist .tabcategories").find("[data-id="+catID+"]").remove();
    });
	$("#allprdcts").on("click", ".prodSection", function () {
		var pID = $(this).data("pid");								
		var res = "";
		$.ajax({
		    type: "POST",
		    url: pk_path+"/ajax.php?pID="+pID,
		    success: function(result){
		      if (result == "0") {
		        res = result;
		        console.log("no data");
		      } else {
		      	res = result;
		      }
		    }
		});
		$(this).clone().appendTo("#selectedprdcts .contain");
    });
	$("#selectedprdcts").on("click", ".prodSection", function () {
		var pID = $(this).data("pid");								
		var res = "";
		$.ajax({
		    type: "POST",
		    url: pk_path+"/ajax.php?rem_pID="+pID,
		    success: function(result){
		      if (result == "0") {
		        res = result;
		      } else {
		      	res = result;
		      }
		    }
		});
		$(this).remove();
	});
});