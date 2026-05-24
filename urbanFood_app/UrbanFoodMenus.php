
<div class="table-responsive">

<table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
              <th>Nombre</th>
                <th>Imagen</th>
                  <th>Descripción</th>
                        <th>Precio</th>
                            <th>Categoría</th>
                                <th>Restaurante</th>
                                <th>Edicion</th>
        
        </tr>

            <?php

            while ($registroMenus = $recibeResultadosMenus->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>" . $registroMenus['id_menu'] . "</td>";
                echo "<td>" . $registroMenus['nombre'] . "</td>";
                echo "<td><img src='" . ($registroMenus['imagen'] ? $registroMenus['imagen'] : 'img/default.jpg') . "' width='100'></td>";
                echo "<td>" . $registroMenus['descripcion'] . "</td>";
                echo "<td>" . $registroMenus['precio'] . "</td>";
                echo "<td>" . $registroMenus['categoria'] . "</td>";
                echo "<td>" . $registroMenus['restaurante'] . "</td>";

                 ?>
                 
               
                 <td>
            <a class="btn btn-warning btn-sm" href="?tabla=menus&vista=form&editar=<?= $registroMenus['id_menu'] ?>">Editar</a>

            <a class="btn btn-danger btn-sm" href="?tabla=menus&vista=tabla&eliminar=<?= $registroMenus['id_menu'] ?>"
            onclick="return confirm('¿Desea eliminar el registro?')">Eliminar</a>
                 </td>
           
            <?php 
                echo "</tr>";
             }
            ?>
           
</table>
</div>