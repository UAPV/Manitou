<?php slot ('email_subject', "Modification d'un host") ?>

<div style="background: #FBF8EB; border: 1px solid #EFEBDD; padding: 20px;">
    <div>
        <p>Bonjour,</p>
        <p>
            Le serveur DNS a été modifié par Manitou et un des hosts a été modifié automatiquement.
            Adresse ip : <?php echo $entry['ip'] ?>, nom : <?php echo $entry['fqdn'] ?> ).<br />
        </p>
    </div>
</div>