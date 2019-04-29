require(['Vue'], function(Vue) {
	var level = 0;
	function flatten(page) {
		var n;
		page.level = level;
		page.indent = '';
		for (n = 0; n < level; n++) {
			page.indent += '&nbsp;&nbsp;';
		}
		level++;
		var pages = [page];
		Object.keys(page._children || {}).forEach(function (child) {
			childPages = flatten(page._children[child]);
			Array.prototype.push.apply(pages, childPages);
		});
		level--;
		return pages;
	}
	var app = new Vue({
		el: '#gonzo',
		data: {
			pages: gonzo.pages
		},
		methods: {
			flatPages: function(page) {
				var pages = flatten(page);
				return pages;
			}
		}
	})
});
