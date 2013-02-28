module.exports = (grunt) ->
  # MODULES
  fs = require('fs')

  # ENVIRONMENT
  __ENV__ = null

  setContantToCorrectJsForEnvironment = (line, boilerplate, file, location) ->
    switch __ENV__
      when 'dev'
        if /\.min\.js$/.test(location)
          location = location.replace(/\.min\.js$/, '.js')
      when 'prod'
        unless /\.min\.js$/.test(location)
          location = location.replace(/\.js$/, '.min.js')
    boilerplate + file + ' = "' + location + '";'

  setupPhp = (file) ->
    php = fs.readFileSync(file).toString()
    php = php.replace(/(\s*const )(JS_DISPLAY)[^;]*"([^;]*)";/, setContantToCorrectJsForEnvironment, 'm')
    php = php.replace(/(\s*const )(JS_EDITOR)[^;]*"([^;]*)";/, setContantToCorrectJsForEnvironment, 'm')
    fs.writeFileSync(file, php)

  # Config
  grunt.initConfig(
    pkg: grunt.file.readJSON('package.json')
    go:
      phpFileLocation: 'Random-Tweet.php'
    coffee:
      compile:
        files:
          '/tmp/grunt/Random-Tweet/js/randomtweet-editor.js': 'src/randomtweet-editor.coffee'
    uglify:
      editor:
        src: '/tmp/grunt/Random-Tweet/js/randomtweet-editor.js'
        dest: 'js/randomtweet-editor.min.js'
    copy:
      main:
        files: [
          {expand: true, cwd: '/tmp/grunt/Random-Tweet/js/', src: '*', dest: 'js/', filter: 'isFile'}
        ]
    clean: ["js"]
    watch:
      scripts:
        files: 'src/*.coffee'
        tasks: ['clean', 'coffee', 'copy']
        options:
          interrupt: true
    compress:
      package:
        options:
          archive: 'builds/Random-Tweet-<%= pkg.version %>.zip'
        files: [
          {src:['*.php', '*.md', 'css/**', 'js/**']}
        ]

    grunt.loadNpmTasks('grunt-contrib-uglify')
    grunt.loadNpmTasks('grunt-contrib-coffee')
    grunt.loadNpmTasks('grunt-contrib-copy')
    grunt.loadNpmTasks('grunt-contrib-watch')
    grunt.loadNpmTasks('grunt-contrib-clean')
    grunt.loadNpmTasks('grunt-contrib-compress')

    grunt.registerTask('go', 'Switch environments', (env) ->
      __ENV__ = env
      config = grunt.config.data.go
      setupPhp(config.phpFileLocation)
      grunt.task.run(['clean', 'coffee'])
      if __ENV__ is 'dev'
        grunt.task.run('copy')
        grunt.task.run('watch')
      else if __ENV__ is 'prod'
        grunt.task.run('uglify')
    )

    grunt.registerTask('package', 'Make deployment package', ->
      grunt.task.run(['go:prod', 'compress:package'])
    )
  )
