/**
 * 2011 Aug 05
 * CSC309 - Textual Analysis
 *
 * Input Panel: textfield, filefield, textareafield, numberfield 
 *
 * @author Kobe Sun
 *
 */


/**
 * Clear the file upload field. I intentionally clear the field after the
 * data is visualized in the window, because file descriptor will be reset
 * once the ajax call is sent in EXT-JS, which may cause potential error.
 */
function clearFileUpload(id){
    // get the file upload element
    fileField = document.getElementById(id);
    // get the file upload parent element
    parentNod = fileField.parentNode;
    // create new element
    tmpForm = document.createElement("form");
    parentNod.replaceChild(tmpForm,fileField);
    tmpForm.appendChild(fileField);
    tmpForm.reset();
    parentNod.replaceChild(fileField,tmpForm);
}

Ext.require([
    'Ext.form.*',
    'Ext.tip.*',
    'Ext.layout.container.Column'
]);

Ext.onReady(function() {

	Ext.QuickTips.init();

	// initialize win var and create the panel
	var win = null,
		top = Ext.create('Ext.form.Panel', {
		frame : true,
		title : 'Textual Information Analysis Source Form',
		bodyStyle : 'padding:5px 5px 0',
		width : 600,
		fieldDefaults : {
			labelAlign : 'top',
			labelWidth : 505,
			msgTarget : 'side'
		},

		// create textfield, filefield, textareafield, numberfield inside
		items : [{
			xtype : 'textfield',
			fieldLabel : 'Public URL',
			name : 'url',
			vtype : 'url',
			anchor : '70%'
		}, {
			xtype : 'hiddenfield',
			name : 'MAX_FILE_SIZE',
			value : 2097152
		}, {
			xtype : 'filefield',
			id : 'form-file',
			emptyText : 'Select a plain text file',
			fieldLabel : 'File (Large file may cause long process time; ' 
				+ 'recommended size < 2M)',
			name : 'file',
			anchor : '70%',
			buttonText : 'Browse'
		}, {
			xtype : 'textareafield',
			name : 'text',
			fieldLabel : 'Input Text',
			grow : true,
			growMin : 200,
			growMax : 400,
			anchor : '100%'
		}, {
			xtype : 'numberfield',
			anchor : '100%',
			labelAlign : 'left',
			name : 'topk',
			fieldLabel : 'Display the frequency of the top-k '
				+ '(i.e. most frequent) unique words in the text, where k',
			value : 5,
			maxValue : 30,
			minValue : 1
		}],

		// create submit button and reset button
		buttons : [{
			text : 'Submit',
			handler : function() {
				// Validate the form, in my case, the only possibility is URL
				// format error
				if (this.up('form').getForm().isValid()) {
					this.up('form').getForm().submit({
						url : 'process.php',
						waitMsg : 'Please wait...',
						success : function(form, action) {
							// destroy the window if it exists
							if (win) win.destroy();
							// show the new windows
							win = getResultWin(action.result);
							win.show();
							// clear the filefield in case of potential error
							clearFileUpload('form-file');
						},
						failure : function(form, action) {
							// destroy the window if it exists
							if (win) win.destroy();
							// show the error message
							Ext.MessageBox.alert('Message', action.result);
							// clear the filefield in case of potential error
							clearFileUpload('form-file');
						}
					});
				} else {
					Ext.Msg.alert('Warning','Please provide a valid URL');
				}
			}
		}, {
			text : 'Reset',
			handler : function() {
				this.up('form').getForm().reset();
				// destroy the window if it exists
				if (win) win.destroy();
				// clear the filefield in case of potential error
				clearFileUpload('form-file');
			}
		}]
	});

	top.render(document.body);
});