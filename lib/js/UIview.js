// FF & IE fix
if(!window.console) console = { log: function(){} };
/*
 *	UI JS Code for the Dropbox Client
 *	It handles all UI generation and interactions.
 *
 *	It requires Client.js as the data handler
 *
 *	By Alejandro U. Alvarez
 */
var UI = {
	viewport : { w : 0, h : 0 },
	mouse : { x : 0, y : 0 },
	dragging : 0,
	view : {
		current : $('.view'),
		create : function(){
			return $('<div class="view" style="right:-'+UI.viewport.w+'px; width:'+UI.viewport.w+'px; height:'+UI.viewport.h+'px"></div>').appendTo('#viewport'); // view is the reference to the new view
		}
	},
	icons : {
		width : 0,
		height : 0,
		preview : 0,
		// Change icon size
		size : function(size){
			// That size will be the width, height will be 3.5%
			if(size<70) return true;	// Min width
			if(size>200) return true;	// Max width
			this.width = size;
			this.height = size+size*0.37;
			this.preview = this.height*0.6-14;
			$('.item a').css({
				width : UI.icons.width,
				height : UI.icons.height
			});
			$('.file .preview').css({
				height : UI.icons.preview
			});
		}
	},
	init : function(){
		// This is somehow the "constructor" for the UI
		// it creates some elements that are required
		// Adjust sizes on resize
		$(window).bind('resize', UI.adjust);
		// Track mouse
		$(document).mousemove(function(e){
			UI.mouse.x = e.pageX;
			UI.mouse.y = e.pageY;
		});
		// Custom context menu
		$('#viewport').bind("contextmenu", function(event) {
			event.preventDefault();
			console.log('Call context menu');
			var elem = document.elementFromPoint(UI.mouse.x, UI.mouse.y) // x, y
			$('.item').removeClass('under');
			if($(elem).hasClass('icon') || $(elem).hasClass('name') || $(elem).attr('href')=='#open' || $(elem).hasClass('type')){
				// Select the parent:
				elem = $(elem).parents('.item')	
			}
			if($(elem).hasClass('item')) $(elem).addClass('under');
			var selectedItem = true;
			if($('.under a').length < 1) selectedItem = false;
			var into = 0;
			if(selectedItem) into = $('.under a').attr('id').replace(/-_-_/g,' '); // ID fixes
			console.log('Selected '+into);
			$("#context-menu").css({top: event.pageY + "px", left: event.pageX + "px"}).show();
		});
		// Remove context menu
		$(document).bind("click", function(event) {
			$("#context-menu").hide();
			$('.item').removeClass('under');
		});
		// Initial adjustments
		this.adjust();
		// Set initial icons size
		this.icons.size(110);
		// Go back event
		Client.backAction();
		$("a[rel='info']").click(function(event){
			event.preventDefault();
			var box = $(this).attr('href');
			UI.hideViews(function(){
				$('#infoBox div').hide();
				$(box).show();
				$('#infoBox').fadeIn();
			});
		});
		$("#infoBox a[href='#close']").click(function(event){
			event.preventDefault();
			$('#infoBox').fadeOut(function(){
				UI.restoreViews();
			});
		});
		// Change icon size
		$("a[href='#icons']").click(function(e){
			e.preventDefault();
			UI.icons.size(parseInt(UI.icons.width) + parseInt($(this).attr('rel')));
		});
		// Create folder
		$("a[href='#addFolder']").click(function(e){
			e.preventDefault();
			Client.addFolder();
		});
		// Start the Client
		Client.showDir();
	},
	adjust : function(){
		// Adjusts all sizes and stuff
		var height = $(window).height();
		var width = $(window).width();
		var headerHeight = $('#header').height();
		var sidebarWidth = $('#sidebar').width();
		// Update local viewport dimensions
		UI.viewport.h = height-headerHeight;
		UI.viewport.w = width-sidebarWidth;
		// Update sidebar
		$('#sidebar').height(height-headerHeight);
		// Update viewport
		$('#viewport').height(UI.viewport.h);
		$('#viewport').width(UI.viewport.w);
		// Update views
		$('.view').height(UI.viewport.h);
		$('.view').width(UI.viewport.w);
	},
	extColor : function(ext){
		// Returns color for given ext
		switch(ext){
			case 'pdf':
				return '#eb0000';
				break;
			case 'doc':
			case 'docx':
				return '#0058b8';
				break;
			case 'xls':
			case 'xlsx':
			case 'java':
				return '#61d702';
				break;
			case 'txt':
				return '#888';
				break;
			case 'zip':
			case '7z':
			case 'rar':
			case 'gz':
			case 'tgz':
				return '#cd6a00';
				break;
			case 'mp4':
			case 'avi':
			case 'mpeg':
			case 'ogg':
			case 'flv':
				return '#09F';
				break;
			default:
				return '#eb0000';	
		}
	},
	// Draws an item into specified view
	// file must be of type File
	// dom must be a jQuery reference to the view
	drawItem : function(file, dom){
		var type = 'folder';
		var classes = '';
		var extra = '';
		var style = '';
		var href = '#open';
		
		if(file.type==0) extra = '<div class="folder"></div><div class="loading"><img src="/img/load/folder_34.gif" /></div>';
		if(file.icon=='folder_photos') extra += '<div class="type"><img src="/img/icons/pictures_folder.png" width="43" height="43" /></div>';
		
		if(file.icon=='folder_user') extra += '<div class="type"><img src="/img/icons/shared_folder.png" width="43" height="43" /></div>';
		
		if(file.type==1){
			type = 'file';
			var ext = Client.extension(file.name);
			extra = '<div class="format" style="background:'+UI.extColor(ext)+'">'+ext+'</div>';
			if(ext=='txt' || ext == 'doc' || ext == 'docx'){
				extra += '<div class="preview" style="height:'+UI.icons.preview+'px">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sit amet leo eget ligula suscipit dictum.<br /><br />Sed tincidunt sagittis leo, id interdum urna egestas vel.</div>';	
			}
			if(ext=='zip' || ext == '7z' || ext == 'rar' || ext=='gz' || ext=='tgz'){
				type += ' zip';
			}
			if(ext=='mp4' || ext == 'avi' || ext == 'mpeg' || ext=='ogg' || ext=='flv'){
				extra += '<div class="type"><img src="/img/icons/pictures_folder.png" width="43" height="43" /></div>';
			}
		}
		
		// Check for images
		if(file.mime!==null){
			var mime = file.mime.split('/');
			if(mime[0] == 'image'){
				// This is a picture
				extra = '';
				console.log('Displaying thumb, current path:');
				console.log(Client.path.content);
				style += 'background:url(\'/thumb/'+file.parent+'/'+file.name+'\') no-repeat center !important; box-shadow:none;';
				classes += ' image';
				href = '/file/'+file.parent+'/'+file.name;
			}
		}
		
		dom.append('<div class="item"><a href="'+href+'" id="'+file.name.replace(/ /g,'-_-_')+'" rel="'+file.type+'" class="'+classes+'" style="width:'+UI.icons.width+'px; height:'+UI.icons.height+'px"><div class="icon '+type+'" style="'+style+'">'+extra+'</div><div class="name">'+file.name+'</div></a></div>');
	},
	// Move views in the screen
	// Both params must be jQuery DOM objects
	// dir is the direction, "in" by default. "in" means opening a folder, "out" means going back.
	move : function(oldView, newView,dir){
		if(dir==null) dir = 'in';
		console.log('Move views (Show old/new) '+dir);
		console.log(oldView);
		console.log(newView);
		UI.loading(0);
		var mapOld = {right : UI.viewport.w+'px'};
		var mapNew = {right : 0};
		if(oldView!==null){
			// If going out, change coordinates
			if(dir!=='in') mapOld = { right : -UI.viewport.w+'px' };
			$(oldView).animate(mapOld, function(){
				// Since there is an oldView, display back button
				if(Client.path.length > 1) $('#back').fadeIn();
				// Remove loaders
				UI.removeLoaders();
			});
		}
		if(newView!==null){
			$(newView).animate(mapNew, function(){
				UI.actions();
			});
		}
	},
	loading : function(state){
		// Set loading state, 1 = loading
		if(state){
			$('#loading').fadeIn();	
		}else{
			$('#loading').fadeOut();	
		}
	},
	fullPath : function(filename){
		if(Client.path.get().substr(Client.path.content.length-1)!=='/') filename = '/'+filename;
		return Client.path.get()+filename;
	},
	removeLoaders : function(){
		$('.selected').removeClass('selected');
		$('.loading').hide();
	},
	actions : function(){
		// Reapply actions after new content is opened
		// But first unbind from previously loaded elements
		$("a[href='#open']").unbind('click');
		$("a[href='#open']").click(function(event){
			event.preventDefault();
			if($(this).parents('.item').hasClass('drag')){
				$(this).parents('.item').removeClass('drag');
				return false;
			}
			var dir = $(this).attr('id').replace(/-_-_/g,' '); // ID Fix
			if($(this).attr('rel')==0){
				$(this).find('.icon').addClass('selected').find('.loading').fadeIn();
				console.log('Requested dir='+dir);
				if(Client.request == dir){
					console.error('Request duplicated. Abort');
					return true;	
				}
				Client.request = dir;
				Client.showDir(dir);
			}else{	// Click on a file. Download file:
				//alert('/file'+Client.path+Client.contents[dir].name);
				window.location = '/file/'+UI.fullPath(Client.contents[dir].name);
			}
		});
		//$('.item').draggable('destroy');
		$('.item').draggable({
			containment: "#viewport",
			opacity: 0.7,
			cursorAt: {top:10, left:-5},
			helper : function( event ) {
				return $(this).clone();
				//return $(this).clone().prepend("<div class='helper'>1</div>");
                //return $("<div class='helper'>1</div>");
            },
			start: function(event, ui) {
				$(this).addClass('drag');
				UI.dragging = $(this).children('a').attr('id').replace(/-_-_/g,' '); // ID fixes
				if(Client.contents[UI.dragging].icon=='folder_public'){
					// We are trying to drag the public folder, not permitted
					console.log('Trying to drag Public folder, not allowed');
					$(this).draggable({disabled : true});
					return true;
						
				}
			},
			stack: '.view',
			drag : function(event,ui){
				var offset = ui.position;
				var elem = document.elementFromPoint(UI.mouse.x, UI.mouse.y) // x, y
				$('.item').removeClass('under');
				if($(elem).hasClass('icon') || $(elem).hasClass('name') || $(elem).attr('href')=='#open' || $(elem).hasClass('type')){
					// Select the parent:
					elem = $(elem).parents('.item')	
				}
				if($(elem).find('.file').length>0) return true;
				if($(elem).hasClass('item')) $(elem).addClass('under');
				if($('.under a').length < 1) return true;
				var into = $('.under a').attr('id').replace(/-_-_/g,' '); // ID fixes
				if(UI.dragging == into){
					console.log('Cant move into itself, tss corta eh!');
					$('.item').removeClass('under');
					return true;	
				}
				if((Client.contents[UI.dragging].icon=='folder_user' && Client.contents[into].icon=='folder_user') || Client.contents[UI.dragging].icon=='folder_public'){
					$('.item').removeClass('under');
					return true;
				}
			},
			stop : function(event,ui){
				// Dragging stopped
				// Was over a folder?
				if($('.under a').length < 1) return true;
				var into = $('.under a').attr('id').replace(/-_-_/g,' '); // ID fixes
				// Check if you are moving a shared folder into a shared folder
				if((Client.contents[UI.dragging].icon=='folder_user' && Client.contents[into].icon=='folder_user') || Client.contents[UI.dragging].icon=='folder_public'){
					console.log('Trying to move shared into shared, or public. Aborting');
					$('.item').removeClass('under');
					return true;
				}
				console.log('Moving '+Client.contents[UI.dragging].name+' into '+Client.contents[into].name);
				if(UI.dragging == into){
					console.log('Cant move into itself, abort');
					$('.item').removeClass('under');
					return true;	
				}
				$('.under').find('.icon').addClass('selected').find('.loading').fadeIn();
				$('.item').removeClass('under');
				Client.move(UI.dragging,into);
			}
		});
	},
	hideViews : function(callback){
		// Hides current view, to show help for example
		$(Client.current.view).animate({
			right : UI.viewport.w+'px'
		},function(){
			callback.call();	
		});
	},
	restoreViews : function(){
		// Restores previewsly hidden views	
		$(Client.current.view).animate({
			right : 0
		});
	}
	
};
$(document).ready(function(e) { UI.init(); });