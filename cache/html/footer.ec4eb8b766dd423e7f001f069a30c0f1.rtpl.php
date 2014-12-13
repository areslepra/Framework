<?php defined('ROOT') or exit(); ?>  <div class="footer">
<?php if( DEV === true ){ ?>
<b><?php echo $lang["dev_time"];?></b>: <?php echo round(microtime(true) - Framework\Core::$initial_data["time"], 4); ?> <b><?php echo $lang["dev_ram"];?></b>: <?php echo roundsize((memory_get_usage() - Framework\Core::$initial_data["ram"])); ?> <b><?php echo $lang["dev_sql"];?></b>: <?php echo Framework\LittleDB::$count; ?>  <b><?php echo $lang["dev_context"];?></b>: <?php echo Framework\Context::$count; ?>
<?php } ?>
</div>
<script src="127.0.0.1/mvclimpio/views\html$2"></script>
</body>
</html>