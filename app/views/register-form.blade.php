<div class='captcha'>
	{{Form::open(array('action' => 'FantasyaController@register'))}}
		<?php echo Form::label('user', 'Benutzername'); ?><br>
		<?php echo Form::text('user'); ?><span class="error"><?php echo $errors->first('user'); ?></span><br>
		<?php echo Form::label('email', 'E-Mail-Adresse'); ?><br>
		<?php echo Form::text('email'); ?><span class="error"><?php echo $errors->first('email'); ?></span><br>
		<br>
		<?php echo Form::label('captcha', 'Anti-Spam: ' . $captcha); ?><br>
		<?php echo Form::text('captcha'); ?><span class="error"><?php echo $errors->first('captcha'); ?></span><br>
		<br>
		<p>Die Antwort auf die Anti-Spam-Frage findest Du in der Taverne.</p>
		<?php echo Form::submit('Registrieren'); ?><br>
	{{Form::close()}}
</div>