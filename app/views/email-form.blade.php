{{ Form::open(array('action' => 'FantasyaController@profile')) }}
    <?php echo Form::label('email', 'E-Mail-Adresse ändern') ?><br>
    <?php echo Form::text('email') ?>
    <?php echo Form::submit('Speichern') ?>
{{ Form::close() }}

