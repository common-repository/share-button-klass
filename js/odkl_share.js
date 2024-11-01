if (!window.ODKL) { ODKL = {};}

if (!ODKL.P) {
	ODKL.P = {
		w : 600,
		h : 420,
		l : (screen.width/2)-(300),
		t : (screen.height/2)-(210),
		requests : null,
		share_host : 'www.odnoklassniki.ru'
	};
}

if (!ODKL.Share) {
	ODKL.Share = function(el){
		if (el.tagName.toLowerCase() != "a") {return ;}	
		
		var src=1000;
		var btnType = el.className ? el.className.toLowerCase() : null;
		if(btnType){
	        if(btnType.indexOf("odkl-klass-stat")>-1) src = '1';
    		else
    		    if(btnType.indexOf("odkl-klass")>-1) src = '0';
		}
		
		var url = 'http://'+ODKL.P.share_host+'/dk?st.cmd=addShare&st.s='+src+'&st._surl='+encodeURIComponent(el.href);
		var w=window.open('','odkl_share', 'top='+ODKL.P.t+',left='+ODKL.P.l+',width='+ODKL.P.w+',height='+ODKL.P.h+',resizable=1');
		var t =
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'+
				'<html lang="ru" xml:lang="ru" xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" />'+
				'<title>Одноклассники.ru</title>'+
				'</head><body style="margin:0;padding:0;"><div style="width:100%;padding:17px 0;text-align:center;background-color:#F93;color:white;font:normal 14px/16px verdana">Происходит загрузка...</div>'+
				'</body></html>';

		w.document.write(t);
		w.location.href=url;

		//return false;
	} 
}


/* Stat buttons support */
if (!ODKL.updateCount) {// should be called by script returned from server
    ODKL.updateCount = function(uid,count){
        if(uid == null || count == null) return;
        
        var counter = document.getElementById(uid);
        if(counter == null) return;
        if(isNaN(count) || count <0){// some error
            counter.innerHTML = '0';
        }else{
            counter.innerHTML = count;// this also removes script
        }
    }
}


if (!ODKL.getStatNodes) {// initialize counters
    ODKL.getStatNodes = function(){
        
        if(document.getElementsByClassName)
            return document.getElementsByClassName("odkl-klass-stat");
        
//      MSIE
        if(document.getElementsByTagName){
            var allNodes = document.getElementsByTagName("a");
            
            var nodes = new Array();
            
            for(var i=0; i<allNodes.length;i++){
                var cn = allNodes[i].className;
                if(cn && cn.toLowerCase().indexOf("odkl-klass-stat")>-1){
                    nodes.push(allNodes[i]);
                }
            }
            return nodes;
        }
        
        
        return null;
    }
}

    
if (!ODKL.init) {// initialize counters
    ODKL.init = function(){
        if(ODKL.P.initialized)
            return;// init invoked second time ...
        
        ODKL.P.initialized = true;
        
        var stat_url=  'http://' + ODKL.P.share_host + '/dk?st.cmd=extLike&uid=';
        
        var statAnchors = ODKL.getStatNodes();
        if(statAnchors == null){
            return; // no anchors in a body
        }
        
        ODKL.P.requests = new Array();
        
        for(var i=0;i<statAnchors.length;i++){
            var a = statAnchors[i];
            
            if(a.href == null) continue; // something wierd .. ignore
            
            var children = a.getElementsByTagName("span");
            var counter;
            if(children && children.length>0){
                counter = children[0];
            }else{
                counter= document.createElement("span");
                a.appendChild(counter);
                counter.innerHTML = '0';
            }
            
            counter.id="odklcnt"+i;
            var statRequest = stat_url + counter.id + '&ref=' + encodeURIComponent(a.href);

            var script = document.createElement("script");
            script.type = "text/javascript";
            counter.appendChild(script);
            script.src=statRequest;
        }
    }
}



//if (window.addEventListener) //DOM method for binding an event
//    window.addEventListener("load", ODKL.init, false);
//else if (window.attachEvent) //IE exclusive method for binding an event
//    window.attachEvent("onload", ODKL.init);
//else if (document.getElementById) //support older modern browsers
//    window.onload=ODKL.init;