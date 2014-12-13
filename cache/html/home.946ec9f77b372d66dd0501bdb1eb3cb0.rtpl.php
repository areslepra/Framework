<?php defined('ROOT') or exit(); ?><h4>Mostrando usuarios registrados por orden alfabético</h4>
<table class="table table-striped table-hover">
<thead>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Apellido</th>
<th>Fecha de registro</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
<?php $counter1=-1; if( isset($users) && is_array($users) && sizeof($users) ) foreach( $users as $key1 => $value1 ){ $counter1++; ?>
<tr>
<td><?php echo $value1["id"];?></td>
<td><?php echo $value1["name"];?></td>
<td><?php echo $value1["lastname"];?></td>
<td><?php echo date('h:m - d/m/y', $value1["datetime"]); ?></td>
<td><a href='<?php echo url('home', 'edit', $value1["id"]); ?>'>Editar</a> - <a href='<?php echo url('home', 'delete', $value1["id"]); ?>'>Borrar</a></td>
</tr>
<?php } ?>
<tr class='warning'>
<td colspan='5' style='text-align:center;''>
<a href='<?php echo url('home', 'create'); ?>'>Agregar usuario</a></td>
</tr>
</tbody>
</table>