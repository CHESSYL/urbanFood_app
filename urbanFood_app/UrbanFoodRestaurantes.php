<div class="table-responsive">
<table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
              <th>Nombre</th>
                <th>Direccion</th>
                  <th>Numero telefonico</th>
                        <th>Correo</th>
                            <th>Imagen</th>
                            <th>Edicion</th>
        
        </tr>
            <?php

        
            while ($registroRestaurantes = $recibeResultados->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>" . $registroRestaurantes['id_restaurante'] . "</td>";
                echo "<td>" . $registroRestaurantes['nombre'] . "</td>";
                echo "<td>" . $registroRestaurantes['direccion'] . "</td>";
                echo "<td>" . $registroRestaurantes['telefono'] . "</td>";
                echo "<td>" . $registroRestaurantes['correo'] . "</td>";  
                echo "<td><img src='".$registroRestaurantes['imagen']."' width='80'></td>";      
    
                 ?>
               
                 <td>
            <a class="btn btn-warning btn-sm" href="?tabla=restaurantes&vista=form&editar=<?= $registroRestaurantes['id_restaurante'] ?>">Editar</a>

            <a class="btn btn-danger btn-sm" href="?tabla=restaurantes&vista=tabla&eliminar=<?= $registroRestaurantes['id_restaurante'] ?>"
            onclick="return confirm('¿Desea eliminar el registro?')">Eliminar</a>

                 </td>
           
            <?php 
                echo "</tr>";
             }
            ?>
           
</table>
</div>