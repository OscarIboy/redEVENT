/**
 * redevent quickbook module javascript
 */

document.addEvent('domready', function(){
	document.id('qbsubmit-btn').addEvent('click', function() {
		var form = this.getParent('form');
		if (document.formvalidator.isValid(form)) {
			form.submit();
		}
	});
});