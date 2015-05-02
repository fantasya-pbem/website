{{Form::open(array('action' => array('FantasyaController@enter')))}}
    <?php echo Form::label('game', 'Spiel') ?><br>
    <?php echo Form::select('game', $games) ?><br>
    <?php echo Form::label('party', 'Name der Partei'); ?><br>
    <?php echo Form::text('party'); ?><br>
    <?php echo Form::label('race', 'Rasse') ?><br>
    <?php echo Form::select('race', $races) ?><br>
    <table>
        <tr>
            <td width="33%"><?php echo Form::label('wood', 'Holz') ?></td>
            <td width="33%"><?php echo Form::label('stone', 'Stein') ?></td>
            <td width="34%"><?php echo Form::label('iron', 'Eisen') ?></td>
        </tr>
        <tr>
            <td><?php echo Form::text('wood') ?></td>
            <td><?php echo Form::text('stone') ?></td>
            <td><?php echo Form::text('iron') ?></td>
        </tr>
    </table>
    <?php echo Form::label('description', 'Kurzbeschreibung') ?><br>
    <?php echo Form::textarea('description') ?><br>
    <?php echo Form::submit('Partei erstellen') ?>
{{Form::close()}}
