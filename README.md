# AutoWPDB

Allows to manage custom database tables in WordPress.

Requires **php 7.0** and **WordPress 4.9.6**. With php 7.1+, can be used with WordPress 4.7.

## What you will be able to do

* Decide if your custom table is network-wide or site-wide in a multisite environment,
* Create your table,
* Update your table structure,
* Determine if your table is ready to be used,
* Create custom CRUD methods, based on the basic ones provided,
* Provide default values,
* Cast and serialize values automatically.

## How to install

With composer:

```
"require": {
	"screenfeed/autowpdb": "dev-master"
},
```

## How to use

Create 2 classes and you’re ready:

* One that "defines" your custom table (name, default values, value types, schema, etc) by extending *TableDefinition\AbstractTableDefinition*,
* One containing your CRUD methods (optional) by extending *CRUD\Basic*.

Please take a look at [this plugin](https://github.com/Screenfeed/autowpdb-example-plugin) to see an example of use.
