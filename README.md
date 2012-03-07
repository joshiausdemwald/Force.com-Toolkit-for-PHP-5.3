Force.com Tookit for PHP 5.3
============================

Welcome to the Force.com Toolkit for PHP 5.3. Likewise it's originator, Force.com Toolkit for PHP
(http://wiki.developerforce.com/page/Force.com_Toolkit_for_PHP), this library enables
you to connect to the Force.com Webservice and Metadata API via SOAP using the native C-extension "PHP:SOAP" (should
be compiled and enabled by default in almost every PHP version >= 5.3.x. Otherwise you'll probably have to reconfigure
your PHP sources by adding "--enable-soap".

On top, a full featured SOQL-Query tokenizer and parser is included which enables you to create SOQL-queries of sort
"prepared statement" (as for example the PHP:PDO application interface provides).

You are able to add SOAP (simple) type and (complex) class bindings. Create or let your .wsdl generate your popo
(plain-old-php-object) stubs and bind them to the soap client.

This toolkit is still subject of heavy development, so no official documentation is provided so far. Take a look
at the sources and unit test cases to get an impression of how to get this stuff to work.

Have fun!





