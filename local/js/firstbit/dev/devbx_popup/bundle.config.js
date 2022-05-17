const scss = require('rollup-plugin-scss');
const postcss = require('rollup-plugin-postcss');
const postCssUrl = require('postcss-url');

module.exports = {
    input: 'src/scripts/index.js',
    output: 'dist/bx_popup.bundle.js',
    namespace: 'BX.FirstBit.Appointment',
    browserslist: false,
    minification: true,
    plugins: {
        resolve: true,
        custom: [
            scss({
                sourceMap: true,
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