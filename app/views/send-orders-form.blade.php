{{Form::open(array('action' => array('FantasyaController@send', 'orders')))}}
	<?php echo Form::label('party', 'Partei') ?><br>
	<?php echo Form::select('party', $parties) ?><br>
	<?php echo Form::label('turn', 'Runde') ?><br>
	<?php echo Form::select('turn', $turns) ?><br>
	<?php echo Form::label('orders', 'Befehle') ?><br>
	<?php echo Form::textarea('orders') ?><br>
	<?php echo Form::submit('Befehle senden') ?>
{{Form::close()}}

