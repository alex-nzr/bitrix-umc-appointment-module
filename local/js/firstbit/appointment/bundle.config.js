module.exports = {
	input: 'src/appointment.js',
	output: {js: 'dist/appointment.bundle.js', css: 'dist/appointment.bundle.css'},
	namespace: 'BX.Firstbit',
	//browserslist: true,
	minification: true,
	plugins: {
		resolve: true,
	},
	sourceMaps: false,
};