
<div class="table-responsive">

<table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
              <th>Nombre</th>
                  <th>Apellido</th>
                        <th>Telefono</th>

        
        </tr>

            <?php

            while ($registroClientes = $recibeResultados->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>" . $registroClientes['id_cliente'] . "</td>";
                echo "<td>" . $registroClientes['nombre'] . "</td>";
                echo "<td>" . $registroClientes['apellido'] . "</td>";
                echo "<td>" . $registroClientes['telefono'] . "</td>";
                 ?>
           
            <?php 
                echo "</tr>";
             }
            ?>
           
</table>
</div>