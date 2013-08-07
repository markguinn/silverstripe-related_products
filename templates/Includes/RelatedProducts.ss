<% if $RelatedProducts %>
	<h3>Related Products</h3>
	<ul class="related-products">
		<% loop $RelatedProducts %>
			<% include  ProductGroupItem %>
		<% end_loop %>
	</ul>
<% end_if %>