
module.exports = {
    input: 'src/admin.js',
    output: 'dist/admin.bundle.js',
    namespace: 'BX.Anz.Appointment',
    browserslist: false,
    minification: true,
    plugins: {
        resolve: true,
    },
};