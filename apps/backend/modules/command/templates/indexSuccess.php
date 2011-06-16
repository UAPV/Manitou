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
          newItem = $(data);
          $(command).replaceWith(newItem);
          $('.commandline, .stderr, .stdout', newItem).each (function () {
              $(this).scrollTop ($('pre', this).outerHeight());
          });
        });
      });
    }

    setInterval (updateRunningCommands, 1000);

    $('.stop_command').live ('click', function (e) {
      if (confirm ('Êtes vous sûr de vouloir stopper cette commande ?'))
        $.get($(this).attr('href'));

      return e.preventDefault();
    });

  });

</script>
