const scss = require('rollup-plugin-scss');
const postcss = require('rollup-plugin-postcss');
const postCssUrl = require('postcss-url');

module.exports = {
    input: 'src/scripts/index.js',
    output: 'dist/bx_popup.bundle.js',
    namespace: 'BX.FirstBit.Appointment',
    browserslist: false,
    minification: 0,
    plugins: {
        resolve: true,
        custom: [
            scss({
                sourceMap: 1,
                outputStyle: 'compressed'
            }),
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