<div class='captcha'>
	{{Form::open(array('action' => 'FantasyaController@register'))}}
		<?php echo Form::label('user', 'Benutzername'); ?><br>
		<?php echo Form::text('user'); ?><span class="error"><?php echo $errors->first('user'); ?></span><br>
		<?php echo Form::label('email', 'E-Mail-Adresse'); ?><br>
		<?php echo Form::text('email'); ?><span class="error"><?php echo $errors->first('email'); ?></span><br>
		<?php echo Form::label('captcha', 'Bist Du ein Mensch? Dann löse das Captcha!'); ?><br>
		<?php echo HTML::image(Captcha::img(), 'Captcha image'); ?>
		<?php echo Form::text('captcha'); ?><span class="error"><?php echo $errors->first('captcha'); ?></span><br>
		<?php echo Form::submit('Registrieren'); ?><br>
	{{Form::close()}}
</div>