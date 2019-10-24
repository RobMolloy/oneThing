	//~ var parameters = {'file':'file.php', 'nav':'case', 'printTo':'id2', 'runOnReturn':function1, 'additionalParameters':{}, 'f':'','getValuesFrom':'id1', 'loadText':true};
	
	
	/*Generic functions - used on all pages*/
	function getElementValues(params={}){
		var f = ('f' in params && params.f!='' ? params.f : new FormData);
		var getValuesFrom = initElement('getValuesFrom' in params ? params.getValuesFrom : '');
		if(getValuesFrom!=='' && getValuesFrom!==undefined){
            var all = getValuesFrom.querySelectorAll('input,select,textarea');
            
            var valid;
			for(var i=0; i<all.length; i++){
                valid = true;
				if(all[i].name==''){valid=false;}
                if(all[i].type=='checkbox' && all[i].checked==false){valid=false;}
                if(valid){f.append(all[i].name,all[i].value);}
            }
		}
		return f;
	}
	
	function ajaj(params={}){
		//~ var parameters = use ajaj.php
		//~ initialize parameters
		var file = ('file' in params ? params.file : ''); //~ !essential parameter!
		var nav = ('nav' in params ? params.nav : ''); //~ !pass in file or essential parameter!
		var printTo = ('printTo' in params ? params.printTo : ''); //~ printTo can pass idString or elm 
		var runOnReturn = ('runOnReturn' in params ? params.runOnReturn : '');
		var additionalParameters = ('additionalParameters' in params ? params.additionalParameters : {}); //~ parameters to be used with the response text
		var f = ('f' in params && typeof(f)!='string' ? params.f : new FormData);
		var getValuesFrom = ('getValuesFrom' in params ? params.getValuesFrom : ''); //~ getValuesFrom can pass idString or elm 
		var loadText = ('loadText' in params ? params.loadText : true);
		
		//~ populate formdata
		if(nav!=''){f.append('nav',nav);}
		f = getElementValues({'getValuesFrom':getValuesFrom, 'f':f});
		
		//~ initialize printToElement, createHiddenJax if necessary
		if(printTo=='' || printTo.toLowerCase=='hiddenjax'){createHiddenJax(); printTo='hiddenJax';}
		var printToElement = (typeof(printTo)=='string' ? document.getElementById(printTo) : printTo);
		
		//~ show load text if true
		if(loadText){printToElement.innerHTML = 'processing...';}
		
		//~ make the xml http request to file using the form data
		const request = new XMLHttpRequest();
		request.onload=function() {
			if(this.status === 200){
				var response = initObject(this.responseText);
                
				//~ ONLY TRUE IN DEV ////////////////////////////////////////////////////////////
				if(true){createResponseLog();document.getElementById('responseLog').innerHTML = prettifyJson(response);}
				
                //~ if no function provided print responseText to printToElement
				if(runOnReturn==''){printToElement.innerHTML = response;} 
                else {
					runOnReturn = (typeof(runOnReturn)=='string' ? window[runOnReturn] : runOnReturn);
					
					var runOnReturnParams = additionalParameters;
					runOnReturnParams.json = response;
					runOnReturnParams.printTo = ('printTo' in runOnReturnParams ? runOnReturnParams.printTo : printTo);
					runOnReturn(runOnReturnParams);
				}
			}
		}
		request.open("post", file);
		request.send(f);
	}
    
    function initElement(element=''){
        if(element==''){return document.createElement("div");}
        return (document.getElementById(element) ? document.getElementById(element) : element);
    }
    
    function initObject(object=''){
        object = (object=='' ? {} : object);
        return (typeof(object)=='object' ? object : JSON.parse(object));
    }
	
	function createHiddenJax(){
        if(!document.getElementById('hiddenJax')){
            var hjax = document.createElement("div");
            hjax.setAttribute("id", "hiddenJax");
            hjax.setAttribute("class", "hidden");
            document.body.appendChild(hjax);
        }
	}
	
	function createResponseLog(){
        if(!document.getElementById('responseLog')){
            var rLog = document.createElement("div");
            rLog.setAttribute("id", "responseLog");
            rLog.setAttribute("class", "hidden");
            document.body.appendChild(rLog);
        }
	}
	
	function getJsonFromGetArray(){
		var getArray = getGetArray();
		var json = {};
		
		if(getArray!={}){
			var json = ('json' in getArray ? decodeURI(getArray.json) : getDefaultJsonString());
			json = (typeof(json)=='object' ? json : JSON.parse(json));
		}
		return json;
	}
	
	function getCurrentFilename(){
		return window.location.href.split('/').pop().split('?')[0];
	}
	
	function getGetArray(){
		var getString = window.location.search.substring(1);
		var getArray = {};
		
		if(getString.length!=0){
			var getPairs = getString.split('&');
			for(key in getPairs){getArray[getPairs[key].split('=')[0]] = getPairs[key].split('=')[1];}
		}
		
		return getArray;
	}
	
	function getDefaultJsonString(){
		return JSON.stringify({
			exists:false,
			success:false,
			valid:false,
			newlyAdded:false,
			datarow:{},
			labelrow:{},
			errors:{}
		});
	}
	
	function getDefaultJsonObject(){
		return JSON.parse(getDefaultJsonString());
	}
	
    function prettifyJson(json={}){
        json = typeOf(json)=='string' ? JSON.parse(json) : json;
        return '<pre>' + JSON.stringify(json,null,4) + '</pre>';
    }
    
    function getContainerHtml(params={}){
        var type = ('type' in params ? params.type : '');
        var label = ('label' in params ? params.label : '');
        var name = ('name' in params ? params.name : '');
        var html = `<h1>${label}</h1><div class="${type}" id="${name}"></div>`;
        
        return html;
    }
	
    function typeOf(obj) {
        return typeof(obj);
        //~ return {}.toString.call(obj).split(' ')[1].slice(0, -1).toLowerCase();
    }
    
    function toShortDate(timestamp){
        var t = new Date(0);
        t.setSeconds(timestamp);
        
        //~ var formatted = t.toString("dd.mm.yyyy hh:MM:ss");
        //~ var formatted = t.toISOString("dd.mm.yyyy hh:MM:ss");

        return t.toLocaleDateString();
    }
    
    function isValid(){
        
    }
    
    function appendToWrapperMain(text){
        document.getElementById('wrapperMain').insertAdjacentHTML('beforeend',text);
    }
    
    function toggleResponseLog(){
        if(!document.getElementById('responseLog')){createResponseLog();}
        document.getElementById('responseLog').classList.toggle("hidden");
    }
    
    function getElementsFromHtmlString(html){
        var template = document.createElement('template');
        html = html.trim(); // Never return a text node of whitespace as the result
        template.innerHTML = html;
        return template.content.firstChild;
    }
    
    function isset(array){
        //~ TO USE: isset(() => arr1.json.datarow.wobble.blah) ? 'true' : 'false';
        try{return typeof array() !== 'undefined'}
        catch (e){return false;}
    }
    
    function issetReturn(array){
        //~ TO USE: isset(() => arr1.json.datarow.wobble.blah);
        //~ CANNOT USE isset as a replacement for the next 3 lines of code - WHY??? WHO CARES!!!
        var tof;
        try {tof = typeof array()!=='undefined';}
        catch (e){tof = false;}
        
        return tof ? array() : false;
    }
    
	/*Login Functions*/
	function getLoginHtml(params={}){
		var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
		
		var container = ('container' in params ? params.container : true);
		var useGet = ('useGet' in params ? params.useGet : false);
		
		if(useGet){
			json = getJsonFromGetArray();
		}
		
		return `${container ? '<div class="panel"><h1>Log In</h1><div class="form" id="loginForm">' : ''}
					${0 in json.errors ? json.errors[0].map((error)=>`<p>${error}</p>`).join('') : ``}
					${'usr_email' in json.errors ? json.errors.usr_email.map((error)=>`<p>${error}</p>`).join('') : ``}
					<input type="text" name="usr_email" placeholder="Email" value="${'usr_email' in json.datarow ? json.datarow.usr_email : ''}">
					
					${typeof(json.errors.usr_password)!=='undefined'? json.errors.usr_password.map((error)=>`<p>${error}</p>`).join(''): ``}
					<input type="password" name="usr_password" placeholder="Password" value="${'usr_password' in json.datarow ? json.datarow.usr_password : ''}">
					
					<button name="submitLogin" onclick="ajaj({'file':'login.nav.php?nav=submitLogin', 'printTo':'loginForm', 'getValuesFrom':'loginForm',\'runOnReturn\':handleLoginResponse});">Log In!</button>
				${container ? '</div></div>' : ''}`;
	}
	
	function handleLoginResponse(params={}){
		var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
		
		var datarow = json.datarow;
		var success = (!json.valid || !json.exists ? false : true);
		var currentFile = getCurrentFilename();
		var printTo = ('printTo' in params || params.printTo!=='' ? params.printTo : 'loginForm');
		
		if(!success && currentFile!='login.php'){
			window.location.href = `login.php?json=${encodeURI(JSON.stringify(json))}`;
			return;
		}
		if(!success){
			document.getElementById(printTo).innerHTML = getLoginHtml({'json':json,'container':false});
			return;
		}
		
		if(success){
			window.location.href = 'index.php';
		}
	}
	
	
	/*Sign up functions*/
	function getSignupHtml(params={}){
		var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
		var container = ('container' in params ? params.container : true);
		var useGet = ('useGet' in params ? params.useGet : false);
		
		if(useGet){
			var getArray = getGetArray();
			for(key in getArray){json.datarow[key] = getArray[key]}
		}
		
		return `${container ? '<div class="panel"><h1>Sign Up</h1><div class="form" id="signupForm">' : ''}
					${0 in json.errors ? json.errors[0].map((error)=>`<p>${error}</p>`).join('') : ``}
					${'usr_first_name' in json.errors ? json.errors['usr_first_name'].map((error)=>`<p>${error}</p>`).join('') : ``}
					<input type="text" name="usr_first_name" placeholder="First Name" value="${'usr_first_name' in json.datarow ? json.datarow.usr_first_name : ``}">
					
					${'usr_last_name' in json.errors ? json.errors['usr_last_name'].map((error)=>`<p>${error}</p>`).join('') : ``}
					<input type="text" name="usr_last_name" placeholder="Last Name" value="${'usr_last_name' in json.datarow ? json.datarow.usr_last_name : ``}">
					
					${'usr_email' in json.errors ? json.errors['usr_email'].map((error)=>`<p>${error}</p>`).join('') : ``}
					<input type="text" name="usr_email" placeholder="Email" value="${'usr_email' in json.datarow ? json.datarow.usr_email : ``}">
					
					${'usr_password' in json.errors ? json.errors['usr_password'].map((error)=>`<p>${error}</p>`).join('') : ``}
					<input type="password" name="usr_password" placeholder="Password" value="">
					<input type="password" name="usr_password_repeat" placeholder="Confirm Password" value="">
		
					<button name="submitSignup" onclick="ajaj({'file':'signup.nav.php?nav=submitSignup', 'printTo':'signupForm', 'getValuesFrom':'signupForm', 'runOnReturn':handleSignupResponse});" >Sign Up!</button>
				${container ? '</div></div>' : ''}`;
		
	}
	
	function handleSignupResponse(params={}){
		var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
		
		var datarow = json.datarow;
		var success = (!json.valid || !json.exists ? false : true);
		var printTo = ('printTo' in params || params.printTo!=='' ? params.printTo : 'loginForm');
		
		if(!success){
			document.getElementById(printTo).innerHTML = getSignupHtml({'json':json,'container':false});
			return;
		} else {
			window.location.href = 'login.php';
		}
	}
	
	function handleLogoutResponse(params={}){
		window.location.href = 'index.php';
	}
	
    /*Thing functions*/
    function getThingJson(params={'tng_id':'','runOnReturn':''}){
        var tng_id = ('tng_id' in params ? params.tng_id : '');
        var runOnReturn = ('runOnReturn' in params ? params.runOnReturn : '');
        //~ var json = ('json' in params ? params.json : '');
        
        var f = new FormData(); 
        f.append('tng_id',tng_id);
        
        ajaj({'file':'thing.nav.php?nav=getThingJson','runOnReturn':runOnReturn,'f':f});
    }
    
    function getThingFormHtml(params={}){
        //~ {'json':json,'container':true,'fillInputs':true,'editMode':true};
        var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
		var datarow = ('datarow' in json ? json.datarow : []);
		var errors = ('errors' in json ? json.errors : []);
        
		var container = ('container' in params ? params.container : false);
		var editMode = ('editMode' in params ? params.editMode : true);
		var fillInputs = ('fillInputs' in params ? params.fillInputs : true);
        
        var heading;
        var buttons;
        
        if(editMode){
            heading = `Edit ${datarow.tng_title}`;
            buttons = 
                `<button name="displayThing" onclick="getThingJson({'tng_id':${datarow.tng_id},'runOnReturn':displayThing});">Back</button> 
                <button name="saveThing" onclick="var f = new FormData();f.append('tng_id',${datarow.tng_id});ajaj({'file':'thing.nav.php?nav=saveThing', 'f':f, 'printTo':thing_${datarow.tng_id}, 'getValuesFrom':'thing_${datarow.tng_id}', 'runOnReturn':handleSubmitThingResponse});">Save</button> 
                <button name="deleteThing" onclick="deleteThing(${datarow.tng_id});">Delete</button>`;
                //~ <button name="deleteThing" onclick="var f = new FormData();f.append('tng_id',${datarow.tng_id});ajaj({'file':'thing.nav.php?nav=deleteThing', 'f':f, 'runOnReturn':handleDeleteThingResponse});">Delete</button>`;
        } else {
            heading = `What are you grateful for today?`;
            buttons = 
                `<button name="submitThing" onclick="ajaj({'file':'thing.nav.php?nav=submitThing', 'printTo':'thingForm', 'getValuesFrom':'thingForm', 'runOnReturn':handleSubmitThingResponse});">Add</button>`;
        }
        
        return `${container 
                    ? `<div class="panel form" id="thingForm">` 
                    : ``
                }
                    <div><h3>${heading}</h3></div>
                    ${json.success ? `${datarow.tng_title} ${editMode ? `edited` : `submitted`} successfully` : ``}
                    ${0 in errors ? json.errors[0].map((error)=>`<p>${error}</p>`).join('') : ``}
                    ${'tng_title' in json.errors 
                        ? json.errors.tng_title.map((error)=>`<p class="error">${error}</p>`).join('') 
                        : ``
                    }
                    <div><input type="text" name="tng_title" placeholder="Title" value="${fillInputs ? datarow.tng_title : ``}"></div>
                        
                    ${'tng_description' in errors ? errors.tng_description.map((error)=>`<p class="error">${error}</p>`).join('') : ``}
                    <div><textarea name="tng_description" placeholder="Description">${fillInputs ? datarow.tng_description : ``}</textarea></div>
                    
                    <div class="buttonBar">${buttons}</div>
				${container ? `</div>` : ``}`;
    }
    
    function getDisplayThingHtml(params={}){
        var json = ('json' in params ? params.json : {});
        
        isset(() => arr1.json.datarow.wobble.blah) ? 'true' : 'false';
        var datarow = (isset(() => params.json.datarow) ? params.json.datarow : {});
        datarow = ('datarow' in params ? params.datarow : datarow);
        
        var container = ('container' in params ? params.container : true);
        
        return `${container ? `<div class="panel listItem" id="thing_${datarow.tng_id}">` : ``}
                <div class="titleBar">
                    <h3 id="tng_title_${datarow.tng_id}">${datarow.tng_title}</h3>
                    <h5>${toShortDate(datarow.tng_time_added)}</h5>
                </div>
                <div class="textBlock" id="tng_description_${datarow.tng_id}">${datarow.tng_description}</div>
                <div class="buttonBar">
                    <button onclick="getThingJson({'tng_id':${datarow.tng_id},'runOnReturn':editThing});">edit</button>
                </div>
             ${container ? `</div>` : ``}`;
    }
    
    function addBlankThingFormToWrapperMain(){
        appendToWrapperMain(getThingFormHtml({'container':true,'editMode':false,'fillInputs':false}));
    }
    
    function deleteThing(tng_id=''){
        var f = new FormData();
        f.append('tng_id',`${tng_id}`);
        ajaj({'file':'thing.nav.php?nav=deleteThing', 'f':f, 'runOnReturn':handleDeleteThingResponse});
    }
    
    function handleDeleteThingResponse(params={}){
        var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
        var datarow = ('datarow' in json ? json.datarow : []);
        
        var success = ('success' in json ? json.success : false);
        
        if(success){
            var thingId = `thing_${datarow.tng_id}`;
            var panel = (document.getElementById(thingId) ? document.getElementById(thingId) :'');
            if(panel!=''){panel.remove();}
        }
        /*
        var printTo = ('printTo' in params || params.printTo!=='' ? params.printTo : 'thingForm');
        
        responseHtml = getThingFormHtml({'json':json,'container':false,'fillInputs':fillInputs,'editMode':editMode});
        
        document.getElementById(printTo).innerHTML = responseHtml;
        */
    }
    
    function handleSubmitThingResponse(params={}){
        var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
        var printTo = ('printTo' in params || params.printTo!=='' ? params.printTo : 'thingForm');
		printTo = (typeof(printTo)=='string' ? document.getElementById(printTo) : printTo);

        var fillInputs = (!json.exists && json.datarow.length>0 || json.exists && json.newlyAdded ? false : true);
        var editMode = (!json.exists || json.exists && json.newlyAdded ? false : true);
        
        printTo.innerHTML = 
            getThingFormHtml({'json':json,'container':false,'fillInputs':fillInputs,'editMode':editMode});
        
        if(json.newlyAdded){
            printTo.insertAdjacentHTML('afterend',getDisplayThingHtml({'container':true,'datarow':json.datarow}));
        }
    }
    
    function editThing(params={}){
        var json = initObject('json' in params ? params.json : getDefaultJsonObject());
        var datarow = ('datarow' in json ? json.datarow : {});
        var thingPanel = document.getElementById('thing_' + datarow.tng_id);
        
        thingPanel.classList.add('form');
        thingPanel.innerHTML = getThingFormHtml({'json':json,'container':false,'editMode':true});
        thingPanel.querySelector('input[name="tng_title"]').focus();
    }
    
    function displayThing(params={}){
        var json = initObject('json' in params ? params.json : getDefaultJsonObject());
        var datarow = issetReturn(() => params.json.datarow);
        datarow = isset(() => params.datarow) ? params.datarow : datarow;
        
        
        var thingPanel = document.getElementById('thing_' + datarow.tng_id);
        thingPanel.classList.remove('form');
        
        thingPanel.innerHTML = getDisplayThingHtml({'datarow':datarow,'container':false,'editMode':true});
    }
    
    /*Thing List functions*/
    function handleThingListResponse(params={}){
        var json = ('json' in params ? params.json : {});
        var datarows = ('datarows' in json ? json.datarows : {});
        
        datarows.forEach((datarow)=>{appendToWrapperMain(getDisplayThingHtml({'datarow':datarow}));});
    }
    
    function getThingList(params={}){
        var order = ('order' in params ? params.order : 'tng_time_added');
        var direction = ('direction' in params ? params.direction : 'ASC');
        
        var f = new FormData();
        f.append('order',order);
        f.append('direction',direction);

        ajaj({'file':'thing.nav.php?nav=getThingList','runOnReturn':handleThingListResponse,'formdata':f});
    }
    
