# grunt-php-config
[![Build Status](https://travis-ci.org/mothepro/grunt-php-config.svg?branch=master)](https://travis-ci.org/mothepro/grunt-php-config)
[![npm version](https://badge.fury.io/js/grunt-php-config.svg)](https://badge.fury.io/js/grunt-php-config)
[![Dependency Status](https://david-dm.org/mothepro/grunt-php-config.svg)](https://david-dm.org/mothepro/grunt-php-config)
[![devDependency Status](https://david-dm.org/mothepro/grunt-php-config/dev-status.svg)](https://david-dm.org/mothepro/grunt-php-config#info=devDependencies)

> Builds a PHP config and constants file

## Getting Started
This plugin requires Grunt `~0.4.5`

If you haven't used [Grunt](http://gruntjs.com/) before, be sure to check out the [Getting Started](http://gruntjs.com/getting-started) guide, as it explains how to create a [Gruntfile](http://gruntjs.com/sample-gruntfile) as well as install and use Grunt plugins. Once you're familiar with that process, you may install this plugin with this command:

```shell
npm install grunt-php-config --save-dev
```

Once the plugin has been installed, it may be enabled inside your Gruntfile with this line of JavaScript:

```js
grunt.loadNpmTasks('grunt-php-config');
```

## The "php_config" task

### Overview
In your project's Gruntfile, add a section named `php_config` to the data object passed into `grunt.initConfig()`.

```js
grunt.initConfig({
  php_config: {
    options: {
      // Task-specific options go here.
    },
    your_target: {
      // Target-specific file lists and/or options go here.
    },
  },
});
```

### Options

#### options.type
Type: `String`
Default value: `php`

The type of file which will be generated

`php` types create constants like `define('\\NAMESPACE\\NAME', 'VALUE');`

#### options.constants
Type: `Object`
Default value: `{}`

This is where all the constants are specified.
The key of the member will be the constant name, while the value can be either a string, or an object with the member `value`.

```js
{
  name: "value",
  build: {
    value: "1.2"
  }
}
```

Other options may be specified in a specific constant.
A generated file the constants will be a float and may look like this `define('BUILD', 1.2);`, (if the `type` is set to `php`).

```js
{
  name: "value",
  build: {
    value: "1.2",
    raw: true
  }
}
```

Constants may also be nested, to create namespaces.
The following constants object will create the constant `\URL\STATIC\IMAGE` and set it to `FOO`.

```js
{
    url: {
        static: {
            image: "FOO"
        }
    }
}
```

#### options.allCaps
Type: `Boolean`
Default value: `true`

Should the constant be in all capitals

#### options.raw
Type: `Boolean`
Default value: `false`

Typically constant values are escaped and put in quotes.
However, if this is true the constant's value will be untouched.

### Usage Examples

#### Options
In this example, an empty `destination.php` file will be generated because no constants are specified.

```js
grunt.initConfig({
  php_config: {
    dest: 'destination.php',
  },
});
```

#### Options
In this example constants will be read from the config.json file and be embedded into `destination.php` as is.

```js
grunt.initConfig({
  php_config: {
    options: {
      constants: grunt.file.readJSON('config.json'),
      raw: true,
      allCaps: false,
    },
    dest: 'destination.php',
  },
});
```

## Contributing
In lieu of a formal styleguide, take care to maintain the existing coding style. Add unit tests for any new or changed functionality. Lint and test your code using [Grunt](http://gruntjs.com/).

## Release History
 * 2015-12-02   v0.1.0   First official release
 
