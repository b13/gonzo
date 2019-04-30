<template>
	<table class="table table-bordered table-hover table-striped table-condensed">
		<thead>
		<tr>
			<th>Page Title</th>
			<th style="min-width: 500px;">URL Path</th>
		</tr>
		</thead>
		<tbody>
			<tr v-for="page in flatPages(pages)">
				<td>
					<span :class="'level-' + page.level">{{ page.nav_title ? page.nav_title : page.title }}</span>
				</td>
				<td><input class="form-control input-sm" :value="page.slug" /></td>
			</tr>
		</tbody>
	</table>
</template>

<script>
	define(["Vue"], function(Vue) {
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

		Vue.component("slug-list", {
			template: template, // the variable template will be injected
			props: {
				pages: Object
			},
			data: function() {
				return {"text": "Ok"};
			},
			methods: {
				flatPages: function(rootPage) {
					return flatten(rootPage);
				}
			}
		});
	});

</script>

<style scoped>
	.level-1 { display: block; padding-left: 1em; }
	.level-2 { display: block; padding-left: 2em; }
	.level-3 { display: block; padding-left: 3em; }
	.level-4 { display: block; padding-left: 4em; }
	.level-5 { display: block; padding-left: 5em; }
</style>
