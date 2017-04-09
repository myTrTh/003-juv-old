/*
npm i --save-dev gulp gulp-sass gulp-concat gulp-uglifyjs gulp-cssnano browser-sync gulp-autoprefixer gulp-twig gulp-rename
*/

var gulp         = require('gulp'), // Подключаем Gulp
    sass         = require('gulp-sass'), //Подключаем Sass пакет,
    uglify       = require('gulp-uglifyjs'), // Подключаем gulp-uglifyjs (для сжатия JS)
    cssnano      = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
    rename       = require('gulp-rename'), // Подключаем пакет для переименования
    concat       = require('gulp-concat'), // Объединение js файлов
    cssnano      = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
    twig		 = require('gulp-twig'), // twig
    browserSync  = require('browser-sync'), // Отслеживание изменений online
    autoprefixer = require('gulp-autoprefixer');// Библиотека для автоматического добавления префиксов

gulp.task('sass', function() {
	return gulp.src('gulp/scss/*.scss')
		   .pipe(sass())
		   .pipe(autoprefixer(['last 25 versions', 'ie 6-9'], { cascade: true}))
		   .pipe(cssnano())
		   .pipe(gulp.dest('web/public/css'))
		   .pipe(browserSync.reload({stream: true}))
});

gulp.task('scripts', function() {
	return gulp.src('gulp/js/*.js')
		   .pipe(concat('libs.js'))
		   .pipe(uglify())
		   .pipe(gulp.dest('web/public/js'))
		   .pipe(browserSync.reload({stream: true}))
});

// gulp.task('twig', function() {
// 	'use strict';
// 	return gulp.src('app/twig/*.html.twig')
// 		   .pipe(twig())
// 		   .pipe(rename(function(path) {
// 		   		path.extname = ""
// 		   }))
// 		   .pipe(gulp.dest('app'))
// 		   .pipe(browserSync.reload({stream: true}))
// })

gulp.task('browser-sync', function() {
	browserSync({
		// server: {
		// 	baseDir: 'app'
		// }
		proxy: 'localhost:8000',
	});
});

gulp.task('watch', ['browser-sync', 'sass', 'scripts'], function() {
	gulp.watch('gulp/scss/*.scss', ['sass']);
	gulp.watch('gulp/js/*.js', ['scripts']);
	gulp.watch('src/App/**Bundle/Resources/views/**/*.html.twig', browserSync.reload);
});