<form class="commentForm" method="get" action="">
	<fieldset>
		<div class="formField">
			<div class="fieldContent">
				<label for="primary_contact_name">Name</label>
				<input type="text" class="primary_contact_name" name="primary_contact_name" />
			</div>
		</div>
		<div class="formField">
			<div class="fieldContent">
				<label for="company">Company</label>
				<input type="text" class="company" name="company" />
			</div>
		</div>
		<div class="formField">
			<div class="fieldContent">
				<label for="address">Address</label>
				<input type="text" class="address" name="address" />
			</div>
		</div>
		<div class="formField">
			<div class="fieldContent">
				<label for="email">E-Mail</label>
				<input type="text" class="email" name="email" />
			</div>
		</div>
		<div class="formField">
			<div class="fieldContent">
				<label for="telephone">Phone</label>
				<input type="text" class="telephone" name="telephone" />
			</div>
		</div>
		<div class="formField">
			<div class="fieldContent">
				<label for="selected_event">Event</label>
				<div class="select-wrapper">
					<select class="selected_event" name="selected_event">
						<option value="">Select an Event</option>
					</select>
				</div>
			</div>
		</div>
		<!-- <div class="formField" id="contactCaptcha"></div> -->
		<div class="formField formSubmit">
			<input class="submit" type="submit" value="Submit"/>
		</div>
	</fieldset>
	<div class="formResponse"></div>
</form>