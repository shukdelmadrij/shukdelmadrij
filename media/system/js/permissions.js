function sendPermissions(e){var t=document.getElementById("icon_"+this.id);t.removeAttribute("class"),t.setAttribute("style","background: url(../media/system/images/modal/spinner.gif); display: inline-block; width: 16px; height: 16px");var a="not",s=getUrlParam("component"),r=getUrlParam("extension"),o=getUrlParam("option"),n=getUrlParam("view"),i=s,l=this.value;"com_config"==o&&0==s&&0==r?a="root.1":0==r&&"component"==n?a=s:0!=r&&0!=n?(a=r+"."+n+"."+getUrlParam("id"),i=document.getElementById("jform_title").value):0==r&&0!=n&&(a=o+"."+n+"."+getUrlParam("id"),i=document.getElementById("jform_title").value);var m=this.id.replace("jform_rules_",""),d=m.lastIndexOf("_"),c={comp:a,action:m.substring(0,d),rule:m.substring(d+1),value:l,title:i};Joomla.removeMessages(),jQuery.ajax({method:"POST",url:document.getElementById("permissions-sliders").getAttribute("data-ajaxuri"),data:c,datatype:"json"}).fail(function(e,a,s){t.removeAttribute("style"),Joomla.renderMessages(Joomla.ajaxErrorsMessages(e,a,s)),window.scrollTo(0,0),t.setAttribute("class","icon-cancel")}).done(function(a){t.removeAttribute("style"),a.data&&1==a.data.result&&(t.setAttribute("class","icon-save"),jQuery(e.target).parents().next("td").find("span").removeClass().addClass(a.data["class"]).html(a.data.text)),"object"==typeof a.messages&&null!==a.messages&&(Joomla.renderMessages(a.messages),a.data&&1==a.data.result?t.setAttribute("class","icon-save"):t.setAttribute("class","icon-cancel"),window.scrollTo(0,0))})}function getUrlParam(e){for(var t=window.location.search.substring(1),a=t.split("&"),s=0;s<a.length;s++){var r=a[s].split("=");if(r[0]==e)return r[1]}return!1}