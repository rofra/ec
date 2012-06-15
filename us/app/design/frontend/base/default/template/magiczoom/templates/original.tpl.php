<?php
$m = intval(self::$options->getValue('selectors-margin'));
$wm = intval(self::$options->getValue('thumb-max-width'));
?>

ee<!-- Begin magiczoom -->
<div class="MagicToolboxContainer" style="width: <?php echo $wm?>px;">
    <?php if($main) echo $main; ?>

    <?php if(isset($message)):?>
        <div class="MagicToolboxMessage"><?php echo $message?></div>
    <?php endif?>

    <?php if(count($thumbs)):?>
    <div id="MagicToolboxSelectors<?php echo $pid?>" class="more-views MagicToolboxSelectorsContainer" style="margin-top: <?php echo $m;?>px">
        <h4><?php echo $moviews ?></h4>
        <ul>
        <?php foreach($thumbs as $thumb):?>
            <li><?php echo $thumb?></li>
        <?php endforeach?>
        </ul>
    </div>
    <?php endif?>
</div>
<!-- End  -->
