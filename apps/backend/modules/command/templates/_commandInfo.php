  <li class="<?php echo $command->hasErrors() ? 'has_errors' : '' ?> <?php echo $command->isRunning() ? 'is_running' : '' ?>"
      data-command-id="<?php echo $command->getId() ?>">
    <p class="process_status">
      <?php if ($command->isRunning ()): ?>
        En cours depuis <?php echo ($min = floor ($command->getDuration() / 60)) > 0 ? ($min.'min') : '' ?> <?php echo $command->getDuration() % 60 ?>sec 

        <a href="#">Arrêter</a>
      <?php elseif ($command->isFinished ()): ?>
        Terminée à <?php echo $command->getFinishedAt ('H:i:s') ?> [CODE <?php echo $command->getReturnCode() ?>]
      <?php elseif ($command->isStopped ()): ?>
        Stoppée
       <?php endif ?>
    </p>
    <p class="process_info">
      <span class="owner"><?php echo $command->getUserId() ?></span>
      <span class="started_at">le <?php echo $command->getStartedAt('d-m-Y à H:i:s') ?></span>
    </p>
    <pre class="commandline"><?php echo $command->getCommand () ?></pre>
    <?php if (strlen ($command->getStdErr ())): ?>
      <div class="stderr"     ><pre><?php echo $command->getStdErr ()  ?></pre></div>
    <?php endif ?>
    <?php if (strlen ($command->getStdOut ())): ?>
      <div class="stdout"     ><pre><?php echo $command->getStdOut ()  ?></pre></div>
    <?php endif ?>
  </li>
