<header id="lTop" class="rel">
    <div class="rel w-960 h-130 mlr-auto tL" >

		header
		
		<nav>
			<ul id="mainMenu">
				{foreach $menu_links as $key => $link}
					<li><a href="{#SITE_URL}page/{$link['title']}">{#Str::decode($link['title'])}</a></li>
				{/foreach}	
			</ul>
		</nav>
		
    </div>
</header>
