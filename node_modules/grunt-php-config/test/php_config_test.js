'use strict';

var grunt = require('grunt');
String.prototype.removeComments = function() {
    return this.replace(/(\s+\/\/.*)/g, "");
};

exports.php_config = {
    setUp: function(done) {
        done();
    },
    default_options: function(test) {
        test.expect(1);

        var actual = grunt.file.read('tmp/default_options.php');
        var expected = grunt.file.read('test/expected/default_options');
        test.equal(actual.removeComments(), expected, 'Create an empty php file');

        test.done();
    },
    custom_options: function(test) {
        test.expect(1);

        var actual = grunt.file.read('tmp/custom_options.php');
        var expected = grunt.file.read('test/expected/custom_options');
        test.equal(actual.removeComments(), expected, 'Create a PHP file of the specified constants');

        test.done();
    },
};
