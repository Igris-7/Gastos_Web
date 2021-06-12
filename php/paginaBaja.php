<?php
/****************
PAGINACION
*****************/
print "<div class='text-left'>";
if ($total_paginas>$PAGINAS_MAXIMAS) {
	//es cuando estamos ubicados en la última página
	if ($pagina==$total_paginas) {
		$inicio = $pagina - $PAGINAS_MAXIMAS;
		$fin = $total_paginas;
	} else {
		# no estamos en la página final
		$inicio = $pagina;
		$fin = ($inicio-1) + $PAGINAS_MAXIMAS;
		if($fin>$total_paginas) $fin = $total_paginas;
	}
	if ($inicio!=1) {
		print "<button type='button' onclick='cambiaPagina(1)'>Primero</button>";
		print "<button type='button' onclick='cambiaPagina(".($pagina-1).")'>Ant.</button>";
	} 
} else {
	$inicio = 1;
	$fin = $total_paginas;
}
//ciclo para desplegar los botones
for ($i=$inicio; $i <= $fin ; $i++) { 
	print '<button type="button" ';
	if($i==$pagina) print "disabled";
	print ' onclick="cambiaPagina('.$i.')">'.$i.'</button>';
}
//Botones de último y siguiente
if ($total_paginas>$PAGINAS_MAXIMAS && $pagina!=$total_paginas) {
	print "<button type='button' onclick='cambiaPagina(".($pagina+1).")'>Sig.</button>";
	print "<button type='button' onclick='cambiaPagina(".$total_paginas.")'>Último</button>";
}
print "</div>";
?>