{{ Form::open(array('action' => array('FantasyaController@edit', 'news#list'))) }}
    <?php echo Form::label('title', 'Titel') ?><br>
    <?php echo Form::text('title') ?><br>
    <?php echo Form::label('content', 'Text (HTML erlaubt)') ?><br>
    <?php echo Form::textarea('content') ?><br>
    <?php echo Form::submit('News erstellen') ?>
{{ Form::close() }}

