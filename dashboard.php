<?php 
	$loc = "dashboard";
	include("_assets/connect.php");
	include("../_assets/header.php");
	
	if (check_logged_in()){
?>

<div class="redeem-confirmation">

	<h4>Are you sure you want to redeem<br />your commission into a store voucher?</h4>
	
	<p>
		<a href="redeem-store-credit.php" class="button">Confirm</a>
		<a href="#" id="cancel" class="button">Cancel</a>
	</p>

</div>

<div class="overlay"></div>

<v:section path="rep_program">
	
	<div class="content dashboard-content">
	
		<div class="good-word-movement-header">
			
			<img src="_assets/images/good-word-movement.jpg" />
			
		</div>
		
		<p>Welcome back, <strong><?php echo $user['name']; ?>!</strong> (<a href="logout.php">Logout</a>)</p>
	
		<div class="dashboard">
		
			<div class="dashboard-nav">
			<?php if (check_admin($user_id) === true) { ?>
				<a href="#" class="dashboard-selected approve">Approve Users</a>
			<?php } else { ?>
				<a href="#" class="dashboard-selected sales">Sales</a>
				<a href="#" class="assets">Assets</a>
				<a href="#" class="faqs">Faqs</a>
			<?php } ?>
				<a href="#" class="settings">Settings</a>
			</div>
			
			<?php if (check_admin($user_id) === true){ ?>
			
			<article id="approve">
			
				<ul class="approve-users">
				
					<li class="approve-headers">
						<div class="name">
							Name
						</div>
						<div class="email">
							Email
						</div>
						<div class="joined">
							Date Applied
						</div>
						<div class="approve">
						</div>
					</li>
				
					<?php
						$users = mysql_query("SELECT * FROM `users` WHERE `approved` = 0");
						
						while ($user = mysql_fetch_assoc($users)) {
							
						?>
							<li>
								<div class="name">
									<?php echo $user['name']; ?>
								</div>
								<div class="email">
									<?php echo $user['email']; ?>
								</div>
								<div class="joined">
									<?php echo date("F d, Y", strtotime($user['joined'])); ?>
								</div>
								<div class="approve">
									<a href="_widgets/approve.php?email=<?= $user['email']; ?>" class="button approve-user">APPROVE</a>
									<a href="_widgets/decline.php?email=<?= $user['email']; ?>" class="button decline-user">DECLINE</a>
								</div>
							</li>
						<?php
						}
					?>

				
				</ul>
			
			</article>
			
			<?php } else { ?>
		
			<article id="sales">
			
				<div class="rep-info">
					
					<h4>Profile</h4>
					
					<div class="earnings">
					
						<h6>Your Current Earnings</h6>
						
						<h1>$<?php echo number_format($commission, 2); ?></h1>
						
						<?php if ($commission > 0) { ?>
						<p>Redeem your earnings:</p>						
						<a href="#" id="redeem-dat-money" class="button">STORE CREDIT</a>
						<?php } ?>
						<?php if ($total_sales >= 1000) { ?>
						<a href="#" class="button">CASH OUT</a>
						<?php } ?>
					
					</div>
					
					<div class="profile-data">
					
						<h6><?php echo greeting(); ?>, <?php echo $user['name']; ?>!</h6>
						
						<p>Member since: <?php echo date("F Y", strtotime($user['joined'])); ?></p>
	
						<p><strong>Rep Code:</strong> <?php echo $user['code']; ?></p>
						
						<p>
							<strong>Alt Rep Code:</strong> <?php echo $user['code'] . "pv"; ?>
							<br />
							<span class="earnings-disclaimer">This code will be used for special store offers.</span>
						</p>
						
						<p>
							<strong>Current Total Sales:</strong> $<?php echo number_format($total_sales, 2); ?>
							<br/>
							<span class="earnings-disclaimer">Earnings can be redeemed as cash when total sales equal or exceed $1000.</span>
						</p>
						
						<p>
							<strong>Lifetime Sales:</strong>  $<?php echo number_format($lifetime_sales, 2); ?> (<?php echo $user['lifetime_sales_count']; if ($user['lifetime_sales_count'] == 1) {echo " item";} else { echo " items"; }  ?>)
						</p>
						
						<p><strong><a href="sales-history.pdf.php">View My Sales History</a></strong></p>
					
					</div>
					
					<div class="clearfix"></div>
					
					<div class="recent-sales">
					
					<h4>Recent Sales</h4>
					
						<ul class="orders">
						
							<li class="header">
								<div class="customer">Customer Name</div>
								<div class="order-date">Order Date</div>
								<div class="commission">Your Commission</div>
							</li>
							
							<?php
								$limit = 5;
								$counter = 0;
								$orders = vae_store_orders(array('discount_code' => $user['code'], 'status' => 'Ordered', 'status' => 'Processing', 'status' => 'Shipped'));
								foreach ($orders as $order){
							?>
									<li>
										<div class="customer"><?php echo $order['billing_name']; ?></div>
										<div class="order-date"><?php echo date("F d, Y", strtotime($order['created_at'])); ?></div>
										<div class="commission">$<?php echo number_format(($order['total'] * .6), 2); ?></div>
									</li>
 
							<?		
									$counter++;
							
 
									if ($counter >= $limit) {
										break;
									}
								}
							?>
														
						</ul>
					
					</div>
					
				</div>
				
				<div class="rep-news">
				
					<h4>News</h4>
					
					<v:collection path="/rep_dashboard/news" limit="1">
					
						<h6><v:text path="title" /></h6>
						
						<h6 class="news-date">Posted on: <v:text path="date" strftime="%B %d, %Y at %l:%M %p %Z" /></h6>
						
						<v:text path="text" />
					
					</v:collection>
				
				</div>
				
				<div class="clearfix"></div>
							
			</article>
		
			<article id="assets">
			
				
	<ul class="assets">
	
				<?php
					
					// Array of "Rep Code" images
					$repImages = array();

					$images = vae("rep_dashboard/rep_code_assets");
					$user_code = $user['code'];
					foreach ($images as $image){
					  	$current = vae_image($image->image);
					  	$url = vae_data_url() . $current;
					  	$xPos = $image->x_position;
					  	$yPos = $image->y_position;
  						$fontSize = $image->font_size;
						$pvSet = $image->pv;
  						$color = $image->color;
  						$color = str_replace("#", "", $color);					
				?> 	
					
						<li>
								<?php
									$user_code = $user['code'];
									$linkUrl = "http://www.paidvaca.com/goodword/rep-image.php?repcode=" .  $user_code . "&pvCheck=" . $pvSet . "&imageUrl=" . $url . "&x=" . $xPos . "&y=" . $yPos . "&fontSize=" . $fontSize . "&color=" . $color;								
								?>
								<a href="<?= $linkUrl; ?>">	
								<img src="<?= $url; ?>" />
							</a>
							<p>
								<strong><?php echo $image->name; ?></strong>
								<br />
								<?php echo $image->size; ?>
							</p>
						</li>
					
					<?php } ?>
				
					<v:collection path="/rep_dashboard/assets">
				
						<li>
							<v:file path="file" filename="<v=name>">
								<v:img path="thumbnail" />
								<p>
									<strong><v:text path="name" /></strong>
									<br />
									<v:text path="size" />
								</p>
							</v:file>
						</li>
					
					</v:collection>

				</ul>
			
			</article>
		
			<article id="faqs">
			
			<v:collection path="/rep_dashboard/faqs">
			
				<div class="question">
					<h6><v:text path="question" /></h6>
					<v:text path="answer" />
				</div>
				
			</v:collection>
			
			</article>
			
			<?php } ?>
			
			<article id="settings">
			
				
				
				<?php if (check_admin($user_id) === false) { ?>
				
				<h4>Settings</h4>
				
				<div id="messages"></div>
				
				<form action="_widgets/settings.php" method="post">
				
					<p><strong>Name</strong><br /><input type="text" name="name" value="<?= $user['name']; ?>" /></p>
					
					<p><strong>Email</strong><br /><input type="text" name="email" value="<?= $user['email']; ?>" /></p>
					
					<p><strong>PayPal Email</strong><br /><input type="text" name="paypal_email" value="<?= $user['paypal_email']; ?>" /></p>
					
					<p><strong>Goodword Code</strong><br /><input type="text" name="code" value="<?= $user['code']; ?>" /></p>
					
					<h4>Password</h4>
					
					<p><strong>Old Password</strong><br /><input type="password" name="old-password" /></p>
					
					<p><strong>New Password</strong><br /><input type="password" name="new-password" /></p>
					
					<p><strong>Confirm New Password</strong><br /><input type="password" name="confirm-new-password" /></p>
					
					<p class="form-submit"><input type="submit" class="button" value="SUBMIT" /></p>
				
				</form>
				
				<?php } else { ?>
				
				<h4>Featured Code</h4>
				
				<div id="messages"></div>
				
				<form action="_widgets/replicate.php" method="post">
				
					<p>Enter the featured code to be used:</p>
					
					<p><input type="text" name="code" /></p>
					
					<p class="form-submit"><input type="submit" class="button" value="SUBMIT" /></p>
				
				</form>
				
				<?php } ?>
			
			</article>
		
		</div>
		 				
	</div>
	
</v:section>

<script src="_assets/js/dashboard.js"></script>

<?php 
	} else {
?>

<div class="content dashboard-content">
	
		<div class="good-word-movement-header">
			
			<img src="_assets/images/good-word-movement.jpg" />
			
		</div>	
		
		<div class="dashboard">
		
			<?php include("_widgets/logged-in-warning.php"); ?>
		
		</div>
		
</div>		
		
<?php		
	}

	include("../_assets/footer.php");
?>
