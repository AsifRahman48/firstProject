<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header"  style="padding-bottom: 0px; margin-bottom: 0px;">
        <div class="row form-group" style=" padding-bottom: 5px; margin-bottom: 0px;">
            <div class="col-12 col-sm-3 col-md-2"> Search User </div>
            <div class="col-12 col-sm-5 col-md-7"> 
              <input type="text" name="search" class="form-control" placeholder="Search User Info" id="SearchInput" >
            </div>
            <div class="col-12 col-sm-4 col-md-3">
               <button class="btn btn-info" id="advancedSearch"> Advanced search</button>
            </div>
        </div>

      </div>
      <style type="text/css">
        table,thead,th{
          padding: 4px !important;
          margin: 0px !important;
        }
      </style>
    <div class="modal-body" style="min-height: 400px;" >
      <div id="modalBody" class=" table-responsive">
     <table id="modalTable" class="table table-striped table-bordered table-hover" >
          <thead class="bg-info text-light"  style="font-size: 12px;">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Designation</th>
              <th scope="col">Department</th>
              <th scope="col">Phone.No</th>
              <th scope="col">Company</th>
            </tr>
          </thead>
          <tbody id="InfoResult">

          </tbody>
      </table>
        <div id="advancedSearchShow"></div>
    </div>

          <br>
          <hr>
          <br>
    <div id="selectedResultModal" class="col-md-8">
      <label class="label">Selected User</label>
     
     
     
          <ul id="sortable"></ul>
          
    </div>
 </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cancelFunction()" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-info"  data-dismiss="modal" onclick="getFormData()">Save changes</button>
       
      </div>
    </div>
  </div>
</div>


 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
   <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
    jQuery(document).ready(function($) {
      if ( $(window).width() < 1500 ) {
      $(".table-responsive").addClass("text-nowrap");
    } else {
      $(".table-responsive").removeClass("text-nowrap");
    }


    $( window ).resize(function() {
      if ( $(window).width() < 1500 ) {
          $(".table-responsive").addClass("text-nowrap");
        } else {
          $(".table-responsive").removeClass("text-nowrap");
      }
  });

    })
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
  </script>
  <style type="text/css">
    #sortable{
      list-style: none;
    }
    #sortable li{
      padding: 0px;
      margin:0px;

    }
  </style>
<script type="text/javascript">
      var inputarrayReconmonder=[];
      var inputarrayApprover=[];
      var inputarray=[];
       $("#SearchInput").keyup(function(){
        $("#modalTable").show();
         $("#advancedSearchShow").hide();
        var inputValu=this.value;
        if(inputValu.length>2){

     // alert(inputValu);
$.ajax({ 
      type: "POST",
      url: "{{URL::to('/search_ad_user_modal')}}",
      data:{searchInput:inputValu},
      datatype: 'json',
      success: function (Resultdata) {  
    // console.log(Resultdata);
     $('#InfoResult').empty();
        var SearchResult='';
        var sl=0;
  $.each(JSON.parse(Resultdata), function (index, account) {
   if (account.title===null){ var titles='';  }else{ var titles=account.title; }
   if (account.department===null){ var department='';  }else{ var department=account.department; }
   if (account.telephonenumber===null){ var telephonenumber='';  }else{ var telephonenumber=account.telephonenumber; }
   if (account.company_name===null){ var company_name='';  }else{ var company_name=account.company_name; }
     sl+=1;
   var userID=account.id;
   var rowIndex=sl;

   SearchResult+='<tr><td><input type="checkbox" id="'+account.id+'" name="selectUser" value="'+account.id+'" onclick="getSelectUser('+userID+','+rowIndex+')"></td>';
   SearchResult+='<td>'+account.name+'</td>';
   SearchResult+='<td>'+account.email+'</td>';
   SearchResult+='<td>'+ titles+'</td>';
   SearchResult+='<td>'+department+'</td>';
   SearchResult+='<td>'+telephonenumber+'</td>';
   SearchResult+='<td>'+company_name+'</td>';
   SearchResult+='</tr>';
   
     });
  $('#InfoResult').append(SearchResult); 

   }
  });

  } 

 });


       	var i=1;
       	function getSelectUser(userID,rowIndex){ 

       		// console.log(inputarrayReconmonder);

        if(ImportDivId=='#Recommender'){
			var inputarray=inputarrayReconmonder;
		}else if(ImportDivId=='#Approver'){
			var inputarray=inputarrayApprover;
		}


	   	if(inputarray.indexOf(userID) !== -1){
	    

	   	}else{
	      inputarray.push(userID);

	      // console.log(inputarray);

	        var UserName =$('#modalTable').find('tr:eq('+rowIndex+')').find("td:eq(1)").html();
	        var UserEmail =$('#modalTable').find('tr:eq('+rowIndex+')').find("td:eq(2)").html();
	        var UserDesignation =$('#modalTable').find('tr:eq('+rowIndex+')').find("td:eq(3)").html();
	        var UserDepartment =$('#modalTable').find('tr:eq('+rowIndex+')').find("td:eq(4)").html();
	        var UserPhoneNo =$('#modalTable').find('tr:eq('+rowIndex+')').find("td:eq(5)").html();
	        var UserCompany =$('#modalTable').find('tr:eq('+rowIndex+')').find("td:eq(6)").html();
	        // alert(UserName);
	         // var UserId =$('#modalTable').find('tr:eq('+tableTRid+')').find("td:eq(0)").html();
	        // alert(userID);
	        var appandSelectResult='';
	        appandSelectResult+='<li id="re'+userID+'" class="ui-state-default" style="padding-left:10px; margin-bottom: 10px;">';
	          appandSelectResult+='<div class="row" style=" flot:left; padding: 10px; margin-bottom: 0px;">';
	        // appandSelectResult+='<div class="form-group"  style=" flot:left; padding: 0px; margin-bottom: 0px;">';
	      
	        // appandSelectResult+='<label for="usr">Name:</label>';
	        // appandSelectResult+='<div/>';
	        appandSelectResult+='<div  style="width: 5%; hight:20px;"><span class="fa fa-arrows-v"></span></div>';
	        appandSelectResult+='<input type="hidden" name="user_name[]" value="'+UserName+'-'+UserEmail+'-'+UserDesignation+'-'+UserDepartment+'-'+UserPhoneNo+'-'+UserCompany+'"  class="form-control"/>';
	        appandSelectResult+='<div style="width:85%; hight:20px;"><input type="hidden" name="user_id[]" value="'+userID+'" />'+UserName+'-'+UserEmail+'-'+UserDesignation+'-'+UserDepartment+'-'+UserPhoneNo+'-'+UserCompany+'<div/>';
	        
	        appandSelectResult+='</div>';
	        appandSelectResult+='<div style="width: 5%; hight:20px; color:red" class="pull-right" onclick="removeListItem('+userID+','+userID+')">&nbsp; <i class="fa fa-times"></i> </div>';
	        // appandSelectResult+='</div>';
	        appandSelectResult+='</li>';


	      $('#sortable').append(appandSelectResult);

	 		i++;
       	}

  }


		function removeListItem(id,userID){


			$("#re"+id+"").remove();

			if(ImportDivId=='#Recommender'){
				var inputarray=inputarrayReconmonder;

			}else if(ImportDivId=='#Approver'){
				var inputarray=inputarrayApprover;

			}

			var index = inputarray.indexOf(userID);
			if (index > -1) {
			   inputarray.splice(index, 1);
			}


			if( $('#InfoResult').find('input#'+id+':checked') ){
				// console.log('checked');
				$('#InfoResult').find('input#'+id).prop( "checked", false );
			}


		}

		// when Remove all recommnder from select box then also make the inputarray empty too
		$(document).on('click','.removeReconmonder', function(){
			var userID = parseInt($(this).attr("id"), 10);
			
			if(ImportDivId=='#Recommender'){
				var inputarray=inputarrayReconmonder;

			}else if(ImportDivId=='#Approver'){
				var inputarray=inputarrayApprover;

			}

			var index = inputarray.indexOf(userID);

			if (index > -1) {
			   inputarray.splice(index, 1);
			}

		});

		// when Remove all Approver from select box then also make the inputarray empty too
		$(document).on('click','.removeApprover', function(){
			var userID = parseInt($(this).attr("id"), 10);

			if(ImportDivId=='#Recommender'){
				var inputarray=inputarrayReconmonder;

			}else if(ImportDivId=='#Approver'){
				var inputarray=inputarrayApprover;

			}

			var index = inputarray.indexOf(userID);
			if (index > -1) {
			   inputarray.splice(index, 1);
			}

		});


$('#advancedSearch').click(function(){
       $("#advancedSearchShow").show();
       $("#modalTable").hide();
        $('#advancedSearchShow').empty();
        var searchForm='';
        searchForm+='<div class="col-md-8 offset-md-2">'
        searchForm+='<form action="" id="advancedSearchForm"  onsubmit="return advancedSearchForm()" method="post" accept-charset="utf-8">';
        searchForm+='<div class="row">';
        searchForm+='<div class="col-lg-12 col-md-12 col-sm-12" style="padding-bottom: 10px;">';
        searchForm+='<input class="form-control" name="AFname" placeholder="Full name" type="text"  />';
        searchForm+='</div>';
        searchForm+='</div>';
        searchForm+='<div class="row">';
        searchForm+='<div class="col-lg-12 col-md-12 col-sm-12" style="padding-bottom: 10px;">';
        searchForm+=' <input class="form-control" name="AFemail" placeholder="E-mail" type="text"  />';
        searchForm+='</div>';
        searchForm+='</div>';
        searchForm+='<div class="row">';
        searchForm+='<div class="col-lg-6 col-md-6 col-sm-6" style="padding-bottom: 10px;">';
        searchForm+='<input class="form-control" name="AFtitle" placeholder="Designation" type="text"  />';
        searchForm+='</div>';
        searchForm+='<div class="col-lg-6 col-md-6 col-sm-6" style="padding-bottom: 10px;">';
        searchForm+='<input class="form-control" name="AFdepartment" placeholder="Department" type="text" />';
        searchForm+=' </div>';
        searchForm+='</div>';
        searchForm+='<div class="row">';
        searchForm+='<div class="col-lg-6 col-md-6 col-sm-6" style="padding-bottom: 10px;">';
        searchForm+='<input class="form-control" name="AFcompany" placeholder="Company name" type="text" />';
        searchForm+='</div>';
        searchForm+='<div class="col-lg-6 col-md-6 col-sm-6" style="padding-bottom: 10px;">';
        searchForm+='<input class="form-control" name="AFphone" placeholder="Phone Number" type="text" />';
        searchForm+=' </div>';
        searchForm+='</div>';   
        searchForm+='<div class="row">';
        searchForm+='<div class="col-lg-12 col-md-12 col-sm-12">';
        searchForm+='<button type="submit" class="btn btn-success pull-right">Search</button>';
        searchForm+=' </div>';
        searchForm+='</div>';
        searchForm+='</div>';     
        searchForm+='</form>';
        searchForm+='</div>';
       $('#advancedSearchShow').append(searchForm);

});



function advancedSearchForm(){
  var name=$('input[name="AFname"]').val();
  var email=$('input[name="AFemail"]').val();
  var title=$('input[name="AFtitle"]').val();
  var department=$('input[name="AFdepartment"]').val();
  var company=$('input[name="AFcompany"]').val();
  var phone=$('input[name="AFphone"]').val();

$.ajax({ 
      type: "POST",
      url: "{{URL::to('/search_ad_user_modal_advancedSearch')}}",
      data:{name:name,email:email,title:title,department:department,company:company,phone:phone},
      datatype: 'json',
      success: function (Resultdata) { 
       
          $("#modalTable").show();
         $("#advancedSearchShow").hide();
 $('#InfoResult').empty();
        var SearchResult='';
        var sl=0;
  $.each(JSON.parse(Resultdata), function (index, account) {
   if (account.title===null){ var titles='';  }else{ var titles=account.title; }
   if (account.department===null){ var department='';  }else{ var department=account.department; }
   if (account.telephonenumber===null){ var telephonenumber='';  }else{ var telephonenumber=account.telephonenumber; }
   if (account.company_name===null){ var company_name='';  }else{ var company_name=account.company_name; }
     sl+=1;
   var userID=account.id;
   var rowIndex=sl;

   SearchResult+='<tr><td><input type="checkbox" name="selectUser" value="'+account.id+'" onclick="getSelectUser('+userID+','+rowIndex+')"></td>';
   SearchResult+='<td>'+account.name+'</td>';
   SearchResult+='<td>'+account.email+'</td>';
   SearchResult+='<td>'+ titles+'</td>';
   SearchResult+='<td>'+department+'</td>';
   SearchResult+='<td>'+telephonenumber+'</td>';
   SearchResult+='<td>'+company_name+'</td>';
   SearchResult+='</tr>';
   
     });
  $('#InfoResult').append(SearchResult); 



      }
    });

return false;
}


function getFormData(){

         if(ImportDivId=='#Recommender'){
            var divid='Recommender';
            var functionname='removeReconmonder';
            var inputFildName='recommender_id';
          }else if(ImportDivId=='#Approver'){
            var divid='Approver';
            var functionname='removeApprover';
            var inputFildName='approver_id';
        }



     var obj = [];
         $('input[name^="user_name"]').each(function() {
              obj.push($(this).val());
             });
        var h=0;
    var globalInput='';
    
        $('input[name^="user_id"]').each(function() {
globalInput+='<div class="col-md-11 col-11 d-flex" id="'+functionname+'Row'+$(this).val()+'" style="margin-top: 5px;  margin-bottom:10px; padding: 0px;">';
        // globalInput+='<div class="input-group" style="margin-bottom: 10px;">';
        globalInput+='<select name="'+inputFildName+'[]" class="form-control selUser" required="1"   >';
          globalInput+='<option value="'+$(this).val()+'">'+obj[h]+'</option>'
        globalInput+='</select>';
         globalInput+='&nbsp; &nbsp; &nbsp;<i id="'+$(this).val()+'" class=" fa fa-minus-square '+functionname+'" value="" aria-hidden="true" ></i>';
        // globalInput+='</div>';
        globalInput+='</div>';
h++;
        });
  $(ImportDivId+'a').empty();
  // $(ImportDivId+'a').append(addbutton);
     $(ImportDivId).append(globalInput);
  // return false;
}

function cancelFunction(){
           if(ImportDivId=='#Recommender'){
var inputarray=inputarrayReconmonder;

    }else if(ImportDivId=='#Approver'){
var inputarray=inputarrayApprover;

    }
// alert(inputarray.length);
if(inputarray.length>0){
for( var i = inputarray.length; i--;){
inputarray.splice(i, 1);
}
}
//   

 // var inputarrayReconmonder=[];
 //      var inputarrayApprover=[];
 //      var inputarray=[];
       
}
</script>
