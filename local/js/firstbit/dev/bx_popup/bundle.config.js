/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - bundle.config.js
 * 10.07.2022 22:37
 * ==================================================
 */

const scss = require('rollup-plugin-scss');
const postcss = require('rollup-plugin-postcss');
const postCssUrl = require('postcss-url');

module.exports = {
    input: 'src/scripts/index.js',
    output: 'dist/bx_popup.bundle.js',
    namespace: 'BX.FirstBit.Appointment',
    browserslist: false,
    minification: 0,
    sourceMaps: false,
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