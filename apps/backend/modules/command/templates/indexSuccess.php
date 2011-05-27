<h1>Commandes exécutées</h1>
<ul id="command_list">
  <?php foreach ($commands as $command): ?>
    <?php include_partial('commandInfo', array('command' => $command)) ?>
  <?php endforeach; ?>
</ul>

<script type="text/javascript">

  $(document).ready (function () {

    function updateRunningCommands () {
      $('#command_list li.is_running').each (function (e, command) {
        $.get ('<?php echo url_for('@command_list') ?>/'+$(command).data('commandId'), function (data) {
          $(command).replaceWith(data);
        });
      });
    }

    setInterval (updateRunningCommands, 2000);

  });

</script>