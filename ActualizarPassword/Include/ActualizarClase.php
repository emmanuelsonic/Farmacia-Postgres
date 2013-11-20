<?php

class Cambios{
	function ActualizarPassword($IdPersonal,$Password){
	
	$query="update fos_user_user set password=md5('$Password') where Id='$IdPersonal'";
	pg_query($query);
		
	}
}//
?>
