require(['Vue', "vue!../typo3conf/ext/gonzo/Resources/Public/JavaScript/Components/slug-list"], function(Vue) {
	var app = new Vue({
		data: function() {
			return {pages: window.gonzo};
		},
		el: '#gonzo'
	})
});
