function load_nextstep(){
	
	if(document.getElementById('hf_state').value==""){
		alert('Select some state please');
		return;	
		} 
	if(document.getElementById('hf_lga').value=="")
	{
		alert('Select some LGA please');
		return;	
		} 
	if(document.getElementById('hfs').value=="")
	{
		alert('Select some health facility please');
		return;	
		} 
	if(document.getElementById('filetypes').value=="")
	{
		alert('Select some file type please');	
		return;
		}
	if(document.getElementById('period').value=="")
	{
		alert('Select some period please');	
		return;
		}
	if(document.getElementById('year').value=="")
	{
				alert('Select some period please');	
		return;
	}
	else{
	// process necessary IDs
	
	var hf_id = document.getElementById('hfs').options[document.getElementById('hfs').options.selectedIndex].id;
	var file_id = document.getElementById('filetypes').options[document.getElementById('filetypes').options.selectedIndex].id;
	var ctt_type = document.getElementById('filetypes').options[document.getElementById('filetypes').options.selectedIndex].title;
	var period = document.getElementById('period').value;
	var year = document.getElementById('year').value;
	
	var periods=period.split("_");
	
	// process necessary texts
	
	var state_name=document.getElementById('hf_state').options[document.getElementById('hf_state').options.selectedIndex].text;
	var lga_name=document.getElementById('hf_lga').options[document.getElementById('hf_lga').options.selectedIndex].text;
	var hf_name=document.getElementById('hfs').options[document.getElementById('hfs').options.selectedIndex].text;
	var filetype_name=document.getElementById('filetypes').options[document.getElementById('filetypes').options.selectedIndex].text;
	
	document.getElementById('postvars').value=state_name+'_'+lga_name+'_'+hf_name+'_'+filetype_name+'_'+hf_id+'_'+file_id+'_'+period+'_'+year+'_'+ctt_type;
	
	if(confirm("YOU SELECTED TO CREATE A DATA FILE ENTRY FOR :\n\n - "+filetype_name.toUpperCase()+"\n\n - FOR "+lga_name.toUpperCase()+" LGA - "+hf_name.toUpperCase()+"\n\n - FOR THE PERIOD OF "+periods[1].toUpperCase()+" "+year.toUpperCase()+"\n\n\n DO YOU WANT TO CONTINUE?"))
	{
		document.getElementById("frm_step_one").action='<?php echo base_url();?>hffiles/datafile';
		document.getElementById("frm_step_one").submit();
	}
	}
	}
