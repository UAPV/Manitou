<h1>Tableau de bord</h1>

<h2>État des serveurs</h2>
<ul id="image_server_status">
  <?php foreach ($imageServers as $server): ?>
  <li class="image_server">
    <span class="hostname"><?php echo $server->getHostname() ?></span>
    <span class="status" data-href="<?php echo url_for('@image_server_status?id='.$server->getId()) ?>">loading...</span>
    <span class="force_stop"><?php echo link_to('Forcer l\'arrêt', '@image_server_stop?id='.$server->getId()) ?></span>
  </li>
  <?php endforeach ?>
</ul>

<div style='margin-top: 30px;margin-bottom: 30px;'>
    <input class='submitButton' id='svn_status' style="padding: 5px;" type='submit' value='Voir le statut SVN' />

    <div class='box'>
        <div class='box_svn_st' data-href="<?php echo url_for('@command_svn_status') ?>"></div>
    </div>
</div>
<h2>Commandes exécutées</h2>
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

    function updateImageServerStatus () {
      $('li.image_server').each (function (e, server) {
        $.get($('.status', server).data('href'), function (status) {
          console.log (status);
          $('.status', server).text(status);
        });
      });
    }

    function showSvnStatus(e, server){
         $.get($('.box_svn_st', server).data('href'), function (status) {
            console.log (status);
            $( ".box" ).toggle(500);
            $('.box_svn_st', server).html(status);
         });
    }

    $( ".box" ).hide();
    setInterval (updateRunningCommands, 7000);
    setInterval (updateImageServerStatus, 30000);

    updateRunningCommands();
    updateImageServerStatus();

    $('.stop_command').live ('click', function (e) {
      if (confirm ('Êtes vous sûr de vouloir stopper cette commande ?'))
        $.get($(this).attr('href'));

      return e.preventDefault();
    });

    $('#svn_status').live ('click', function (e){
       showSvnStatus();
    });

  });

</script>
