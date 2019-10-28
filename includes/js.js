    /*Generic functions - used on all pages*/
	function getElementValues(params={}){
		var f = ('f' in params && params.f!='' ? params.f : new FormData);
        var getValuesFrom = initElement('getValuesFrom' in params ? params.getValuesFrom : '');
		
		if(getValuesFrom!=='' && getValuesFrom!==undefined && getValuesFrom!==null){
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
	
    function initElement(element=''){
        return element.nodeName==undefined ? document.getElementById(element) : element;
    }
    
    function initObject(object){
        return (isJsonString(object) ? JSON.parse(object) : object);
    }
    
    function isJsonString(str) {
        try {JSON.parse(str);return true;} catch(e) {return false;}
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
    }
    
    function toShortDate(timestamp){
        let t = new Date(0);
        t.setSeconds(timestamp);
        
        let formatted = t.toLocaleTimeString('en-GB');

        return formatted;
    }
    
    function appendToWrapperMain(html){
        document.getElementById('wrapperMain').insertAdjacentHTML('beforeend',html);
    }
    
    function appendAboveNthPanelInWrapperMain(params={}){
        let html = params.html;
        let allPanels = document.getElementById('wrapperMain').querySelectorAll('.panel');
        
        let position = isset(()=>params.position) ? params.position : 0;
        position = position=='' || position=='first' ? 0 : position;
        position = position>=allPanels.length ? 'last' : position;
        
        if(position=='last'){appendToWrapperMain(html);}
        else{allPanels[position].insertAdjacentHTML('beforeBegin',html);}
    }
    
    function toggleResponseLog(){
        if(!document.getElementById('responseLog')){createResponseLog();}
        document.getElementById('responseLog').classList.toggle("hidden");
    }
    
    function isset(array){
        //~ TO USE: isset(() => arr1.json.datarow.wobble.blah) ? 'true' : 'false';
        try{return typeof array() !== 'undefined'}
        catch (e){return false;}
    }
    
    function issetReturn(array){
        //~ TO USE: isset(() => arr1.json.datarow.wobble.blah);
        //~ CANNOT USE isset as a replacement for the next 3 lines of code - WHY??? WHO CARES!!!
        let tof;
        try {tof = typeof array()!=='undefined';}
        catch (e){tof = false;}
        
        return tof ? array() : '';
    }
    
    function goto(file){
        window.location.href = file;
    }
    
	//~ ************ Login Functions ***************/
	function getLoginHtml(params={}){
		/*
        */
        var json = ('json' in params ? params.json : getDefaultJsonString());
		json = (typeof(json)=='object' ? json : JSON.parse(json));
		
		var container = ('container' in params ? params.container : true);
		var useGet = ('useGet' in params ? params.useGet : false);
		
		if(useGet){
			json = getJsonFromGetArray();
		}
		
		return `${container ? '<div class="panel singleColumn"><h1>Log In</h1><div class="form singleColumn" id="loginForm">' : ''}
					${0 in json.errors ? json.errors[0].map((error)=>`<p>${error}</p>`).join('') : ``}
					${'usr_email' in json.errors ? json.errors.usr_email.map((error)=>`<p>${error}</p>`).join('') : ``}
					<input type="text" name="usr_email" placeholder="Email" value="${'usr_email' in json.datarow ? json.datarow.usr_email : ''}">
					
					${typeof(json.errors.usr_password)!=='undefined'? json.errors.usr_password.map((error)=>`<p>${error}</p>`).join(''): ``}
					<input type="password" name="usr_password" placeholder="Password" value="${'usr_password' in json.datarow ? json.datarow.usr_password : ''}">
					
					<div><button name="submitLogin" onclick="submitLogin();">Log In!</button></div>
				${container ? '</div></div>' : ''}`;
	}
	
    async function submitLogin(){
		let currentFile = getCurrentFilename();
        
        let file = 'login.nav.php?nav=submitLogin';
        let f = getElementValues({'getValuesFrom':'loginForm'});
        
        let response = await ajax({'file':file,'f':f});
        json = initObject(response);
        console.log(json);
		
		let datarow = json.datarow;
		let success = (!json.valid || !json.exists ? false : true);
		let printTo = initElement('loginForm');
        
        if(success){goto('index.php');}
        else{printTo.innerHTML = getLoginHtml({'json':json,'container':false});}
        
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
	
    //~ *********** Ajax Functions ************* //
    function ajax(params={}) {
        let file = ('file' in params ? params.file : ''); //~ !essential parameter!
        let f = ('f' in params ? params.f : new FormData());
        let nav = ('nav' in params ? params.nav : ''); //~ !pass in file or essential parameter!
        
        if(nav!=''){f.append('nav',nav);}
        
        const request = new XMLHttpRequest();
        return new Promise((resolve, reject) => {
            request.open("POST", file);
            request.onload = (()=>{
                if (request.status == 200){resolve(request.response);} 
                else {reject(Error(request.statusText));}
            });
            request.onerror = (()=>{reject(Error("Network Error"));});
            request.send(f);
        });
    }
    
    async function ajaxTarget(params={}){
        let file = ('file' in params ? params.file : ''); //~ !essential parameter!
        let f = ('f' in params ? params.f : new FormData());
        let getValuesFrom = ('getValuesFrom' in params ? params.getValuesFrom : ''); //~ getValuesFrom can pass idString or elm 
        
        f = getElementValues({'f':f,'getValuesFrom':getValuesFrom});
        let response = await ajax({'file':file,'f':f});
        return response;
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
		
		return `${container ? '<div class="panel"><h1>Sign Up</h1><div class="form singleColumn" id="signupForm">' : ''}
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
		
					<div><button name="submitSignup" onclick="submitSignup();" >Sign Up!</button></div>
				${container ? '</div></div>' : ''}`;
		
	}
    
    async function submitSignup(){
        let response = await ajaxTarget({'file':'signup.nav.php?nav=submitSignup', 'getValuesFrom':'signupForm'});
        let json = initObject(response);
        
		var success = (!json.valid || !json.exists ? false : true);
		
		if(!success){
			document.getElementById('signupForm').innerHTML = getSignupHtml({'json':json,'container':false});
			return;
		} else {
			window.location.href = `login.php?json=${encodeURI(JSON.stringify(json))}`;
		}
    }
	
	
	function handleLogoutResponse(params={}){
		window.location.href = 'index.php';
	}
	
    //~ ******* Thing List Functions ********** //
    async function getThingList(params={}){
        let order = ('order' in params ? params.order : 'tng_time_added');
        let direction = ('direction' in params ? params.direction : 'ASC');
        
        let f = new FormData();
        f.append('order',order);
        f.append('direction',direction);
        
        let response = await ajax({'file':'thing.nav.php?nav=getThingList','f':f});
        let json = initObject(response);
        return json;
        //~ handleThingListResponse({'json':json});
    }
    
    async function showThingListPanels(){
        let thingList = await getThingList();
        let things = thingList.objects;
        
        things.forEach((thingJson)=>showThingDisplayPanel(thingJson));
    }
    
    
    //~ ******* Thing Functions ********** //
    function showBlankThingEditPanel(){
        showThingEditPanel();
    }
    
    async function showThingEditPanel(thingJson={}){
        thingJson = await initThingJson(thingJson);
        
        let tng = new thing(thingJson);
        tng.showEditPanel();
    }
    
    async function showThingDisplayPanel(thingJson={}){
        thingJson = await initThingJson(thingJson);
        
        let tng = new thing(thingJson);
        tng.showDisplayPanel();
    }
    
    async function getThingJson(tng_id=''){
        var f = new FormData(); 
        f.append('tng_id',tng_id);
        
        return await ajax({'file':'thing.nav.php?nav=getThingJson','f':f});
    }
    
    async function switchThingDisplayPanelToEdit(thingJson={}){
        thingJson = await initThingJson(thingJson);
        
        let tng = new thing(thingJson);
        tng.showEditPanel();
    }
    
    async function switchThingEditPanelToDisplay(thingJson={}){
        thingJson = await initThingJson(thingJson);
        
        let tng = new thing(thingJson);
        tng.showDisplayPanel();
    }
    
    async function initThingJson(thingJson){
        //~ pass a tng_id or an object
        if(typeof(thingJson)!='object'){thingJson = await getThingJson(thingJson);}
        thingJson = thingJson=='' ? {} : thingJson;
        return thingJson;
    }
    
    async function deleteThing(tng_id=''){
        var f = new FormData();
        f.append('tng_id',`${tng_id}`);
        
        let response = await ajax({'file':'thing.nav.php?nav=deleteThing', 'f':f});
        let json = initObject(response);
        if(json.success){
            let tng = new thing(json);
            tng.removePanel();
        }
    }
    
    async function saveThing(tng_id=''){
        var f = new FormData();
        f.append('tng_id',`${tng_id}`);
        
        let thingPanelId = `thingPanel${tng_id==`` ? `` : `_${tng_id}`}`;
        
        let response = await ajaxTarget({'file':'thing.nav.php?nav=saveThing', 'f':f, 'getValuesFrom':thingPanelId});
        let json = initObject(response);
        if(json.newlyAdded){
            let tng = new thing(json);
            tng.buildPanel(1);
            tng.showDisplayPanel();
            
            let tng2 = new thing();
            tng2.showEditPanel();
        }
    }
    
    
    class thing {
        
        constructor(object={}) {
            this.exists = false;
            this.newlyAdded = false;
            this.valid = false;
            this.success = false;
            
            this.datarow = {};
            this.errors = {};
            this.labelrow = {};
            this.table = {'name':'ont_things','label':'thing','primarykey':'tng_id','userkey':'tng_usr_id'};
            
            this.panel = null;
            this.init(object);
        }
        
        init(object={}){
            object = initObject(object);
            let json = isset(()=>object.json) ? object.json : object;
            
            this.exists = isset(()=>json.exists) ? json.exists : this.exists;
            this.newlyAdded = isset(()=>json.newlyAdded) ? json.newlyAdded : this.newlyAdded;
            this.valid = isset(()=>json.valid) ? json.valid : this.valid;
            this.success = isset(()=>json.success) ? json.success : this.success;
            
            
            this.datarow = isset(()=>json.datarow) ? json.datarow : this.datarow;
            this.datarow = Object.keys(this.datarow).length==0 ? this.getEmptyDatarow() : this.datarow;
            
            this.labelrow = isset(()=>json.labelrow) ? json.labelrow : this.labelrow;
            
            this.errors = isset(()=>json.errors) ? json.errors : this.errors;
            this.populateErrors();
            
            //~ var fillInputs = (!json.exists && json.datarow.length>0 || json.exists && json.newlyAdded ? false : true);
            //~ var editMode = (!json.exists || json.exists && json.newlyAdded ? false : true);
            
            this.updatePanel();
        }
        
        populateErrors(){
            //~ requires populated datarow to work
            let datarowKeys = Object.keys(this.datarow);
            let errorKeys = datarowKeys.concat([0]);

            errorKeys.forEach((key) => this.errors[key]=(isset(()=>this.errors[key]) ? this.errors[key] : []));
        }
        
        getEmptyDatarow(){
            //~ echoed with php to window.thingDatarow;
            return emptyDatarows[this.table.label];
        }
        
        getEditPanelHtml(){
            let heading = 
                this.exists ? `Edit ${this.datarow.tng_title}`:`What are you grateful for today?`;
                
            let buttons = this.exists   
                ?  `<button name="displayThing" onclick="showThingDisplayPanel(${this.datarow.tng_id})">Back</button> 
                    <button name="saveThing" onclick="saveThing(${this.datarow.tng_id});">Save</button> 
                    <button name="deleteThing" onclick="deleteThing(${this.datarow.tng_id});">Delete</button>`
                
                :   `<button name="submitThing" onclick="saveThing();">Add</button>`;
            
            
            return `<div><h3>${heading}</h3></div>
                    ${this.success ? `${datarow.tng_title} ${editMode ? `edited` : `submitted`} successfully` : ``}
                    ${this.errors[0].map((error)=>`<p>${error}</p>`).join('')}
                    ${this.errors['tng_title'].map((error)=>`<p>${error}</p>`).join('')}
                    <div><input type="text" name="tng_title" placeholder="Title" value="${this.datarow.tng_title}"></div>
                        
                    ${this.errors['tng_description'].map((error)=>`<p>${error}</p>`).join('')}
                    <div><textarea name="tng_description" placeholder="Description">${this.datarow.tng_description}</textarea></div>
                    
                    <div class="buttonBar">${buttons}</div>`;
        }
        
        showEditPanel(){
            this.buildPanel();
            this.panel.innerHTML = this.getEditPanelHtml();
            this.panel.classList = 'panel editMode form';
            this.panel.querySelector('input,textarea').focus();
        }
        
        getDisplayPanelHtml(){
            return `<div class="titleBar">
                        <h3 id="tng_title_${this.datarow.tng_id}">${this.datarow.tng_title}</h3>
                        <h5>${toShortDate(this.datarow.tng_time_added)}</h5>
                    </div>
                    <div class="textBlock">${this.datarow.tng_description}</div>
                    <div class="buttonBar">
                        <button onclick="showThingEditPanel(${this.datarow.tng_id});">edit</button>
                    </div>`;
                    
        }
        
        showDisplayPanel(){
            this.buildPanel();
            this.panel.innerHTML = this.getDisplayPanelHtml();
            this.panel.classList = 'panel displayMode';
        }
        
        getPanelId(){
            let id = `${this.table.label}Panel${this.datarow.tng_id==`` ? `` : `_${this.datarow.tng_id}`}`;
            return id;
        }
        
        getPanel(){
            return initElement(`${this.getPanelId()}`);
        }
        
        updatePanel(){
            this.panel = this.getPanel();
            
            return this.panel;
        }
        
        getPanelMode(){
            this.updatePanel();
            if(this.panel!=null){
                if(this.panel.classList.contains('editMode')){return 'edit';}
                else if(this.panel.classList.contains('displayMode')){return 'display';}
            }
            return '';
        }
        
        buildPanel(position=''){
            if(this.panel==null){
                let html = `<div class="panel" id="${this.getPanelId()}"></div>`
                if(position==''){
                    appendToWrapperMain(`<div class="panel" id="${this.getPanelId()}"></div>`);
                }else{
                    appendAboveNthPanelInWrapperMain({'html':html,'position':position});
                }
            }
            this.updatePanel();
        }
        
        removePanel(){
            if(this.panel!=null){
                this.panel.remove()
            }
            this.updatePanel();
        }
    }

/*
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
    */
    
