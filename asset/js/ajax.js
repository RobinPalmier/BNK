for(var croixEvn=document.querySelectorAll('.croix-e'),u=0;u<croixEvn.length;u++),croixEvn[u].onclick=function(a){a.preventDefault();var b=this.getAttribute('suppr');ajax(b)};function ajax(a){var b;b=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject('Microsoft.XMLHTTP'),b.onreadystatechange=function(){4==b.readyState&&200==b.status&&(document.getElementById('contenaire-event').innerHTML=this.response)},b.open('GET','ajax/traitement-ajax.php?action=suppr&event_id='+a,!0),b.send()}