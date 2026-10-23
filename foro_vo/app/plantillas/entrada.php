<p><?= $msg ?? '' ?></p>
<div>
<form name='entrada' method="POST" action="index.php">
<table>
<tr>
<td>Nombre:</td><td><input type="text" name="nombre" value="<?= isset($_REQUEST['nombre']) ? escaparHtml($_REQUEST['nombre']) : '' ?>" ></td></tr>
<tr>
<td>Contraseña: </td><td><input type="password" name="contraseña" value="<?= $_REQUEST['contraseña'] ?? '' ?>" ></td>
</tr>
</table>
<input type="submit" name="orden" value="Entrar">
</form>
</div>