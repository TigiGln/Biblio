<link href="../modules/edit_article_menu/notes/notes.css" rel="stylesheet"/>
<div id="notes" class="notesEditor d-flex flex-column">

<div id="notesEditor" class="notesEditor d-flex flex-column" onload="notesLoad()">
	<div class="notesToolbar">
	<div class="alert alert-secondary mt-0 mb-0 p-1">
		<button id="notesSave" type="button" onclick="notesSave()" class="btn btn-success">S</button>
		Your Notes:
  	</div>
	<div class="notesLine bg-light w-100">
	<div class="notesBox row text-center">
		<span class="notesAction col-md-auto" data-action="bold" title="Bold">
			<img src="https://image.flaticon.com/icons/svg/25/25432.svg">
		</span>
		<span class="notesAction col-md-auto" data-action="italic" title="Italic">
			<img src="https://image.flaticon.com/icons/svg/25/25392.svg">
		</span>
		<span class="notesAction col-md-auto" data-action="underline" title="Underline">
			<img src="https://image.flaticon.com/icons/svg/25/25433.svg">
		</span>
		<span class="notesAction col-md-auto" data-action="createLink" title="Insert Link">
			<img src="https://image.flaticon.com/icons/svg/25/25385.svg">
		</span>
		<span class="notesAction col-md-auto" data-action="unlink" title="Unlink">
			<img src="https://image.flaticon.com/icons/svg/25/25341.svg">
		</span>
		<span class="notesAction col-md-auto" data-action="undo" title="Undo">
			<img src="https://image.flaticon.com/icons/svg/25/25249.svg">
		</span>
		<span class="notesAction col-md-auto" data-action="removeFormat" title="Remove format">
			<img src="https://image.flaticon.com/icons/svg/25/25454.svg">  
		</span>
		<span id="notesCode" class="notesAction col-md-auto" data-action="code" title="Show HTML-Code">
			<img src="https://image.flaticon.com/icons/svg/25/25185.svg">
		</span>
	</div>
	</div>
	<div id="notesArea">
		<div id="notesVisualView"  placeholder="Your Notes" contenteditable="true"></div>
	<textarea id="notesHtmlView"></textarea>
	</div>
</div>

<div class="alert alert-secondary mt-0 mb-0 p-1">Notes Thread:</div>
<div id="notesThread" class="notesThread overflow-auto alert alert-light mt-0 mb-0">
	<!---->
</div>
<script src="../modules/edit_article_menu/notes/notes-INTERACTIONS.js"></script>
<script src="../modules/edit_article_menu/notes/notes-WYSIWYG.js"></script>
</div>