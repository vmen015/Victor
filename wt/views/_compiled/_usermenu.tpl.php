<?php $_ = OutlineRuntime::start(__FILE__, isset($this) ? $this : null); ?><div class="abs abs-tr bgc-b c-w plr-20 ptb-4 z-10">
    <a href="<?php echo SITE_URL.$me['username']; ?>" class="dIB mr-4 mmTip" data-tip="<?php echo $me['username']; ?>" data-tip-position="bottom" data-tip-edge="top">Profile</a>
    <a href="<?php echo SITE_URL; ?>account" class="dIB mr-4">Account</a>
    <a href="<?php echo SITE_URL; ?>account/songs" class="dIB mr-4">Songs</a>
    <a href="<?php echo SITE_URL; ?>account/samples" class="dIB mr-4">Samples</a>
    <a href="<?php echo SITE_URL; ?>logout" class="dIB mr-4">Logout</a>    
</div>
<?php $_ = OutlineRuntime::finish(__FILE__); ?>