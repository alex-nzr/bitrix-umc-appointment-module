module.exports = {
    input: 'src/scripts/index.js',
    output: 'dist/bx_popup.bundle.js',
    namespace: 'BX.FirstBit.Appointment',
    browserslist: false,
    minification: true,
    plugins: {
        resolve: true,
    },
};