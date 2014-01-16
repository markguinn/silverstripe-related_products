Related Projects Submodule for Silverstripe Shop/Ecommerce
==========================================================

Allows you to specify related products for any Buyable. This submodule
is compatible with both Shop and Ecommerce modules.

USAGE:
------
Install module via composer (markguinn/silverstripe-related_products)
or the old-fashioned way.

Duplicate or modify the following to apply to any other buyable
models you're using.

```
[buyable class]:
  extensions:
    - HasRelatedProducts
```

Add the following to your `Product.ss` template:

 ```
 <% include RelatedProducts %>
 ```

INSTALL FOLDER:
---------------
Default install folder is related_products. If you would like install
to ecommerce_related_products use dev-ecommerce and likewise for
shop_related_products use dev-shop.

Run dev/build?flush=1 to update your database.

TODO:
-----
* Would be cool to have default settings to generate related
  products from categories and/or search terms

DEVELOPERS:
-----------
* Mark Guinn - mark@adaircreative.com

Pull requests welcome.

LICENSE (MIT):
--------------
Copyright (c) 2013 Mark Guinn

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is furnished to do so, subject
to the following conditions:

The above copyright notice and this permission notice shall be included in all copies
or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.