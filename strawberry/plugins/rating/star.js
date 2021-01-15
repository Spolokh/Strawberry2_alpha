/*******************************************************************************
* Author: Justin Barlow - www.netlobo.com
*******************************************************************************/
function ajaxUpdateRating(elemid, url, options){
  var params = options.params || "";
  var meth = options.meth || "post";
  var async = options.async || true;
  var startfunc = options.startfunc || "";
  var endfunc = options.endfunc || "";
  var errorfunc = options.errorfunc || "";
  var req = false;
  if( window.XMLHttpRequest )
	req = new XMLHttpRequest();
    else if( window.ActiveXObject )
	req = new ActiveXObject( "Microsoft.XMLHTTP" );
    else {
	alert( "Your browser cannot perform the requested action. "+
		   "Either your security settings are too high or your "+
		   "browser is outdated. Try the newest version of "+
		   "Internet Explorer or Mozilla Firefox." );
	       return false;
  }
  if( startfunc != "" )
	eval( startfunc );
    req.open( meth, url+( params != "" ? "?"+params : "" ), async );
    req.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
    req.onreadystatechange = function() {
	  if ( req.readyState == 4 ){
			if ( req.status == 200 ){
		      $(elemid).innerHTML = req.responseText;
		    if( endfunc != "" )
			 eval( endfunc );
		     return true;
			} else {
		  if( endfunc != "" )
			eval( endfunc );
		  if( errorfunc != "" )
			eval( errorfunc );
		  return false;
			}
		}
	};
	req.send(null);
}

function rate_it(news_id, rating, path, stars, stars_size, ip){
	
	var days = 365;
	var date = new Date();
	date.setTime(date.getTime()+(days*24*60*60*1000));
	var expires = "; expires="+date.toGMTString();
	document.cookie = "rating"+news_id+"="+news_id+expires+"; path=/";
	
	ajaxUpdateRating( "ratingform"+news_id, path+"rating.php", {params:"id=" + news_id + "&rating=" + rating + "&stars=" + stars + "&stars_size=" + stars_size + "&ip=" + ip, startfunc:"show_message("+news_id+")", endfunc:"hide_message("+news_id+"); update_middle_total('"+news_id+"', '"+path+"')"});
	

}

function update_middle_total(news_id, path){
	if($('rating_middle'+news_id)){
		ajaxUpdateRating("rating_middle"+news_id, path+"ratingmiddle.php", {params:"id="+news_id+"&what=middle"});
	}
	if($('rating_total'+news_id)){
		ajaxUpdateRating("rating_total"+news_id, path+"ratingmiddle.php", {params:"id="+news_id+"&what=total"});
	}
}

function show_message(news_id){
	$('message'+news_id).innerHTML = "Please wait...";
}

function hide_message(news_id){
	$('message'+news_id).innerHTML = "Thanks for voting.";
}