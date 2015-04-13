{{Form::open(array('action' => 'FantasyaController@login'))}}
	<?php echo Form::label('user', 'Benutzername'); ?><br>
	<?php echo Form::text('user'); ?><br>
	<?php echo Form::label('password', 'Passwort'); ?><br>
	<?php echo Form::password('password'); ?><br>
	<?php echo Form::submit('Anmelden'); ?>
{{Form::close()}}
