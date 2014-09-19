
module.exports = function(grunt) {
  grunt.initConfig({
    phpunit: {
      classes: {
        dir: 'tests/WpMock'
      },
      options: {
        followOutput: true,
        coverageHtml: 'coverage'
      }
    },
    phplint: {
      options: {
        swapPath: '/tmp'
      },
      all: [
        'src/**/*.php',
        'tests/**/*.php'
      ]
    },
    phpcs: {
      application: {
        dir: [
          "src/**/*.php",
          "tests/**/*.php"
        ]
      },
      options: {
        standard: 'PSR2',
        extensions: 'php'
      }
    },
    watch: {
      test: {
        files: [
          "src/**/*.*",
          "tests/**/*.*"
        ],
        tasks: ['clear','phplint','phpunit','phpcs']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-phpunit');
  grunt.loadNpmTasks('grunt-phplint');
  grunt.loadNpmTasks('grunt-phpcs');
  grunt.loadNpmTasks('grunt-clear');
};
