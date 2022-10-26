var gulp = require('gulp');

var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
gulp.src(["lib/*.{js,json}", "lib/**/*.{js,json}"])

//script paths
var jsFiles = '../scripts/**/*.js',
    jsDest = '';

gulp.task('scripts', function() {
    return gulp.src( [
            "../scripts/app.js",
            "../scripts/AppController.js",
            "../scripts/authController.js",
            "../scripts/userController.js",
            "../scripts/**/*.js"
        ])
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest(jsDest))
        .pipe(rename('scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(jsDest));
});


