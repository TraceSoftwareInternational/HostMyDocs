var webpack = require('webpack');
var webpackMerge = require('webpack-merge');
var commonConfig = require('./webpack.common.js');
var helpers = require('./helpers');

module.exports = webpackMerge(commonConfig, {
    output: {
        path: helpers.root('dist'),
        publicPath: '/',
        filename: '[name].[hash].js',
        chunkFilename: '[id].[hash].chunk.js'
    },

    plugins: [
        new webpack.LoaderOptionsPlugin({
            minimize: true,
            debug: false,
            htmlLoader: {
                minimize: true // workaround for ng2
            },
        }),
        // make build fail if there is any error
        new webpack.NoEmitOnErrorsPlugin(),
        new webpack.optimize.UglifyJsPlugin({

            beautify: false,
            mangle: {
                screw_ie8: true,
                // https://github.com/angular/angular/issues/10618
                keep_fnames: true
            },
            compress: {
                screw_ie8: true,
            },
            comments: false
        })
    ]
});
