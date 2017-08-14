{{Form::open(array('action' => array('FantasyaController@enter')))}}
    <?php echo Form::label('party', 'Name der Partei'); ?><br>
    <?php echo Form::text('party'); ?><span class="error"><?php echo $errors->first('party'); ?></span><br>
    <?php echo Form::label('race', 'Rasse') ?><br>
    <?php echo Form::select('race', $races) ?><span class="error"><?php echo $errors->first('race'); ?></span><br>
    <table>
        <tr>
            <td width="33%"><?php echo Form::label('wood', 'Holz') ?></td>
            <td width="33%"><?php echo Form::label('stone', 'Stein') ?></td>
            <td width="34%"><?php echo Form::label('iron', 'Eisen') ?></td>
        </tr>
        <tr>
            <td><?php echo Form::text('wood') ?><br><span class="error"><?php echo $errors->first('wood'); ?></span></td>
            <td><?php echo Form::text('stone') ?><br><span class="error"><?php echo $errors->first('stone'); ?></span></td>
            <td><?php echo Form::text('iron') ?><br><span class="error"><?php echo $errors->first('iron'); ?></span></td>
        </tr>
    </table>
    <?php echo Form::label('description', 'Kurzbeschreibung') ?><br>
    <?php echo Form::textarea('description') ?><span class="error"><?php echo $errors->first('description'); ?></span><br>
    <?php echo Form::submit('Partei erstellen') ?>
{{Form::close()}}
