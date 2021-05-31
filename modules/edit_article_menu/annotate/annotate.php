<link href="../modules/edit_article_menu/annotate/annotate.css" rel="stylesheet"/>
<div id="subMenu">
	<div class="annotateEditor d-flex flex-column">
	  <div id="annotateSelection">
	  <div class="alert alert-secondary mt-0 mb-0">Selection:</div>
		<div class="selected overflow-auto alert-light mt-0 mb-0">
			<div id="selection"></div>
	  	</div>
	  </div>
	  <div id="annotateEditor" class="annotateToolbar" style="pointer-events: none; user-select: none;">
	  	<div id="annotateInteraction" class="alert alert-secondary mt-0 mb-0 p-1">
	  		<button id="annotateSave" type="button" onclick="annotateSend()" class="btn btn-success">S</button>
    		<button id="annotateAbort" type="button" onclick="annotateClose()" class="btn btn-danger">X</button>
	  		Write an annotation:
	  	</div>
	    <div class="annotateLine bg-light w-100">
	      <!-- Actions -->
	    <div class="annotateBox row text-center">
	        <span class="annotateAction col-md-auto" data-action="bold" title="Bold">
	          <img src="https://image.flaticon.com/icons/svg/25/25432.svg">
	        </span>
	        <span class="annotateAction col-md-auto" data-action="italic" title="Italic">
	          <img src="https://image.flaticon.com/icons/svg/25/25392.svg">
	        </span>
	        <span class="annotateAction col-md-auto" data-action="underline" title="Underline">
	          <img src="https://image.flaticon.com/icons/svg/25/25433.svg">
	        </span>
	        <span class="annotateAction col-md-auto" data-action="createLink" title="Insert Link">
	          <img src="https://image.flaticon.com/icons/svg/25/25385.svg">
	        </span>
	        <span class="annotateAction col-md-auto" data-action="unlink" title="Unlink">
	          <img src="https://image.flaticon.com/icons/svg/25/25341.svg">
	        </span>
	        <span class="annotateAction col-md-auto" data-action="undo" title="Undo">
	          <img src="https://image.flaticon.com/icons/svg/25/25249.svg">
	        </span>
	        <span class="annotateAction col-md-auto" data-action="removeFormat" title="Remove format">
	          <img src="https://image.flaticon.com/icons/svg/25/25454.svg">  
	        </span>
	        <input id="annotateColorPicker" type="color" class="align-middle" value="#ffff00">
	        <span id="annotateCode" class="annotateAction" data-action="code" title="Show HTML-Code" hidden></span>
	    </div>
	  	</div>
	  <div id="annotateArea">
	     <div id="annotateVisualView" contenteditable="true" placeholder="Your Annotation"></div>
	    <textarea id="annotateHtmlView" placeholder="Your Annotation"></textarea>
	  </div>
	  <div id="annotateRemove"><!----></div>
	</div>
	<script src="../modules/edit_article_menu/annotate/annotate-INTERACTIONS.js"></script>
	<script src="../modules/edit_article_menu/annotate/annotate-WYSIWYG.js"></script>
</div>
</div>