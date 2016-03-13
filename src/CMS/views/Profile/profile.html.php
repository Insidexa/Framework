<div class="col-md-offset-2 col-md-8">
	<form role="form" method="post" action="<?php echo $action ?>">
		<div style="display: none" class="form-group">
			<label for="id" class="col-sm-2 control-label">Email</label>
			<div class="col-sm-10">
				<input type="hidden" id="id" value="<?php echo $user->id ?>" name="id">
			</div>
		</div>
		<div class="form-group">
			<label for="email" class="col-sm-2 control-label">Email</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" id="email" placeholder="Email"
				       value="<?php echo $user->email ?>" name="email">
			</div>
		</div>
		<div class="form-group">
			<label for="password" class="col-sm-2 control-label">Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="password" placeholder="Password"
				       value="<?php echo $user->password ?>" name="password">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Update</button>
			</div>
		</div>
		<?php $generateToken() ?>
	</form>
</div>