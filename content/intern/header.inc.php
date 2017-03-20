<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="">Internal area</a>
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#internal-navigation" aria-expanded="false">
			<span class="sr-only">show/hide navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</div>
		<div class="collapse navbar-collapse" id="internal-navigation">
			<ul class="nav navbar-nav navbar-right">
				<li <?=(isset($_GET['s']) ? (($_GET['s'] == 'home') ? 'class="active"' : '') : 'class="active"');?>>
					<a href="?s=home">
						Home <span class="glyphicon glyphicon-home"></span>
					</a>
				</li>
				<li <?=(isset($_GET['s']) ? (($_GET['s'] == 'profile') ? 'class="active"' : '') : '');?>>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						Profile <span class="glyphicon glyphicon-user"></span> <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a href="?s=profile&a=email">Change E-Mail</a>
						</li>
						<li>
							<a href="?s=profile&a=image">Change identification image</a>
						</li>
						<li>
							<a href="?s=profile&a=delete">Delete Profile</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(logout());">
						Logout <span class="glyphicon glyphicon-off"></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>