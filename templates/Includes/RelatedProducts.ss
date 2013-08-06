<% if $RelatedProducts %>
	<ul class="related-products">
		<% loop $RelatedProducts %>
			<% include  ProductGroupItem %>
		<% end_loop %>
	</ul>
<% end_if %>