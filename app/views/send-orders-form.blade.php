{{ Form::open(array('action' => array('FantasyaController@send', 'orders'))) }}
<?php echo Form::label('orders', 'Befehle') ?><br>
<?php echo Form::textarea('orders') ?><br>
<?php echo Form::submit('Befehle senden') ?>
{{ Form::close() }}

