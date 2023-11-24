module.exports = function(grunt) {

    // load all grunt tasks in package.json matching the `grunt-*` pattern
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        csscomb: {
            dist: {
                files: [{
                    expand: true,
                    cwd: 'assets/css/',
                    src: ['*.css', '!js/**'],
                    dest: 'assets/css/',
                }]
            }
        },

        autoprefixer: {
            options: {
                browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1']
            },
            multiple_files: {
              expand: true,
              flatten: true,
              src: ['assets/css/*.css', '!**/*.min.js'],
              dest: 'assets/css/'
            }
        },

        cmq: {
            options: {
                log: false
            },
            dist: {
                files: {
                    'assets/css/admin.css': 'assets/css/admin.css'
                }
            }
        },

        cssmin: {
            minify: {
                expand: true,
                cwd: 'assets/css/',
                src: ['*.css', '!*.min.css', '!js/**'],
                dest: 'assets/css/',
                ext: '.min.css'
            }
        },

        concat: {
            dist: {
                src: [
                    'assets/js/src/*.js',
                ],
                dest: 'assets/js/admin.js',
            }
        },

        uglify: {
            build: {
                options: {
                    mangle: false
                },
                files: [{
                    expand: true,
                    cwd: 'assets/js/',
                    src: ['**/*.js', '!**/*.min.js', '!src/*.js','!tests/**/*.js'],
                    dest: 'assets/js/',
                    ext: '.min.js'
                }]
            }
        },

        imagemin: {
            dynamic: {
                files: [{
                    expand: true,
                    cwd: 'assets/images/',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: 'assets/images/'
                }]
            }
        },

        watch: {
            scripts: {
                files: ['assets/js/**/*.js'],
                tasks: ['js'],
                options: {
                    spawn: false,
                    livereload: true,
                },
            },

            css: {
                files: ['assets/**/*.css'],
                tasks: ['css'],
                options: {
                    spawn: false,
                    livereload: true,
                },
            }
        },

        shell: {
            grunt: {
                command: '',
            }
        },



    });

    grunt.registerTask('css', ['autoprefixer', 'cssmin']);
    grunt.registerTask('js', ['concat', 'uglify']);
    grunt.registerTask('imageminnewer', ['newer:imagemin']);
    grunt.registerTask('default', ['css', 'js', 'imageminnewer']);
};