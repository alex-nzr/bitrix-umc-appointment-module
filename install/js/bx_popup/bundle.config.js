const postcss = require('rollup-plugin-postcss');
const postCssUrl = require('postcss-url');

module.exports = {
    input: 'src/index.js',
    output: 'dist/classic_form.bundle.js',
    namespace: 'BX.Anz.Appointment',
    browserslist: false,
    minification: 0,
    sourceMaps: false,
    plugins: {
        resolve: true,
        custom: [
            postcss({
                minimize: true,
                modules: true,
                plugins: [
                    postCssUrl({
                        url: 'inline',
                    }),
                ],
            })
        ],
    },
};