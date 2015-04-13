{{Form::open(array('action' => 'FantasyaController@profile'))}}
	<?php echo Form::label('password', 'Neues Passwort') ?><br>
	<?php echo Form::password('password') ?>
	<?php echo Form::submit('Speichern') ?>
{{Form::close()}}
