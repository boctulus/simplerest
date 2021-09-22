<h1>DSI - Test</h1>

<br/>
<img src="<?= assets('img/DSI-Logo.png') ?>" style="height: 100px;" />

<table class="table"  style="margin-top:50px;">
  <thead>
    <tr>
      <th>Prueba</th>
      <th>Fecha</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pruebas as $p): ?>
    <tr>
      <td><?= $p['name']; ?></td>
      <td><?= $p['date']; ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>

