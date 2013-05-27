/**
 * javascript for ajax navigation
 */

window.addEvent('domready', function() {

	document.id('redevent').addEvent('change:relay(#el_filter input)', function(e) {
		e.stop();
		red_ajaxnav.submitForm(this.getParent('form'));
	});
	document.id('redevent').addEvent('change:relay(#el_filter select)', function(e) {
		e.stop();
		red_ajaxnav.submitForm(this.getParent('form'));
	});
	document.id('redevent').addEvent('click:relay(#filter-go)', function(e) {
		e.stop();
		red_ajaxnav.submitForm(this.getParent('form'));
	});
	document.id('redevent').addEvent('click:relay(#filter-reset)', function(e) {
		e.stop();
		$$('#el_filter select').set('value', '0');
		$$('#el_filter input').set('value', '');
		red_ajaxnav.submitForm(this.getParent('form'));
	});
	
});

