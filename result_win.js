/**
 * 2011 Aug 05
 * CSC309 - Textual Analysis
 *
 * Window Container for the tabpanel and charts 
 *
 * @author Kobe Sun
 *
 */


Ext.require([
    'Ext.window.*',
   	'Ext.tab.*',
    'Ext.layout.container.Border',
    'Ext.layout.container.HBox'
]);


// Show the constant number results in the tab bar
window.getTbarItems = function(key) {
	var items = [{
		xtype : 'tbtext',
		text : "Number of Sentences: " + key.num_sentences
	}, {
		xtype : 'tbtext',
		text : "Number of Words: " + key.num_words
	}, {
		xtype : 'tbtext',
		text : "Number of Unique Words: " + key.num_uniq_words
	}, {
		xtype : 'tbtext',
		text : "Number of English Characters: " + key.num_chars
	}];

	return items;
};


window.getResultWin = function(res) {
	// create the window
	var win = Ext.create('widget.window', {
		title : 'Showing the result',
		closable : true,
		closeAction : 'destroy',
		width : 800,
		height : 600,
		layout : 'border',
		bodyStyle : 'padding: 5px;',
		items : {
			region : 'center',
			xtype : 'tabpanel',
			// 4 tabs: Total, URL, File, Input Text
			items : [{
				title : 'Total',
				tbar : getTbarItems(res.total),
				layout : {
					type : 'hbox',
					align : 'stretch'
				},
				items : getResultChart(res.total)
			}, {
				title : 'URL',
				tbar : getTbarItems(res.url),
				layout : {
					type : 'hbox',
					align : 'stretch'
				},
				items : getResultChart(res.url)
			}, {
				title : 'File',
				tbar : getTbarItems(res.file),
				layout : {
					type : 'hbox',
					align : 'stretch'
				},
				items : getResultChart(res.file)
			}, {
				title : 'Input Text',
				tbar : getTbarItems(res.text),
				layout : {
					type : 'hbox',
					align : 'stretch'
				},
				items : getResultChart(res.text)
			}]
		}
	});

	return win;
};
	

