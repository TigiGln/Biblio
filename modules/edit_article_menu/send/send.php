<link href="../modules/edit_article_menu/send/send.css" rel="stylesheet"/>
<div id="send" class="d-flex flex-column">
	<div class="alert alert-secondary mt-0 mb-0 p-1">
		Once you send the article to someone else, unless the person sent it back to you, you will not be able to note, nor annotate it.<br>Do not select an user and send to send back the article to the open tasks.
  	</div>
  	<!-- send data using js function to augment it and catch servers failure-->
	<div class="form-group">
		<label for="sendTo">Send To:</label>
		<input type="text" list="usersList" id="sendTo" name="sendTo" class="form-control" />
		<datalist id="usersList">
		</datalist>
	</div>
	<button type="button" class="btn btn-success" onclick="validateSendInteraction()">Send</button>
<script src="../modules/edit_article_menu/send/send-INTERACTIONS.js"></script>
</div>