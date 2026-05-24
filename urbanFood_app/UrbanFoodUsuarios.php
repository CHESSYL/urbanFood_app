
<div class="table-responsive">

<table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>ID Cliente</th>
            <th>ID Repartidor</th>
        </tr>
            <?php

        
            while ($registroClientes = $recibeResultados->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>" . $registroClientes['id_usuario'] . "</td>";
                echo "<td>" . $registroClientes['correo'] . "</td>";
                echo "<td>" . $registroClientes['rol'] . "</td>";
                echo "<td>" . $registroClientes['id_cliente'] . "</td>";
                echo "<td>" . $registroClientes['id_repartidor'] . "</td>";
              
    
                 ?>  
            <?php 
                echo "</tr>";
             }
            ?>
           
</table>
</div>