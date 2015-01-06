
/*
 * SqlQueryBuilder v 0.06 for jQuery
 *
 * Copyright (c) 2009 Ismail ARIT / K Sistem Ltd. iarit@ksistem.com
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */




(function($) {


    $.fn.sqlsimpletreeview= function(options) {

    	$.fn.sqlsimpletreeview.defaults = {
			name:'mytree',
			onselect:null,
		};
		var opts = $.extend({}, $.fn.sqlsimpletreeview.defaults, options);


    return this.each(function() {
                 
				 this.opts=opts;

				 var tree = $(this);
				 tree.find('ul.treeview li.list').addClass('expanded').find('>ul').toggle();
				 //node.find('>ul').toggle();
				 tree.click(function(e) {
					//is the click on me or a child
					var node = $(e.target);
					//check the link is a directory
					if (node.is("li.list")) { //Is it a directory listitem that fired the click?
 						//do collapse/expand
						if (node.hasClass('collapsed')) {
							node.find('>ul').toggle(); //need the > selector else all child nodes open
							node.removeClass('collapsed').addClass('expanded');
						}
						else if (node.hasClass('expanded')) {
							node.find('>ul').toggle();
							node.removeClass('expanded').addClass('collapsed');
						}
						//its one of our directory nodes so stop propigation
						e.stopPropagation();
					} else if (node.attr('href') == '#' | node.hasClass('item')) {
						//its a file node with a href of # so execute the call back
						// if the item that fired the click is not either a folder or a file it cascades as normal
						// so that contained links behave like normal
						opts.onselect(node);
					}
 
            });

	  
	});

 
    };




$.fn.extend({
        getSQBClause: function (ctype) {
            var tt = this[0];
			//alert($(tt).html());
            switch(ctype){
			 case 'where':
			   return $('.sqlwheredata',$(tt)).text();
			 case 'sort':
			   return $('.sqlsortdata',$(tt)).text();  
			 case 'group':
			   return $('.sqlgroupbydata',$(tt)).text();
			 case 'column':
			   return $('.sqlcolumndata',$(tt)).text();
			 case 'all':
			   return $('.sqlalldata',$(tt)).text();			
			}  
        },
        getSQBParam: function (prm) {
            var $tt = this[0];
            if (!prm) 
                return $tt.opts;
             else 
                return ($tt.opts[prm] ? $tt.opts[prm] : null);
            
        },
        setSQBParam: function (newprms) {
            return this.each(function () {
                if (typeof(newprms) === "object") {
                    $.extend(true, this.opts, newprms);
                }
            })
        },
        loadSQB: function (jsonstr) {
            var $tt = this[0];

			$('.sqlcolumn').remove();
			$('.sqlwhere').remove();
			$('.sqlgroup').remove();
			$('.sqlsort').remove();
			
            var j=eval('(' + jsonstr + ')');    

			var coldiv  =$(".addnewsqlcolumn");
			var sortdiv =$('.addnewsqlsort');
			var groupdiv=$('.addnewsqlgroup');
			var wherediv=$('.addnewsqlwhere');
						
            /*rebuild col data*/
			for(var i=0;i<j.columndata.length;i++){
			    //j.columndata[i].columnslot, j.columndata[i].columnvalue
			    coldiv[0].opts.onselect(j.columndata[i].columnslot,coldiv,{columnas:j.columndata[i].columnas}); 
			}
            /*rebuild sort data*/
			for(var i=0;i<j.sortdata.length;i++){
			    //j.sortdata[i].columnslot, j.sortdata[i].columnas
			    sortdiv[0].opts.onselect(j.sortdata[i].columnslot,sortdiv,{columnas:j.sortdata[i].columnas}); 			
			}
            /*rebuild group by data*/
			for(var i=0;i<j.groupdata.length;i++){
			    //j.groupdata[i].columnslot, 
			    groupdiv[0].opts.onselect(j.groupdata[i].columnslot,groupdiv,null); 						
			}
            /*rebuild where data*/
			for(var i=0;i<j.wheredata.length;i++){
			    //j.wheredata[i].columnslot, j.wheredata[i].opslot,j.wheredata[i].chainslot,j.wheredata[i].columnvalue
			    wherediv[0].opts.onselect(j.wheredata[i].columnslot,wherediv,{columnslot:j.wheredata[i].columnslot,opslot:j.wheredata[i].opslot,chainslot:j.wheredata[i].chainslot,columnvalue:j.wheredata[i].columnvalue}); 			
			
			}
			
			
			
			
			
			



        }
      


});




   
  var mouseX = 0,mouseY = 0;
  $().mousemove( function(e) {mouseX = e.pageX;mouseY = e.pageY;});



  $.fn.sqlsimplemenu = function(options) {
  	$.fn.sqlsimplemenu.defaults = {
  	    menu:'kmenu',
		mtype: 'menu',
		menuid:0,     
		checkeditems:'',
		checkalltext:'Select all',
		onselect:null,
        onselectclose:null,
		onselectablelist:null,
		oncheck:null,
		fields:[]
	};
	var opts = $.extend({}, $.fn.sqlsimplemenu.defaults, options);

	function buildsimplemenu(){
	    /*console.log("buildsimplemenu: %o", this);*/
        var mmenu=''; 
		if(opts.fields.length > 0) {
			for (var i=0;i<opts.fields.length;i++){
	            if(opts.fields[i].ftype=='{')
	              mmenu=mmenu+'<li><a href="#'+i+'">'+opts.fields[i].name+'</a><ul>';
	            else if(opts.fields[i].ftype=='}')
	              mmenu=mmenu+'</ul></li>';
				else mmenu=mmenu+'<li><a href="#'+i+'">'+opts.fields[i].name+'</a></li>';
			}
		}
        return '<div id="'+opts.menu+'" class="sqlsimplemenu">'+	
                  '<ul class="clickmenu">'+   
                    mmenu+
                  '</ul>'+
                '</div>';
    
	}

	function buildselectboxmenu(){
	
        var mmenu=''; 
        
        if(opts.onselectablelist){
              fieldvals=opts.onselectablelist(opts.menuid);
              var farray=fieldvals.split(',');
              var ff=new Array();
                                        
              for(h=0;h<farray.length;h++)
                       ff[h]={'name':farray[h]};
                       
              opts.fields=ff;                                 
        }  
        
        
		if(opts.fields.length > 0) {
			mmenu=mmenu+'<li><input type="checkbox" href="#0" id="'+opts.checkalltext+'">'+opts.checkalltext+'</li>';
			for (var i=0;i<opts.fields.length;i++){
				mmenu=mmenu+'<li><input type="checkbox" '+(opts.checkeditems.indexOf(opts.fields[i].name)!=-1?' checked ':'')  +'href="#'+(i+1)+'" id="'+opts.fields[i].name+'">'+opts.fields[i].name+'</li>';
			}
		}
        return '<div id="'+opts.menu+'" class="sqlsimplemenu">'+	
                  '<ul class="clickmenu">'+   
                    mmenu+
                  '</ul>'+
                '</div>';
    
	}


    return this.each(function() {
                 
				 this.opts=opts;
				 /*console.log("sqlsimplemenu:this.each: %o",this);*/
                 var sm= opts.mtype=='selectbox'? buildselectboxmenu():buildsimplemenu();                 
                 $(document.body).after(sm);//add to body
                 $('div#'+opts.menu).hide();//hide
	             



                 $(this).click(function(e) {

                     var srcelement=$(this);
    				 /*console.log("sqlsimplemenu:this.each:click: %o",this);*/

                     e.stopPropagation();
  				     $('div.sqlsimplemenu').hide();//hide all if any	
  				     if(!e.pageX){ e.pageX=mouseX;e.pageY=mouseY;}								
                     $('div#'+opts.menu).css({ top: e.pageY+5, left: e.pageX+5, position: 'absolute' }).slideToggle(200);                              
                     $(document).unbind('click').click( function(e) {
					                /*console.log("sqlsimplemenu:this.each:click:unbind:"+'div#'+opts.menu+": %o",this);*/
									$('div#'+opts.menu).slideUp(200,function(){
							               if(opts.onselectclose) opts.onselectclose($(this));
							        });
					                $(document).unbind('click'); 		        
					                e.stopPropagation();		        
									return false;									
					 });
			
			         $('div#'+opts.menu).find('input[type=checkbox]').unbind('click')
                                                                     .click( function(e) {
								e.stopPropagation();
								if($(this).attr('href').substr(1)==0)
								  $('div#'+opts.menu).find('input[type=checkbox]').attr('checked', $(this).attr('checked')? true:false);								
								else
								  $(this).attr('checked', $(this).attr('checked')? true:false);

                                 var items=new Array();
                                 var k=0;
                                 $('div#'+opts.menu).find('input[type=checkbox]').each(function(){
                                    //if not select all(first item in the list...)
                                    if($(this).attr('href').substr(1)!='0'){
                                     items[k] =($(this).attr('checked')? $(this).attr('id'):'');                                    
                                     if($(this).attr('checked'))k++;
                                    }
                                 });

                                if(k==0) items[0]='[]';//if empt put etleast [] in it..
                                var items_str=items.join(',');
                                //alert(items_str.substr(-1,1));
                                //alert(items_str.substr(0,items_str.length-1));
                                
                                if(items_str.substr(-1,1)==',')
                                     items_str=items_str.substr(0,items_str.length-1);
                                
                                 																							
                                 																							
								if( opts.onselect ) opts.oncheck( $(this).attr('href').substr(1), $(srcelement),$(this),items_str );
								//return false;
					 });			
								

                     $('div#'+opts.menu).find('a').unbind('click')
                                                  .click( function(e) {
                                                  var selitem=$(this);
												  //alert($(this).text());
					                /*console.log("sqlsimplemenu:this.each:click:find(a):unbind:"+'div#'+opts.menu+": %o",this);*/
												  
								$('div#'+opts.menu).slideUp(200,function(){								
					                /*console.log("sqlsimplemenu:this.each:click:find(a):unbind:"+'div#'+opts.menu+":slideup(200): %o",this);
								            if( opts.onselect ) opts.onselect( $(selitem).attr('href').substr(1), $(srcelement),null );*/
								});
								
								if( opts.onselect ) opts.onselect( $(selitem).attr('href').substr(1), $(srcelement),null );

								return false;
					 });			
								
         return false;
      });
	  
	});
		
  };




  $.fn.sqlquerybuilder = function(options) {
  	$.fn.sqlquerybuilder.defaults = {
        reportid:0,
  	    counters:[0,0,0,0],//we have four counters..
  	    sqldiv:null,//where sql clause will be put..
  	    presetlistdiv:null,//where saved sqls are listed...
        reporthandler:null,//this is the .php to query to get the ul treeview to show previuosly saved sqls...
  	    datadiv:null,//where we put data, so that data can be saved..
  	    statusmsgdiv:null,//where we put error strs..
		whereinput:null,
		sortinput:null,
		groupinput:null,
		columninput:null,
        allinput:null,
        reportnameprompt:'Report name',
		reportnameinput:'type your report name here',
		columntitle:'<b>Result columns ..</b>',
		addnewcolumn:'[Click to a add new column]..',
		showcolumn:true,
		wheretitle:'<b>Select records where all of the following apply ..</b>',
		addnewwhere:'[Click to a add new condition]..',
		showwhere:true,
		sorttitle:'<b>Sort columns by..</b>',
		addnewsort:'[Click to a add new sort column]..',
		showsort:true,
		grouptitle:'<b>Group columns by..</b>',
		addnewgroup:'[Click to a add new group column]..',
		showgroup:true,
		deletetext:'X',
		animate:true,
		onchange:null,
		onselectablelist:null,
		fields:[],
		joinrules:[],
		extrawhere:'',
		operators:[
		 {name:'equal',op:"%f='%s'",multipleval:false},
		 {name:'not equal',op:"%f!='%s'",multipleval:false},
		 {name:'starts with',op:"%f like '%s%'",multipleval:false},
		 {name:'not starts with',op:"not(%f like '%s%')",multipleval:false},
		 {name:'contains',op:"%f like '%%s%'",multipleval:false},		 
		 {name:'not contains',op:"not(%f like '%%s%')",multipleval:false},		 
		 {name:'bigger than',op:"%f>'%s'",multipleval:false},
		 {name:'bigger or equal',op:"%f>='%s'",multipleval:false},
		 {name:'smaller than',op:"%f<'%s'",multipleval:false},
		 {name:'smaller or equal',op:"%f<='%s'",multipleval:false},
		 {name:'in between',op:"%f between '%s1' and '%s2'",multipleval:true,info:''},
		 {name:'not in between',op:"not(%f between '%s1' and '%s2')",multipleval:true,info:''},		 
		 {name:'is in',op:"%f in (%s)",multipleval:false,selectablelist:true,info:''},
		 {name:'is not in',op:"not(%f in (%s))",multipleval:false,selectablelist:true,info:''},		 
		 {name:'is null',op:" %f is null",multipleval:false}		 		 		 		 		
		],
		chain:[
		 {name:' and ',op:' and '},
		 {name:' or ',op:' or '},
		 {name:' and ( ',op:' and ( '}, 
		 {name:' or ( ',op:' or ( '},
		 {name:' ) and ',op:' ) and '},
		 {name:' ) or ',op:' ) or '},
		 {name:' ) . ',op:' ) '},
		 {name:' . ',op:''}

		],
		astagpre: '"',
		astagsuf:'"'
	};
	var opts = $.extend({}, $.fn.sqlquerybuilder.defaults, options);
    var sqlwidget=$(this);

	var howmany = opts.amount;


	function addnewsqlwhere(){

		var sql_text = opts.wheretitle+"<br>";
        //add predefined rules here too...
        //...

		return sql_text;
	}

	function addnewsqlcolumn(){

		var sql_text = opts.columntitle+"<br>";
        //add predefined columns here too...
        //...

		return sql_text;
	}


	function addnewsqlgroup(){

		var sql_text = opts.grouptitle+"<br>";
        //add predefined group here too...
        //...

		return sql_text;
	}



	function addnewsqlsort(){

		var sql_text = opts.sorttitle+"<br>";
        //add predefined sort here too...
        //...

		return sql_text;
	}


    function onchangeevent(type){
    
        if(opts.datadiv){

            
            var data='{'+
                         'reportid:'+opts.reportid+',';
                         
                          data=data+'columndata:[';
                            $('span.sqlcolumn').each(function(){
                              var col_slot=$(this).find('a.addnewsqlcolumn').attr('href').substr(1);
                              var col_as=$(this).find('span.addnewsqlcolumnvalue').html();
                              var columndata= '{'+
                                  'columnslot:'+col_slot+','+
                                  'columnas:"'+col_as+'"'+
                                '},';
                              data=data+columndata;             
                            });        
                          data=data+'],';//close columns data   
        

                          data=data+'sortdata:[';
                            $('span.sqlsort').each(function(){
                              var col_slot=$(this).find('a.addnewsqlsort').attr('href').substr(1);
                              var col_as=$(this).find('span.addnewsqlsortvalue').html();
                              var columndata= '{'+
                                  'columnslot:'+col_slot+','+
                                  'columnas:"'+col_as+'"'+
                                '},';
                              data=data+columndata;             
                            });        
                          data=data+'],';//close sort data   
        
                          data=data+'groupdata:[';
                            $('span.sqlgroup').each(function(){
                              var col_slot=$(this).find('a.addnewsqlgroup').attr('href').substr(1);
                              var columndata= '{'+
                                  'columnslot:'+col_slot+','+
                                '},';
                              data=data+columndata;             
                            });        
                          data=data+'],';//close group data   

 
                         data=data+'wheredata:[';
                            $('span.sqlwhere').each(function(){
                              var col_slot=$(this).find('a.addnewsqlwhere').attr('href').substr(1);
                              var op_slot=$(this).find('a.addnewsqlwhereoperator').attr('href').substr(1);
                              var chain_slot=$(this).find('a.addnewsqlwherechain').attr('href').substr(1);
                              var col_value=$(this).find('span.addnewsqlwherevalue').html();

                              
                              var columndata= '{'+
                                  'columnslot:'+col_slot+','+
                                  'opslot:'+op_slot+','+
                                  'chainslot:'+chain_slot+','+
                                  'columnvalue:"'+col_value+'",'+                                  
                                '},';
                              data=data+columndata;             
                            });        
                          data=data+'],';//close where data   
        
        
                data=data+'}'//close full json;   
        
            $('.sqldata',$(sqlwidget)).html(data);
 
        }    
    
        //create sql clause
        //if(opts.sqldiv)
		{
        
        
            //get columns....
            var columns= new Array();
            var ccount=0;
            var tablehash=new Array();
            $('span.sqlcolumn').each(function(){
                   var col_slot=$(this).find('a.addnewsqlcolumn').attr('href').substr(1);
                   var col_as=$(this).find('span.addnewsqlcolumnvalue').html();
                   var fieldstr=opts.fields[col_slot].field;
                   if(col_as.indexOf(':')!=-1){
                      var colfuncarray=col_as.split(':');
                      var colfunc=colfuncarray[1];//syntax is fieldname:func like invtrans.quantity:sum(%f) 
                      fieldstr=colfunc.replace('%f',fieldstr);
                      col_as=colfuncarray[0];
                   }
                   
                   columns[ccount++] = fieldstr+' as '+opts.astagpre+col_as+opts.astagsuf;
                   var xx=opts.fields[col_slot].field.split('.');//table.field
                   tablehash[xx[0]]=xx[0];
            });        
            var colstr=columns.join(',');
            if(ccount==0)colstr=' * ';
            $('.sqlcolumndata',$(sqlwidget)).html(colstr);
            if(opts.columninput)$(opts.columninput).val(colstr);
 

            //get sorts......... 
            var sorts= new Array();
            var scount=0;
            $('span.sqlsort').each(function(){
                   var col_slot=$(this).find('a.addnewsqlsort').attr('href').substr(1);
                   var col_as=$(this).find('span.addnewsqlsortvalue').html();
                   sorts[scount++] = opts.fields[col_slot].field+'  '+(col_as=='Descending'? 'desc':'');
                   var xx=opts.fields[col_slot].field.split('.');//table.field
                   tablehash[xx[0]]=xx[0];
            });        
            var sortstr=sorts.join(',');
            $('.sqlsortdata',$(sqlwidget)).html(sortstr);
            if(opts.sortinput)$(opts.sortinput).val(sortstr); 


            //get group bys....
            var groups= new Array();
            var gcount=0;
            $('span.sqlgroup').each(function(){
                   var col_slot=$(this).find('a.addnewsqlgroup').attr('href').substr(1);
                   groups[gcount++] = opts.fields[col_slot].field;
                   var xx=opts.fields[col_slot].field.split('.');//table.field
                   tablehash[xx[0]]=xx[0];
            });        
            var groupstr=groups.join(',');
            $('.sqlgroupbydata',$(sqlwidget)).html(groupstr);
            if(opts.groupinput)$(opts.groupinput).val(groupstr);




            //get where str...
            var wheres= new Array();
            var wcount=0;
            var prevchain=' ',prevchainstr=' ';
            $('span.sqlwhere').each(function(){
                   var col_slot=$(this).find('a.addnewsqlwhere').attr('href').substr(1);
                   var op_slot=$(this).find('a.addnewsqlwhereoperator').attr('href').substr(1);
                   var chain_slot=$(this).find('a.addnewsqlwherechain').attr('href').substr(1);
                   var col_value=$(this).find('span.addnewsqlwherevalue').html();

                   var xx=opts.fields[col_slot].field.split('.');//table.field
                   tablehash[xx[0]]=xx[0];
                   
                   var wstr=prevchain+opts.operators[op_slot].op;
                        wstr=wstr.replace('%f',opts.fields[col_slot].field);
                   if(opts.operators[op_slot].multipleval){
                         var xx=col_value.split('and');
                         wstr=wstr.replace('%s1',xx[0]);
                         wstr=wstr.replace('%s2',xx[1]);                         
                   }else{
                         if(opts.operators[op_slot].selectablelist){
                              var xx=col_value.split(',');
                              for (k in xx) {
                                 xx[k]="'"+xx[k]+"'";
                              }
                              col_value=xx.join(',');
                         }
                         wstr=wstr.replace('%s',col_value);
                   }
                   
                   
                   prevchain=opts.chain[chain_slot].op;
                   prevchainstr=opts.chain[chain_slot].name;
                   
                   wheres[wcount++]=wstr;                   

            });
            
            var wherestr=wheres.join(' ');
            $('.sqlwheredata',$(sqlwidget)).html(wherestr);
            if(opts.whereinput)$(opts.whereinput).val(wherestr);
			

            if(prevchainstr.indexOf('.')!=-1)
                 wherestr +=prevchain;
                      
            if(wcount)wherestr = ' where '+wherestr+' '+opts.extrawhere;
            else if(opts.extrawhere) wherestr = ' where '+opts.extrawhere;



            //table names
            var tcount=0; var tables=new Array();
            for (tablename  in tablehash) {
	              tables[tcount++]=tablename;
            }
            var tablestr=tables.join(',');
            if(tcount>1){
               tablestr= tables[0]+' ';
               for(j=0;j<tcount;j++){
                  for(k=0;k<opts.joinrules.length;k++){
                    if(tables[0]==opts.joinrules[k].table1 &&
                       tables[j]==opts.joinrules[k].table2)
                            tablestr += opts.joinrules[k].rulestr+' ';               
                  }
               }            
            } 



        
            if(opts.sqldiv)$(opts.sqldiv).html('select '+colstr+' from '+tablestr+wherestr+ (gcount? (' group by '+groupstr):'') +(scount? (' order by '+sortstr):''));
            $('.sqlalldata',$(sqlwidget)).html('select '+colstr+' from '+tablestr+wherestr+ (gcount? (' group by '+groupstr):'') +(scount? (' order by '+sortstr):''));
            if(opts.allinput)$(opts.allinput).val('select '+colstr+' from '+tablestr+wherestr+ (gcount? (' group by '+groupstr):'') +(scount? (' order by '+sortstr):''));              
			 
        }    
     
        //if(opts.onchange)opts.onchange(type);   
        
    }




return this.each(function() {
	  
	  this.opts=opts;
		 
 	  var columnmarkup = addnewsqlcolumn();
	  var wheremarkup = addnewsqlwhere();
 	  var sortmarkup = addnewsqlsort();
 	  var groupmarkup = addnewsqlgroup();
      var sqlbuildelement=$(this); 

      /*load before-saved sqls*/ 
	  if(opts.presetlistdiv && opts.reporthandler){
	  

        $.ajax({    
            type:    'POST',    
            url:     opts.reporthandler,   
            data:    '',    
            error:   function(){ if(opts.statusmsgdiv)$(opts.statusmsgdiv).html("Can't load preset"); },    
            success: function(data) { sqlbuildelement.loadSQB(data);}    
         });             


	  
	  
	    
	  
	  
	  
	  
	  
	  }



 
 
      $(this).html(                   
                    '<p class=sqldata></p>'+
                    '<p class=sqlwheredata></p>'+
                    '<p class=sqlsortdata></p>'+
                    '<p class=sqlcolumndata></p>'+
                    '<p class=sqlgroupbydata></p>'+
                    '<p class=sqlalldata></p>' +					
                    '<p class=sqlbuildercolumn>'+columnmarkup+'<a class="addnewsqlcolumn" id=9999 href="#">'+opts.addnewcolumn+'</a>'+'</p>'+
                    '<p class=sqlbuilderwhere>'+wheremarkup+'<a class="addnewsqlwhere" id=9999 href="#">'+opts.addnewwhere+'</a>'+'</p>'+
                    '<p class=sqlbuildergroup>'+groupmarkup+'<a class="addnewsqlgroup" id=9999 href="#">'+opts.addnewgroup+'</a>'+'</p>'+
                    '<p class=sqlbuildersort>'+sortmarkup+'<a class="addnewsqlsort" id=9999 href="#">'+opts.addnewsort+'</a>'+'</p>'
                   );

 
                $(".sqldata").hide();
                $(".sqlalldata").hide();
                $(".sqlcolumndata").hide();
                $(".sqlwheredata").hide();
                $(".sqlsortdata").hide();
                $(".sqlgroupbydata").hide();
				
                if(!opts.showcolumn)
                 $(".sqlbuildercolumn").hide();
                if(!opts.showsort)
                 $(".sqlbuildersort").hide();
                if(!opts.showgroup)
                 $(".sqlbuildergroup").hide();
                if(!opts.showwhere)
                 $(".sqlbuilderwhere").hide();
                 
                
                 
    

               /*************************************************************************************************************/ 

                //column or sort click handling is here..... 
				$(".addnewsqlcolumn,.addnewsqlsort,.addnewsqlgroup").sqlsimplemenu({
					menu: 'sqlmenulist',	
				    fields:opts.fields,
				    onselect:function(action, el,defval) {
                    /*console.log(".addnewsqlcolumn,.addnewsqlsort,.addnewsqlgroup: %o",this);*/

					var menutype= '';//$(el).hasClass('addnewsqlcolumn')?'column':'sort';
					//var iscolumn= $(el).hasClass('addnewsqlcolumn')?true:false;			
					var countertype=0;
					if($(el).hasClass('addnewsqlcolumn')){menutype='column';countertype=0;}
					else if($(el).hasClass('addnewsqlsort')){menutype='sort';countertype=1;}
					else if($(el).hasClass('addnewsqlgroup')){menutype='group';countertype=2;} //where counter id is 3
								
						    
				    var sqlline=
				                 '<span class="sql'+menutype+'" id='+(opts.counters[countertype])+'>'+
				                 '<a class="addnewsql'+menutype+'delete" id='+(opts.counters[countertype])+' href="#'+action+'">'+opts.deletetext+'</a>&nbsp;'+
				                 '<a class="addnewsql'+menutype+'" id='+(opts.counters[countertype])+' href="#'+action+'">'+opts.fields[action].name+'</a>'+ (countertype==2?'':'&nbsp;as &nbsp;')+
   			                     '<span class="addnewsql'+menutype+'value" id='+(opts.counters[countertype])+' href="#0">'+((countertype==0 || countertype==2)? (countertype==0? (defval? defval.columnas:opts.fields[action].name):''):( defval? defval.columnas:'Ascending'))+'</span>&nbsp;'+				                 
				                 '</span>';
				                
				
				    var item=$(sqlline).hide();				
				    $('[class=addnewsql'+menutype+'][id=9999]').before(item);
                    if(opts.animate) $(item).animate({opacity: "show",height: "show"}, 150, "swing", function() {$(item).animate({height: "+=3px"}, 75, "swing", function() {$(item).animate({height: "-=3px"}, 50, "swing");onchangeevent('new');});}); 
				    else             {$(item).show();onchangeevent('new');}
				    
				    
				    
				   				    
                    //on click edit value
				    if(countertype==1){
				                                      
                                     
                       $("span[class=addnewsql"+menutype+"value][id='"+(opts.counters[countertype])+"']").sqlsimplemenu({
                                            menu:'sortmenu',
		                                    fields:[ 
		                                            {name:'Ascending'}, 
		                                            {name:'Descending'} 
		                                           ],
		                                    onselect:function(action,el){
		                                            //alert(action+'---- val:'+$(el).text());
		                                            $(el).text(action==0?'Ascending':'Descending');
		                                            onchangeevent('change');   
		                                    },
                                    });    
                                 

				    
				    
				    }else{
				      $("span[class=addnewsql"+menutype+"value][id='"+(opts.counters[countertype])+"']").click(                   
				               function (e) { 

				               
                                
				                 e.stopPropagation();
				               
                                    var element=$(this);
				               
                                 
                                    var fieldid=$('a[class=addnewsql'+menutype+'][id='+element.attr('id')+']').attr('href').substr(1);
                                    var slotid=element.attr('id');


                                    if (element.hasClass("editing") || element.hasClass("disabled")) {
                                         return;
                                    }
 
                                    element.addClass("editing");
 

                                    //in place edit...
                                    var oldhtml=$(this).html();

                                    $(this).html('<input type="text" class="editfield" id=99><span class="sqlsyntaxhelp"></span>'); 
                                    $('.editfield').val(oldhtml.replace(/^\s+|\s+$/g,""));
                                                                     
                                
                                                                 

                                    $('.editfield').blur(function() { 
                                      element.html($(this).val().replace(/^\s+|\s+$/g,""));
                                      element.removeClass("editing");
                                      element.attr("disabled","disabled");
                                      $('.sqlsyntaxhelp').remove();
                                      onchangeevent('change');                                     
                                    });

                                    $('.editfield', element).keyup(function(event) {
                                      if(event.which == 13) { // enter
                                        element.html($(this).val());
                                        element.removeClass("editing");
                                        element.removeAttr("disabled");
                                        $('.sqlsyntaxhelp').remove();    
                                        onchangeevent('change');                                                                            
                                      }
                                      return true;
                                    });
                                    element.attr("disabled","disabled");
                                 
                                    $('input[class=editfield][id=99]').focus().select();
                                 
                                 
                                                                                                                                
                                                               
                               });
                    }           
                               
                               





               //on click delete remove p for the condition...
				$("[class=addnewsql"+menutype+"delete][id='"+(opts.counters[countertype])+"']").click(
				               function () { 
				                     var item=$('span[class=sql'+menutype+'][id='+$(this).attr('id')+']');
                                     if(opts.animate) $(item).animate({opacity: "hide",height: "hide"}, 150, "swing", function() {$(this).hide().remove();onchangeevent('change');}); 
				                     else             {$(item).hide().remove();onchangeevent('change');}
                               });



                //add a menu to newly added operator
				$("[class=addnewsql"+menutype+"][id='"+(opts.counters[countertype])+"']").sqlsimplemenu({
					menu: 'sqlmenulist',
					fields:opts.fields,
				    onselect:function(action, el) {
                    
				    $("[class=addnewsql"+menutype+"][id="+$(el).attr('id')+"]")
				            .html(opts.fields[action].name)
				            .attr('href',"#"+action);
				    onchangeevent('change');
						
				}});
      

                 opts.counters[countertype]++;
                 //if(iscolumn) opts.columncount++; else opts.sortcount++;
                  
	
				}
				
				});//end of column handling....







 
                /*************************************************************************************************************/ 
                //where click handling is here..... 
				$("[class=addnewsqlwhere][id=9999]").sqlsimplemenu({
					menu: 'sqlmenulist',	
				    fields:opts.fields,
				    onselect:function(action, el,defval) {
								
					//alert($(el).text());	    
				    var sqlline=
				                 '<span class="sqlwhere" id='+opts.counters[3]+'>'+
				                 '<a class="addnewsqlwheredelete" id='+opts.counters[3]+' href="#'+action+'">'+opts.deletetext+'</a>&nbsp;'+
				                 '<a class="addnewsqlwhere" id='+opts.counters[3]+' href="#'+action+'">'+opts.fields[action].name+'</a>&nbsp;'+
				                 '<a class="addnewsqlwhereoperator" id='+opts.counters[3]+' href="#'+(defval? defval.opslot:'0')+'">'+opts.operators[(defval? defval.opslot:0)].name+'</a>&nbsp;'+
				                 '<span class="addnewsqlwherevalue" id='+opts.counters[3]+' href="#0">'+(defval? defval.columnvalue:opts.fields[action].defaultval)+'</span>&nbsp;'+				                 
				                 '<a class="addnewsqlwherechain" id='+opts.counters[3]+' href="#'+(defval? defval.chainslot:'0')+'">'+opts.chain[(defval?defval.chainslot:0)].name+'</a>&nbsp;'+
				                 '</span>';
				                
				
				    var item=$(sqlline).hide();				
				    $('[class=addnewsqlwhere][id=9999]').before(item);
                    if(opts.animate) $(item).animate({opacity: "show",height: "show"}, 150, "swing", function() {$(item).animate({height: "+=3px"}, 75, "swing", function() {$(item).animate({height: "-=3px"}, 50, "swing",function(){onchangeevent('new');});});}); 
				    else             {$(item).show();onchangeevent('new');}

                    
                     				    
				    
				    
                //on click edit value
				$("span[class=addnewsqlwherevalue][id='"+opts.counters[3]+"']").click(                   
				               function (e) { 
				               
                                 //alert('onvalueclick');
				                 e.stopPropagation();
				               
                                 var element=$(this);
                                 
                                 var operatorid=$('a[class=addnewsqlwhereoperator][id='+element.attr('id')+']').attr('href').substr(1);
                                 var fieldid=$('a[class=addnewsqlwhere][id='+element.attr('id')+']').attr('href').substr(1);
                                 var chainid=$('a[class=addnewsqlwherechain][id='+element.attr('id')+']').attr('href').substr(1);
                                 var slotid=element.attr('id');


                                 if (element.hasClass("editing") || element.hasClass("disabled")) {
                                  return;
                                 }
 
                                 element.addClass("editing");
 

                                 //in place edit...
                                 var oldhtml=$(this).html();



                                 if(opts.operators[operatorid].multipleval){
                                   $(this).html('<input type="text" class="editfield" id=1> and <input type="text" class="editfield" id=2> <span class="sqlsyntaxhelp"></span>'); 
                                   var vals=oldhtml.split('and');
                                   $('input[class=editfield][id=1]').val(vals[0].replace(/^\s+|\s+$/g,""));
                                   $('input[class=editfield][id=2]').val(vals[1].replace(/^\s+|\s+$/g,""));                                  
                                 }else{                                  
                                   $(this).html('<input type="text" class="editfield" id=99><span class="sqlsyntaxhelp"></span>'); 
                                   $('.editfield').val(oldhtml.replace(/^\s+|\s+$/g,""));
                                    


                                   if(opts.operators[operatorid].selectablelist){

                                        var editfield=$(this);
                                        var fieldvals='';
                                        if(opts.onselectablelist) fieldvals=opts.onselectablelist(slotid);
                                        var farray=fieldvals.split(',');
                                        var ff=new Array();
                                        
                                        for(h=0;h<farray.length;h++)
                                               ff[h]={'name':farray[h]};
                                               
                                         //alert('open menu....');       
                                         //$('#selectmenu'+slotid).remove(); 
                                   $('input[id=99]').sqlsimplemenu({
                                            menu:'selectmenu'+slotid,
                                            menuid:slotid,
                                            mtype:'selectbox',
                                            onselectablelist:function(slotid){
                                               return opts.onselectablelist(slotid,fieldid,operatorid,chainid);                                            
                                            },
                                            onselectclose:function(el){
                                                element.removeClass("editing");
                                                element.attr("disabled","disabled");
                                                $('.sqlsyntaxhelp').remove(); 
                                                 
                                                //alert('onclosee');
                                                //$(element).click(onvalueclick);
                                                $('#selectmenu'+slotid).remove();
                                                onchangeevent('changed');

    
                                            },
                                            checkeditems:oldhtml,
		                                    fields:ff,
		                                    onselect:function(action,el){
		                                            
		                                            $(editfield).htlm();
		   
		                                    },
   		                                    oncheck:function(action,el,checkbox,checkitems){
		                                            //alert(action+'---- val:'+$(el).val()+'--checked:'+$(checkbox).attr('checked')+'text:'+$(checkbox).attr('id')+'items:'+checkitems);
		                                            $(editfield).html(checkitems);
		                                            onchangeevent('change');

		                                            
		                                    }

                                   });    
                                   $(this).trigger('click');
                                 
                                   
                                   
                                   }
                                   
                                   
                                 } 


                                 
                                
                                 $('.sqlsyntaxhelp').html(opts.operators[operatorid].info);                                 

                                 $('.editfield').blur(function() { 
                                   
                                     switch($(this).attr('id')){
                                       case '99': element.html($(this).val().replace(/^\s+|\s+$/g,""));break;
                                       case '1':return;break;
                                       case '2':element.html($('input[class=editfield][id=1]').val().replace(/^\s+|\s+$/g,"")+' and '+$('input[class=editfield][id=2]').val().replace(/^\s+|\s+$/g,""));break;
                                     } 
                                      element.removeClass("editing");
                                      element.attr("disabled","disabled");
                                      $('.sqlsyntaxhelp').remove();
                                      onchangeevent('change');

                                     
                                     
                                 });

                                 $('.editfield', element).keyup(function(event) {
                                     if(event.which == 13) { // enter
                                        switch($(this).attr('id')){                                                                                    
                                           case '99':element.html($(this).val());break;
                                           case '1':$('input[class=editfield][id=2]').focus().select();return;
                                           case '2':element.html($('input[class=editfield][id=1]').val()+' and '+$('input[class=editfield][id=2]').val());break;
                                        }
                                        element.removeClass("editing");
                                        element.removeAttr("disabled");
                                        $('.sqlsyntaxhelp').remove();
                                        onchangeevent('change');   
                                        
                                     }
                                     return true;
                                 });
                                 element.attr("disabled","disabled");
                                 
                                 $('input[class=editfield][id='+(opts.operators[operatorid].multipleval? '1':'99')+']').focus().select();
                                 
                                                               
                               });
                               
                               





               //on click delete remove p for the condition...
				$("[class=addnewsqlwheredelete][id='"+opts.counters[3]+"']").click(
				               function () { 
				                     var item=$('span[class=sqlwhere][id='+$(this).attr('id')+']');
                                     if(opts.animate) $(item).animate({opacity: "hide",height: "hide"}, 150, "swing", function() {$(this).hide().remove();onchangeevent('change');}); 
				                     else             {$(item).hide().remove();onchangeevent('change');}
				                     
                               });



                //add a menu to newly added operator
				$("[class=addnewsqlwhere][id='"+opts.counters[3]+"']").sqlsimplemenu({
					menu: 'sqlmenulist',
					fields:opts.fields,
				    onselect:function(action, el) {
                    
				    $("[class=addnewsqlwhere][id="+$(el).attr('id')+"]")
				            .html(opts.fields[action].name)
				            .attr('href',"#"+action);
				    onchangeevent('change');        
				    
						
				}});
      



                //add a menu to newly added operator
				$("[class=addnewsqlwhereoperator][id='"+opts.counters[3]+"']").sqlsimplemenu({
					menu: 'operatorlist',
					fields:opts.operators,					
				    onselect:function(action, el) {
                    
				    $("[class=addnewsqlwhereoperator][id="+$(el).attr('id')+"]")
				            .html(opts.operators[action].name)
				            .attr('href',"#"+action);
				    //if the operator is in between        
				    if(opts.operators[action].multipleval){				    
				            var val=$("[class=addnewsqlwherevalue][id="+$(el).attr('id')+"]").html();
  				            $("[class=addnewsqlwherevalue][id="+$(el).attr('id')+"]")
				                 .html(val+' and '+val);
				    }else{
				            var val=$("[class=addnewsqlwherevalue][id="+$(el).attr('id')+"]").html();
                            //if there is any and in it..
                            if(val.indexOf('and')!=-1){ 
  				             var vals=$("[class=addnewsqlwherevalue][id="+$(el).attr('id')+"]").html().split('and');
  				                      $("[class=addnewsqlwherevalue][id="+$(el).attr('id')+"]").html(vals[0]);
				            }
				    
				    
				    }        
				    onchangeevent('change');
				    
				    
						
				}});
      

                //add a menu to newly added chain
				$("[class=addnewsqlwherechain][id='"+opts.counters[3]+"']").sqlsimplemenu({
					menu: 'chainlist',
					fields:opts.chain,
				    onselect:function(action, el) {
                    
				    $("[class=addnewsqlwherechain][id="+$(el).attr('id')+"]")
				            .html(opts.chain[action].name)
				            .attr('href',"#"+action);
				    onchangeevent('change');        
				    						
				}
				});



                 opts.counters[3]++;//where counters...

	
				}
				
				});/*end of where handling....*/
      
      
      
      
      
      
      
	  
	});
  };

})(jQuery);