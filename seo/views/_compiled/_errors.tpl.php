<?php $_ = OutlineRuntime::start(__FILE__, isset($this) ? $this : null); ?><section class="rel w-960 mlr-auto tL">
<?php if (!empty($errors)) { ?>
    <?php foreach ($errors as $key => $error) { ?>
        <div class="rel error tL rnd-5">
            <p><?php echo $error; ?></p>
        </div>
    <?php } ?>
<?php } ?>
</section><?php $_ = OutlineRuntime::finish(__FILE__); ?>