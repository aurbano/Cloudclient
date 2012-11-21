/*
 *	Client JS Code for the Dropbox Client
 *	it should handle files/folders, cache...
 *
 *	By Alejandro U. Alvarez
 */
 
	// File
	// attr must contain all needed attributes
	function File(attr){
		this.parent = attr['parent'];		// Full path to parent
		this.name = attr['name'];			// File/Directory name
		this.loaded = false;				// true if children have been loaded
		this.icon = attr['icon'];			// File/Directory icon
		this.type = attr['type'];			// File/Directory
		this.revision = attr['revision'];	// Revision
		this.rev = attr['rev'];				// Rev
		this.modified = attr['modified'];	// Modified
		this.size = attr['size'];			// Size in bytes
		this.mime = attr['mime'];			// mime type
		this.hash = attr['hash'];			// hash value
	}
// Client class
var Client = {
	request : 0,	// Current request (AJAX) updated on click on a folder
	fillCache : 0,	// Makes sure cache is only tried to fill once
	path : {
		// Handles current path, and path ops
		get : function(){
			// Returns path in path1/path2/current/ format
			var ret = '';
			for(var i=0;i<Client.path.content.length;i++){
				if(Client.path.content[i]=='/') continue;
				ret += Client.path.content[i]+'/';
			}
			return ret;
		},
		add : function(path){
			console.log('path.add('+path+')');
			if(path==undefined){
				console.error('Path undefined, aborting');
				return false;	
			}
			Client.path.content.push(path);
			Client.path.display();
			return true;
		},
		back : function(num){
			// Go back num steps
			console.log('path.back('+num+')');
			console.log(Client.path.content);
			for(var i=0;i<num;i++)
				Client.path.content.pop();
			console.log(Client.path.content);
			Client.path.display();
			return true;
		},
		display : function(){
			var ret = '';
			if(Client.path.content.length<2){
				ret = 'Start';
				$('#back').fadeOut();
			}else{
				for(var i=0;i<Client.path.content.length;i++){
					var text = Client.path.content[i];
					if(text=='/') text = 'Start';
					if(i<Client.path.content.length-1)
						ret += '<a href="#back" rel="'+(Client.path.content.length-i-1)+'">'+text+'</a>/';
					else
						ret += text;
				}
				$('#back').fadeIn();
			}
			$('#breadcrumbs').html(ret);
			Client.backAction();
		},
		content : new Array()	
	},
	backAction : function(){
		// Reset handler for back button
		$("a[href='#back']").unbind('click');
		// Sets the action for #back elements
		$("a[href='#back']").click(function(event){
			event.preventDefault();
			var count = $(this).attr('rel');
			console.log('Client.backAction(): Initial count='+count);
			if(count==null || count<1) count = 1;
			console.log('Client.backAction(): Prepared count='+count);
			Client.back(count);
			return false;
		});
	},
	current : {					// Currently shown directory
		file : null,	// Current directory File object
		view : null 			// Current view DOM element
	},
	cached : new Array(),		// Cached array, it contains {file,view} elements
								// cached[0] should always be the root element 
								// this provides the back(-1) functionality.
	loaded : {					// Next directory (right)
		file : null,	// Loaded directory File object
		view : null				// Loaded view DOM element
	},
	// Add a file to cache
	cache : function(file,view){
		if(file==null || view==null){
			console.error('Cache error: file or view are null');
			return true;
		}
		Client.cached.push({file: file, view: view});
	},
	contents : {},
	// Get the contents of required directory
	// dirId is the ID of the desired directory
	// loadDOM is the container where the files/folders should be drawn into
	getDir : function(dir, callback){
		console.log('Getting dir '+dir);
		if(dir==null) dir = '/';
		console.log('Check cache');
		// Now check in JS cache for directory
		if(!Client.isCached(dir)){
			// We need to build local cache, send AJAX petition
			var  loadDir = dir;
			if(loadDir!=='/') loadDir = Client.path.get()+loadDir;
			console.log('Fetching from AJAX directory='+loadDir);
			$.post('process.php',{type:'list',dir:loadDir},function(data){
				if(!data.done){
					var msg = 'An error ocurred ['+data.msg+']';
					if(data.msg=='Nothing returned from Dropbox')
						msg = 'That folder is empty!';
					alert(msg);
					UI.removeLoaders();
					return false;	
				}
				// Data returned OK, proceed to populate Client.root.contents with the data
				// data.contents contains the directory listing
				console.log(data.contents);
				$.each(data.contents, function() {
					var name = 0;
					var tmp = {};
					$.each(this, function(k, v) {
						if(k=='name') name = v;
						tmp[k] = v;
					});
					Client.contents[name] = new File(tmp); // Store as new File
				});
				console.log('Storing dir in Client.loaded');
				// Store directory in loaded directory
				Client.loaded.file = Client.contents[dir];
				Client.contents[dir].loaded = true;
				callback.call();
			},'json');
		}else{
			// Make sure its in loaded object, and load it otherwise
			if(Client.loaded.file==null || Client.loaded.file.name!==dir){
				// Set requested file in loaded
				console.log('Set requested file in loaded');
				Client.loaded.file = Client.contents[dir];
				console.log(Client.loaded.file);
			}
			callback.call();
		}
	},
	extension : function(filename){
		// Return file extension
		return filename.split('.').pop();
	},
	
	// isCached -> Returns true if directory ID is in cache (in Client.contents)
	isCached : function(dir){
		if(!(dir in Client.contents)) return false;
		// The loaded attr implies that getDir(dirId) was called
		return Client.contents[dir].loaded;
	},
	
	// Draw a directory into a new hidden view
	// it will replace the current Client.loaded element
	// it also returns the created view
	drawDir : function(dir){
		console.log('Draw directory: '+dir);
		if(dir==null) dir = '/';
		
		// This will iterate through this directory (parent)
		// calling UI.drawItem for each file it finds
		// it will do so in a new view.
		// When done, it will move the view into view
		console.log('Dir: '+dir)
		// It must be the one loaded
		if(Client.loaded.file==null || Client.loaded.file.name!==dir){
				console.log('Not in cache, call getDir');
				if(Client.fillCache == 0){
					Client.fillCache = 1;
					Client.getDir(dir,function(){
						Client.drawDir(dir);
						return false;
					});
					return false;
				}else{
					console.error('getDir was called but cache keeps failing');
					return false;	
				}
		}
		Client.fillCache = 0;
		var view = UI.view.create(); // New view to insert data
		$.each(Client.contents, function(k,file) {
			// k contains the file ID
			// v contains the file info
			// so v.parent should be cool
			if(file.parent !== Client.path.get()+dir) return true;
			UI.drawItem(file, view);
		});
		Client.loaded.view = view;
		console.log('Drawing finished, view stored in loaded');
		console.log(Client.loaded.view);
		
		// Apply lightbox
		$('#viewport a.image').lightBox();
		return view;
	},
	
	// Moves current view into cache
	// Then moves desired view into screen
	showDir : function(dir){
		if(dir==null) dir = '/';
		console.log('Have root, proceed');
		if(Client.loaded.file == null || dir !== Client.loaded.file.name){
			// Requested view is not loaded
			console.log('Must load file');
			Client.getDir(dir,function(){
				Client.drawDir(dir);
				Client.showDir(dir);
				return false;
			});
			return false;
		}
		console.log('Directory loaded, add current to cache and move');
		// Not its supposed to be loaded and drawn
		// and stored in Client.loaded
		// Move current to cache and loaded to current, and move
		
		// Add to cache
		if(Client.current.file !== null)
			Client.cache(Client.current.file, Client.current.view);
		else console.error('Current file was undefined');
		Client.current = Client.loaded;
		
		// Reset loaded (It doesn't work if you dont)
		Client.loaded = {			// Next directory (right)
			file : {name : '-'},	// Loaded directory File object
			view : null				// Loaded view DOM element
		};
		
		// Move (old is last cached)
		console.log('Lets move, size of cached: '+Client.cached.length);
		console.log(Client.cached);
		if(Client.cached.length>0){
			UI.move(Client.cached[Client.cached.length-1].view, Client.current.view)
		}else{
			UI.move(null, Client.current.view)
		}
		// Update path
		Client.path.add(Client.current.file.name);
	},
	back : function(num){
		// Goes back num levels
		// by default num = -1 (One level)
		// it's the same as history.back(-1)
		
		console.log('Client.back('+num+')');
		
		if(num==null) num = 1;
		num = Math.abs(num);	// Independent of sign
		
		console.log('Going back '+num+' element/s');
		console.log(Client.cached);
		
		// Last element:
		if(num > Client.cached.length) num = Client.cached.length;
		var last = Client.cached[Client.cached.length-num];
		console.log('Selected element: ');
		console.log(last);
		if(last==undefined){
			console.log('Selected element was undefined WTF, put root in selected');
			last = Client.contents['/'];
		}
		
		// Remove unnecessary elements from cache
		for(var i=0;i<num;i++) Client.cached.pop();
		
		console.log('We now removed unnecesary items');
		console.log(Client.cached);
		
		// Move current to loaded (If back 1 level)
		// Go back num levels
		Client.loaded = Client.current;
		Client.current = last;
		
		Client.path.back(num);
		
		// Move
		if(Client.loaded == undefined)
			Client.loaded = {file : null, view : null};
		UI.move(Client.loaded.view, Client.current.view, 'out')
		// Reset loaded
		if(num>1) Client.loaded = {file : null, view : null};
		// Reset request
		Client.request = 0;
	},
	move : function(elem,dest){
		// Elem and dest must be the ids of the elements to move
		console.log('Client.move('+elem+','+dest+')');
		if(Client.contents[elem]==undefined || Client.contents[dest]==undefined){
			console.error('elem or dest are undefined');
			return false;	
		}
		elem = Client.contents[elem];
		dest = Client.contents[dest]
		// Let's move!
		$.post('process.php',{type:'move',elem:elem.name, dest:dest.name, path:Client.path.get()},function(data){
			if(!data.done){
				alert('An error ocurred ['+data.msg+']');
				UI.removeLoaders();
				return true;	
			}
			// Now we must do some stuff here:
			// Delete moved object from current view:
			console.log('Move successful, now delete item from view');
			$('.drag').remove(); // Item still has "drag" class from draggable
			Client.contents[elem.name].parent = dest.name;
			UI.removeLoaders();			
		},'json');
	},
	addFolder : function(){
		var name = prompt('Folder name?');
		if(!name) return true;
		$.post('process.php',{type:'addFolder', name : name, path:Client.path.get()},function(data){
			if(!data.done){
				alert('An error ocurred ['+data.msg+']');
				return true;	
			}
			// Now we must do some stuff here:
			// Delete moved object from current view:
			console.log('Folder created, draw and store');
			/*
			this.parent = attr['parent'];		// Full path to parent
			this.name = attr['name'];			// File/Directory name
			this.loaded = false;				// true if children have been loaded
			this.icon = attr['icon'];			// File/Directory icon
			this.type = attr['type'];			// File/Directory
			this.revision = attr['revision'];	// Revision
			this.rev = attr['rev'];				// Rev
			this.modified = attr['modified'];	// Modified
			this.size = attr['size'];			// Size in bytes
			this.mime = attr['mime'];			// mime type
			this.hash = attr['hash'];			// hash value*/
			var attr = {
				name : name,
				parent : Client.path.get(),
				icon : 'folder',
				type : 0,
				revision : 0,
				rev : 0,
				modified : '',
				size : 0,
				mime : '',
				hash : ''
			};
			Client.contents[name] = new File(attr);
			UI.drawItem(Client.contents[name],Client.current.view);
			UI.actions();
		},'json');
	}
};