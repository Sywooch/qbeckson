var gulp = require('gulp');
var pug = require('gulp-pug');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var cssnano = require('gulp-cssnano');
var csscomb = require('gulp-csscomb');

gulp.task('default', ['watch']);

gulp.task('watch', function () {
    gulp.watch('sass/**/*.sass', ['sass']);
    gulp.watch('sass/**/*.scss', ['sass']);
    gulp.watch('pug/**/*.html', ['pug']);
    gulp.watch('pug/**/*.pug', ['pug']);
});

gulp.task('sass', function () {
    gulp.src('sass/newstyle.sass')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['> 1%', 'IE 8'],
            cascade: false
        }))
        .pipe(csscomb())
        .pipe(cssnano())
        .pipe(gulp.dest('build'));
});

gulp.task('pug', function () {
    return gulp.src('pug/pages/**/*.pug')
        .pipe(pug({pretty: true}))
        .pipe(gulp.dest('build'));
});