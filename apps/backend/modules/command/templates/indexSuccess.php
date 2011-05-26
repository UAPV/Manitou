<h1>Commands List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Created at</th>
      <th>Updated at</th>
      <th>Command</th>
      <th>Std out file</th>
      <th>Std err file</th>
      <th>Std out</th>
      <th>Std err</th>
      <th>Return code</th>
      <th>User</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($Commands as $Command): ?>
    <tr>
      <td><a href="<?php echo url_for('command/show?id='.$Command->getId()) ?>"><?php echo $Command->getId() ?></a></td>
      <td><?php echo $Command->getCreatedAt() ?></td>
      <td><?php echo $Command->getUpdatedAt() ?></td>
      <td><?php echo $Command->getCommand() ?></td>
      <td><?php echo $Command->getStdOutFile() ?></td>
      <td><?php echo $Command->getStdErrFile() ?></td>
      <td><?php echo $Command->getStdOut() ?></td>
      <td><?php echo $Command->getStdErr() ?></td>
      <td><?php echo $Command->getReturnCode() ?></td>
      <td><?php echo $Command->getUserId() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('command/new') ?>">New</a>
