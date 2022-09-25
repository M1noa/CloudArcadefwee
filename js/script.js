"use strict";
var listGames;
class GameList {
	getList(url){
		$('.fetch-loading').css('display', 'block');
		$('.fetch-list').css('display', 'none');
		let wait = new Promise((res) => {
			var xhr = new XMLHttpRequest();
			xhr.open('GET', url);
			xhr.onload = function() {
				if (xhr.status === 200) {
					let arr = JSON.parse(xhr.responseText);
					res(arr);
				}
				else {
					res(false);
				}
			}.bind(this);
			xhr.send();
		});
		return wait;
	}
	generateList(arr){
		listGames = arr;
		let result = '';
		let dom = document.getElementById("gameList");
		for(let i=0; i<arr.length; i++){
			let thumb;
			if(arr[i].assetList.length > 1){
				let game_id = arr[i].url.replace("html5.", "img.");
				game_id = game_id.slice(0, -1);
				arr[i].thumb_1 = game_id+'/512x384.jpg';
				arr[i].thumb_2 = game_id+'/512x512.jpg';
				let id = (i+1);
				result += '<tr id="tr'+id+'"><th scope="row">'+(id)+'</th><td>'+arr[i].title+'</td><td><a href="'+arr[i].url+'" target="_blank">Play</a></td><td><img src="'+arr[i].thumb_2+'" width="80px" height="auto"></td><td>'+arr[i].categorylist+'</td><td><a href="#" onclick="addData('+i+')">Add game</a></td></tr>';
			}	
		}
		dom.innerHTML = result;
		function _getCategories(e){
			let r = '';
			for(let i=0; i<e.categoryList.length; i++){
				r += e.categoryList[i].name+', ';
			}
			return r;
		}
		$('.fetch-list').css('display', 'block');
		$('.fetch-loading').css('display', 'none');
	}
}
var getGame = new GameList();
function sendRequest(data, reload, action, id){
	$.ajax({
		url: '/admin/request.php',
		type: 'POST',
		dataType: 'json',
		data:data,
		success: function (data) {
			//console.log(data.responseText);
		},
		error: function (data) {
			//console.log(data.responseText);
		},
		complete: function (data) {
			console.log(data.responseText);
			if(reload){
				location.reload();
			}
			if(action === 'edit-page'){
				set_edit_modal(JSON.parse(data.responseText));
			} else if(action === 'remove'){
				$('.fetch-list').removeClass('disabled-list');
				if(id){
					remove_from_list(id-1);
				}
			}
		}
	});
}
function addData(id){
	$('.fetch-list').addClass('disabled-list');
	let arr = listGames[id];
	let data = {
		action: 'addGame',
		source: 'gamemonetize',
		title: arr.title,
		thumb_1: arr.thumb_1,
		thumb_2: arr.thumb_2,
		description: arr.description,
		url: arr.url,
		instructions: arr.instructions,
		width: arr.width,
		height: arr.height,
		category: arr.categorylist,
		tags: arr.tags,
	}
	sendRequest(data, false, 'remove', id+1);
}
function remove_from_list(id){
	$("#tr"+(id+1)).remove();
}
function set_edit_modal(data){
	$('#edit-id').val(data.id);
	$('#edit-title').val(data.title);
	$('#edit-slug').val(data.slug);
	$('#edit-content').text(data.content);
	$('#edit-createdDate').val(data.createdDate);
	$('#edit-page').modal('show');
}
$(document).ready(function(){
	$("#add-all").on('click', function(){
		let f = $("#gameList > tr");
		f.each(function( index ) {
			let id = Number($( this ).attr('id').substring(2));
			addData(id-1);
		});
	});
	$( "form" ).submit(function( event ) {
		let arr = $( this ).serializeArray();
		if($(this).attr('id') === 'form-fetch'){
			event.preventDefault();
			// GameMonetize.com
			let url = 'https://gamemonetize.com/rssfeedcms.php?format=json&category='+arr[1].value+'&collection='+arr[0].value+'&type=html5&company=All&amount='+arr[2].value+'';
			getGame.getList(url).then((res)=>{
				getGame.generateList(res);
			});
		} else if($(this).attr('id') === 'form-remote'){
			event.preventDefault();
			let data = {
				action: 'addGame',
				source: 'remote',
				title: get_value(arr, 'title'),
				thumb_1: get_value(arr, 'thumb_1'),
				thumb_2: get_value(arr, 'thumb_2'),
				description: get_value(arr, 'description'),
				url: get_value(arr, 'url'),
				instructions: get_value(arr, 'instructions'),
				width: get_value(arr, 'width'),
				height: get_value(arr, 'height'),
				tags: '',
			}
			if(get_value(arr, 'slug')){
				data.slug = get_value(arr, 'slug');
			}
			data.category = get_comma(get_category_list(arr));
			sendRequest(data, true);
		} else if($(this).attr('id') === 'form-newpage'){
			event.preventDefault();
			let data = {
				action: 'newPage',
				title: get_value(arr, 'title'),
				slug: (get_value(arr, 'slug').toLowerCase()).replace(/\s+/g, "-"),
				createdDate: get_value(arr, 'createdDate'),
				content: get_value(arr, 'content'),
			}
			sendRequest(data, true);
		} else if($(this).attr('id') === 'form-editpage'){
			event.preventDefault();
			let data = {
				action: 'editPage',
				title: get_value(arr, 'title'),
				slug: (get_value(arr, 'slug').toLowerCase()).replace(/\s+/g, "-"),
				id: get_value(arr, 'id'),
				createdDate: get_value(arr, 'createdDate'),
				content: get_value(arr, 'content'),
			}
			sendRequest(data, true);
		}
	});
	$('.remove-category').click(function() {
		if(confirm('Are you sure?\nDeleting category also delete all games on it (if there are).')){
			window.open('/admin/request.php?action=deleteCategory&id='+$(this).attr('id')+'&redirect=/admin/dashboard.php?viewpage=categories', '_self');
		}
	});
	$( "#newpagetitle" ).click(function() {
		let parent = $( "#newpagetitle" );
		parent.change(function(){
			$( "#newpageslug" ).val((parent.val().toLowerCase()).replace(/\s+/g, "-"));
		});
	});
	$( ".deletepage" ).click(function() {
		let id = $(this).attr('id');
		if(confirm('Are you sure want to delete this page ?')){
			let data = {
				action: 'deletePage',
				id: id,
			}
			sendRequest(data, true);
		}
	});
	$( ".editpage" ).click(function() {
		let id = $(this).attr('id');
		let data = {
			action: 'getPageData',
			id: id,
		}
		sendRequest(data, false, 'edit-page');
	});
	$(".custom-file-input").on("change", function() {
	  var fileName = $(this).val().split("\\").pop();
	  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
	function get_value(arr, key){
		for(let i=0; i<arr.length; i++){
			if(arr[i].name === key){
				return arr[i].value;
			}
		}
	}
	function get_category_list(arr){
		let cats = [];
		for(let i=0; i<arr.length; i++){
			if(arr[i].name === 'category'){
				cats.push({name: arr[i].value});
			}
		}
		return cats;
	}
});

function get_comma(arr){
	let res = '';
	arr.forEach((item, index)=>{
		res += item['name'];
		if(index < arr.length-1){
			res += ',';
		}
	});
	return res;
}