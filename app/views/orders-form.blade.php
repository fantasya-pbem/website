{{ Form::open(array('action' => array('FantasyaController@orders'))) }}
<?php echo Form::select('party', $parties) ?>
<?php echo Form::select('turn', $turns, $turn) ?>
<?php echo Form::submit('Anzeigen') ?>
{{ Form::close() }}
