
<div class="table-responsive">

<table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
              <th>Nombre</th>
                  <th>Numero telefonico</th>
                        <th>Vehículo</th>
                            <th>Estado</th>
                                <th>Edicion</th>
        
        </tr>
            <?php

        
            while ($registroRepartidores = $recibeResultados->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>" . $registroRepartidores['id_repartidor'] . "</td>";
                echo "<td>" . $registroRepartidores['nombre'] . "</td>";
                echo "<td>" . $registroRepartidores['telefono'] . "</td>";
                echo "<td>" . $registroRepartidores['vehiculo'] . "</td>";     
                echo  "<td>" . $registroRepartidores['estado'] . "</td>"; 
    
                 ?>
                 
               
                 <td>
            <a class="btn btn-warning btn-sm" href="?tabla=repartidores&vista=form&editar=<?= $registroRepartidores['id_repartidor'] ?>">Editar</a>

                 </td>
           
            <?php 
                echo "</tr>";
             }
            ?>
           
</table>
</div>