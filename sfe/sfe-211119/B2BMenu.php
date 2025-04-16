<script type="text/javascript"><!--//--><![CDATA[//><!--

startList = function() {
	if (document.all&&document.getElementById) {
		navRoot = document.getElementById("nav");
		for (i=0; i<navRoot.childNodes.length; i++) {
			node = navRoot.childNodes[i];
			if (node.nodeName=="LI") {
				node.onmouseover=function() {
					this.className+=" over";
				}
				node.onmouseout=function() {
					this.className=this.className.replace(" over", "");
				}
			}
		}
	}
}
window.onload=startList;

//--><!]]></script>
<div id="menu">
<script type="text/javascript">
            var char34 = String.fromCharCode( 34); //This gives me quotes!
           </script>
<ul id="nav">
	<li CLASS="wider">
	  <div><a href="B2BJump.php">Stock&nbsp;Search</a></div>
	</li>
	<li CLASS="wider">
	  <div><a href="B2BViewBasket.php">Basket</a></div>
	</li>
	<li CLASS="wider">
	  <div><a href="B2BOrderHist.php">Your&nbsp;Orders</a></div>
	</li>
	<li CLASS="wider">
	  <div><a href="B2BAccount.php">Your&nbsp;Account</a></div>
	</li>
	<li CLASS="wider">
          <div><a href="logout.php">Logout</a></div>
    	</li>
    	<li CLASS="highlight">
 	  <div>* Specials *</div>
    	</li>
</ul>
<br clear=left>
</div><!-- /menu -->
