wp_mock
==========

Mock integration for WordPress functions in PHPUnit

Option mocking
==============
You can test option related functions add_option, update_option, delete_option.
The function get_option can be used but is not testable (yet).

== Usage
````php
WpOptions::expects('foo')->added();
WpOptions::expects('foo')->added()->with('bar');
WpOptions::expects('foo')->added()->with('bar')->noAutoload();
WpOptions::expects('foo')->added()->with('bar')->isAutoload();
WpOptions::expects('foo')->added()->noAutoload();
WpOptions::expects('foo')->added()->isAutoload();
WpOptions::expects('foo')->updated();
WpOptions::expects('foo')->updated()->to('bar');
WpOptions::expects('foo')->deleted();
````
