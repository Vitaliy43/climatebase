var alert_title = 'Climatebase.ru';

function myAlert(text){
	
	jAlert(text,alert_title);
}


function ajax_link(url){

	
	$.ajax({
	type: "POST",
    url: url,
	dataType: 'json',
	data: 'type=ajax',
    cache: false,
	beforeSend:function(){
		$('#content').html('<div class="ajax_loader"><img src="/images/waterfalls/ajax-loaders/content.gif"></div>');	
	},
	error: function(){
		ajax_error();
	},
    success: function(data){
		$('#content').html(data.content);
		$.scrollTo('#content',1000);
		$('#stationpoint').val('');
		History.pushState(null,document.title,document.location.href);
		History.replaceState(null,null,url);
		if(typeof data.title!='undefined')
			document.title=data.title;
	}
    });
	
	
//	hash_change(url);
}

function delete_station(object,station){
	
	jConfirm('Вы уверены, что хотите удалить станцию '+station+'?',alert_title,function(r){
		
	if(r==true){
	$.ajax({
	type: "POST",
    url: object.href,
	dataType: 'json',
	data: 'type=ajax',
    cache: false,
	error: function(){
		myAlert('Неизвестная ошибка!');
	},
    success: function(data){
	
		if(data.answer==1){
			$('#'+station+' .link').remove();
			$('#'+station+' .delete_link').remove();
			$('#'+station+' .label').remove();
			$('#'+station).html('<div>'+station+': удалено</div>');
		}
		else{
			myAlert('Неизвестная ошибка');
		}
		
	}
    });
		
	}
	}
	);
}

function parameters_list(object){
	
}


function delete_parameter(object,station,parameter,period){
	
	var container=$('#'+parameter).html();
	jConfirm('Вы уверены, что хотите удалить данный параметр?',alert_title,function(r){
		
	if(r==true){
	$.ajax({
	type: "POST",
    url: object.href,
	dataType: 'json',
	data: 'type=ajax&parameter='+parameter+'&period_id='+period,
    cache: false,
	beforeSend:function(){
		$('#'+parameter).html('<div class="ajax_loader"><img src="/images/waterfalls/ajax-loaders/content.gif"></div>');
	},
	error: function(){
		myAlert('Неизвестная ошибка!');
		$('#'+parameter).html(container);
	},
    success: function(data){
	
		if(data.answer==1){
		
			$('#'+parameter).html('<div>Удалено</div>');
		}
		else{
			myAlert('Неизвестная ошибка');
			$('#'+parameter).html(container);

		}
		
	}
    });
		
	}
	}
	);
}


function ajax_link_inner(url){

	$.ajax({
	type: "POST",
    url: url,
	dataType: 'json',
	data: 'type=ajax',
    cache: false,
	beforeSend:function(){
		$('#content').html('<div class="ajax_loader"><img src="/images/waterfalls/ajax-loaders/content.gif"></div>');	
	},
	error: function(){
		ajax_error();
	},
    success: function(data){
		$('#content').html(data.content);
		$.scrollTo('#content',1000);
		History.pushState(null,document.title,document.location.href);
		History.replaceState(null,null,url);
		if(typeof data.title!='undefined')
			document.title=data.title;
	}
    });
	
}

function ajax_error(){	
	var message='Подгрузка завершилась неудачей...';
	$('#content').html(message);

}

function merge_arrays(arr) {
	var merged_array = arr;
	for (var i = 1; i < arguments.length; i++) {
	merged_array= merged_array.concat(arguments[i]);
	}
	return merged_array;
}

function ajax_search(object){
	var stationpoint=$('#stationpoint').val();
	
	$.ajax({
	type: "POST",
    url: object.action,
	dataType: 'json',
	data: 'type=ajax&stationpoint='+stationpoint,
    cache: false,
	error: function(){
		ajax_error();
	},
    success: function(data){

		if(data.answer==1){
			ajax_link_inner(data.url);	
			$('#stationpoint').val(data.station);
		}
		else{
			myAlert('Ошибка!');
		}
	}
    });
	
}
	function ajax_search_from_list(stationpoint){
		
	var url = $('#form_search').attr('action');
	
	$.ajax({
	type: "POST",
    url: url,
	dataType: 'json',
	data: 'type=ajax&stationpoint='+stationpoint,
    cache: false,
	error: function(){
		ajax_error();
	},
    success: function(data){

		if(data.answer==1){
			ajax_link_inner(data.url);	
			$('#stationpoint').val(data.station);
		}
		else{
			myAlert('Ошибка!');
		}
	}
    });
	}
	
function hash_change(url){
	if(supportsHistoryAPI == false)
		document.location.href = url;
	if(document.location.href == url)
		return false;
	from_layout = false;
	last_url = document.location.href;		
	history.pushState(null,document.title,document.location.href);
	History.replaceState(null,null,url);
}

