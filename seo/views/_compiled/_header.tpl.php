<?php $_ = OutlineRuntime::start(__FILE__, isset($this) ? $this : null); ?><header id="lTop" class="rel">
    <div class="rel w-960 h-130 mlr-auto tL" >

		<img src="<?php echo SITE_URL;?>assets/img/seo.jpg" style="position:absolute; left:20px; top:27px;" />
		
		
		<nav>
			<ul id="mainMenu">
				<?php foreach ($menu_links as $key => $link) { ?>
					<li><a href="<?php echo SITE_URL; ?>page/<?php echo $link['title']; ?>"><?php echo Str::decode($link['title']); ?></a></li>
				<?php } ?>	
			</ul>
		</nav>
		
    </div>
</header>
<?php $_ = OutlineRuntime::finish(__FILE__); ?>