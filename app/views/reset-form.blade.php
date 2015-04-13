{{Form::open(array('action' => 'FantasyaController@reset'))}}
	<?php echo Form::label('user', 'Benutzername'); ?><br>
	<?php echo Form::text('user'); ?><br>
	<?php echo Form::label('email', 'E-Mail-Adresse'); ?><br>
	<?php echo Form::text('email'); ?><br>
	<?php echo Form::submit('Passwort anfordern'); ?><br>
{{Form::close()}}
