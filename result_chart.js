/**
 * 2011 Aug 05
 * CSC309 - Textual Analysis
 *
 * EXTJS Charts: Pie chart and Column chart
 *
 * @author Kobe Sun
 *
 */

Ext.require([
    'Ext.chart.*',
    'Ext.data.*',
    'Ext.tip.*'
]);


window.getResultChart = function(resdata) {
	// use label instead if no data is provided
	if (resdata.freq_tbl.length < 1) {
		var labelwarning = {
			xtype: 'label',
	        text: 'No chart data is available',
	        margins: '10'
		};
		return labelwarning;
	} else {
		// initialize the data store for pie chart and load
		var store1 = Ext.create('Ext.data.JsonStore', {
	        fields: ['name', 'freq']
	    });
		store1.loadData(resdata.freq_tbl);
		
		// initialize the data store for column chart and load
		var store2 = Ext.create('Ext.data.JsonStore', {
	        fields: ['name', 'freq']
	    });
		store2.loadData(resdata.most_freq);
		
		// create the pie chart and column chart
		var charts = [{
			xtype : 'chart',
			flex : 1,
			animate : true,
			store : store1,
			shadow : true,
			insetPadding : 25,
			series : [{
				type : 'pie',
				field : 'freq',
				tips : {
					trackMouse : true,
					width : 100,
					height : 25,
					renderer : function(storeItem, item) {
						// calculate percentage
						var total = 0;
						store1.each(function(rec) {
							total += rec.get('freq');
						});
						this.setTitle(storeItem.get('name') + ': ' 
								+ storeItem.get('freq') + " - " 
								+ Math.round(storeItem.get('freq') 
										/ total * 1000) / 10 + '%');
					}
				},
				highlight : {
					segment : {
						margin : 20
					}
				},
				label : {
					field : 'name',
					display : 'rotate',
					contrast : true,
					font : '18px Arial'
				}
			}]
		}, {
			xtype : 'chart',
			flex : 1,
			animate : true,
			shadow : true,
			store : store2,
			axes : [{
				type : 'Numeric',
				position : 'left',
				fields : [ 'freq' ],
				label : {
					renderer : Ext.util.Format.numberRenderer('0,0')
				},
				title : 'Number of Occurrences',
				grid : true,
				minimum : 0
			}, {
				type : 'Category',
				position : 'bottom',
				fields : [ 'name' ],
				title : 'Most Frequent Words'
			}],
			series : [{
				type : 'column',
				axis : 'left',
				gutter : 80,
				xField : 'name',
				yField : 'freq',
				stacked : true,
				tips : {
					trackMouse : true,
					width : 180,
					height : 25,
					renderer : function(storeItem, item) {
						this.setTitle(storeItem.get('name') + ': ' 
								+ storeItem.get('freq'));
					}
				}
			}]
		}];
		
		return charts;
	}
};
