    
<div class="table-responsive">
<table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
              <th>Cliente</th>
                  <th>Fecha</th>
                    <th>Total</th>
                      <th>Estado</th>
                        <th>Repartidor</th>
        
        </tr>
            <?php

        
            while ($registroPedidos = $recibeResultadosPedidos->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>" . $registroPedidos['id_pedido'] . "</td>";
                echo "<td>" . $registroPedidos['cliente'] . "</td>";
                echo "<td>" . $registroPedidos['fecha'] . "</td>";
                echo "<td>" . $registroPedidos['total'] . "</td>";
                echo "<td>" . $registroPedidos['estado'] . "</td>";
                echo "<td>" . $registroPedidos['repartidor'] . "</td>";

                 ?>
           
            <?php 
                echo "</tr>";
             }
            ?>
           
</table>
</div>