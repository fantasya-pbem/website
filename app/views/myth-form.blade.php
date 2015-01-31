<div id='myth-form'>
    {{ Form::open(array('action' => 'FantasyaController@myth')) }}
    <?php echo Form::label('mythtext', '...das da lautet:'); ?><br>
    <?php echo Form::text('mythtext', $mythtext); ?><br>
    <?php echo Form::label('captcha', 'Bist Du ein Mensch? Dann löse das Captcha!'); ?><br>
    <?php echo HTML::image(Captcha::img(), 'Captcha image'); ?>
    <?php echo Form::text('captcha'); ?><br>
    <?php echo Form::submit('Los'); ?>
    {{ Form::close() }}
</div>