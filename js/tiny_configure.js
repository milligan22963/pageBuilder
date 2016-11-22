/*
 * tiny_MCE configuration file
 *
 */
function SaveDelegateSingleton()
{
	this.delegate = null;
	
	this.clearSaveCallback = function()
	{
		this.setSaveCallback(null);
	};
	
	this.setSaveCallback = function(saveCallback)
	{
		this.delegate = saveCallback;
	};
	
	this.makeCall = function(data)
	{
		alert('Save: ' + data.getContent());
		if (this.delegate != null)
		{
			this.saveCallback();
		}
	};
	
	if (!arguments.callee.instance)
	{
		arguments.callee.instance = this;
	}
	return arguments.callee.instance;
}

SaveDelegateSingleton.getInstance = function ()
{
	return new SaveDelegateSingleton();
};

var saveSingleton = SaveDelegateSingleton.getInstance();

tinyMCE.init({
        theme : "advanced",
        theme_advanced_layout_manager : "SimpleLayout",
        skin : "o2k7",
    	skin_variant : "silver",
    	plugins: "save,autolink,lists,pagebreak,style,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
    	theme_advanced_buttons1 : "newdocument,save,cut,copy,paste,pastetext,pasteword,|,bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
    	theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,removeformat,visualaid,|,charmap,emotions,iespell,media,advhr,|,print,|,fullscreen",
    	theme_advanced_buttons3 : "",
    	theme_advanced_buttons4 : "",
    	theme_advanced_toolbar_location : "top",
    	theme_advanced_toolbar_align : "left",
    	theme_advanced_statusbar_location : "bottom",
    	theme_advanced_resizing : true,
        save_enablewhendirty : true,
        save_onsavecallback : "saveSingleton.makeCall",
        autosave_ask_before_unload : false,
        mode : "specific_textareas",
        editor_selector : "mceEditor"
});
