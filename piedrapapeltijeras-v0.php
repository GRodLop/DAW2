<?php
// JUEGO DE PIEDRA, PAPEL Y TIJERAS VERSION INCOMPLETA
//-------------------------------
// Caracteres asociadas a las logos 
// PIEDRA (Puño derecho)
// PIEDRA2 (Puño Izquierdo / jugador 2 ) 
define ('PIEDRA',   "&#x1F91C;");
define ('PIEDRA2',  "&#x1F91B;");
define ('TIJERAS',  "&#x1F596;");
define ('PAPEL',    "&#x1F91A;" );

// Tabla de mensajes en función del ganador
$tmsg = [
          "¡Empate !",
          " Ha ganado el jugador 1",
          " Ha ganado el jugador 2"
        ];


/**
 *  Calcula el ganador 
 *  Parámetros: Dos valores PIEDRA, PAPEL O TIJERA
 *  Resultado: 0 (Empate),1 (1 Gana jugador 1), 2 (Gana jugador 2)   
 *  
 */

function calcularGanador (String $valor1, String $valor2): int{
    // COMPLETAR
    $resultado = 0;
    if ($valor1 == $valor2) $resultado= 0;
    elseif ($valor1 == PIEDRA && $valor2 == TIJERAS) $resultado = 1;
    elseif ($valor1 == TIJERAS && $valor2 == PAPEL) $resultado = 1;
    elseif ($valor1 == PAPEL && $valor2 == PIEDRA) $resultado = 1;
    elseif ($valor1 == TIJERAS && $valor2 == PIEDRA) $resultado = 2; 
    elseif ($valor1 == PAPEL && $valor2 == TIJERAS) $resultado = 2;
    elseif ($valor1 == PIEDRA && $valor2 == PAPEL) $resultado = 2;
    return $resultado;
}
/**
 *  Obtiene un valor aleatorio PIEDRA, PAPEL O TIJERAS
 * @return string
 */
function obtenerFicha (): string {
   // COMPLETAR
   $ficha=random_int(0,2);
   
    if ($ficha == 0) return PIEDRA;
    else if ($ficha == 1) return TIJERAS;
    else if ($ficha == 2) return PAPEL;
   return "";
  }


$jugador1 = obtenerFicha();
$jugador2 = obtenerFicha();
$pos = calcularGanador($jugador1,$jugador2);
$mensaje =  $tmsg[$pos]; 

// Si el jugador 2 saca piedra, se cambia el símbolo para que sea el puño izquierdo
$jugador2 = ($jugador2 == PIEDRA)?PIEDRA2:$jugador2;

?>

<html>
<head>
<title>Online PHP Script Execution</title>
</head>
<body>
<h1>¡Piedra, papel, tijera!</h1>

    <p>Actualice la página para mostrar otra partida.</p>

    <table>
      <tr>
        <th>Jugador 1</th>
        <th>Jugador 2</th>
      </tr>
      <tr>
        <td><span style="font-size: 7rem"><?= $jugador1; ?></span></td>
        <td><span style="font-size: 7rem"><?= $jugador2; ?></span></td>
      </tr>
      <tr>
        <th colspan="2"><?= $mensaje ?></th>
      </tr>
    </table>
</body>
</html>