<?php $_ = OutlineRuntime::start(__FILE__, isset($this) ? $this : null); ?><article>

	<h1><?php echo Str::decode($data['title']); ?> </h1>
	<?php echo $data['text']; ?>

</article>

<?php $_ = OutlineRuntime::finish(__FILE__); ?>