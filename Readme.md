# FacetsPlugin

Allows you to insert a Facets block made of Option blocks containing available metadata values extracted from browsing context.

## how to Use

Install the FacetsPlugin

Edit your Theme & add the following code Where you want to display the facets.

```php
<?php echo $this->partial('/facets/index.php', array('request' => new Zend_Controller_Request_Http())); ?>
```
