{{Form::open(array('action' => array('FantasyaController@report')))}}
	<?php echo Form::hidden('party', $id) ?>
	<?php echo Form::select('turn', $turns[$id], $turn) ?>
	<?php echo Form::submit('Download') ?>
{{Form::close()}}
