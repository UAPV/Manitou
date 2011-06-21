  <li class="<?php echo $command->hasErrors() ? 'has_errors' : '' ?> <?php echo $command->isRunning() ? 'is_running' : ($command->isStopped() ? 'is_stopped' : 'is_finished') ?>"
      data-command-id="<?php echo $command->getId() ?>">
    <p class="process_status">
      (<?php echo ($min = floor ($command->getDuration() / 60)) > 0 ? ($min.'min ') : '' ?><?php echo $command->getDuration() % 60 ?>sec)
      <?php if ($command->isRunning ()): ?>
        <span class="pid">[PID <?php echo $command->getPid() ?>]</span>
        <a href="<?php echo url_for('@command_stop?id='.$command->getId()) ?>" class="stop_command">Arrêter</a>
      <?php elseif ($command->isStopped ()): ?>
        <span class="status">Stoppée</span> à <?php echo $command->getFinishedAt ('H:i:s') ?>
      <?php elseif ($command->isFinished ()): ?>
        <span class="status">Terminée</span> à <?php echo $command->getFinishedAt ('H:i:s') ?> <span class="return_code">[CODE <?php echo $command->getReturnCode() ?>]</span>
       <?php endif ?>
    </p>
    <p class="process_info">
      <span class="owner"><?php echo $command->getUserId() ?></span> :
      <span class="label"> <?php echo $command->getLabel() ?></span>
      <span class="started_at">- le <?php echo $command->getStartedAt('d-m-Y à H:i:s') ?></span>
    </p>
    <pre class="commandline"><?php echo $command->getCommand () ?></pre>
    <?php if (strlen ($command->getStdErr ())): ?>
      <div class="stderr"     ><pre><?php echo $command->getStdErr ()  ?></pre></div>
    <?php endif ?>
    <?php if (strlen ($command->getStdOut ())): ?>
      <div class="stdout"     ><pre><?php echo $command->getStdOut ()  ?></pre></div>
    <?php endif ?>
  </li>
