<?php
if (isset($msg)) {
	for ($i=0; $i < count($msg); $i++) { 
		$cadena = substr($msg[$i],0,1);
		print "<div class='alert ";
		if($cadena=="0") print "alert-success' ";
		if($cadena=="1") print "alert-danger' ";
		if($cadena=="2") print "alert-warning' ";
		print ">";
		print substr($msg[$i],1);
		print "</div>";
	}
}
?>