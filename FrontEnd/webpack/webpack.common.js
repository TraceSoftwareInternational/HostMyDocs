const webpack = require('webpack');
const path    = require("path");
const helpers = require('./helpers');

const CleanWebpackPlugin   = require('clean-webpack-plugin');
const { CheckerPlugin }    = require('awesome-typescript-loader');
const ExtractTextPlugin    = require("extract-text-webpack-plugin");
const HtmlWebpackPlugin    = require('html-webpack-plugin');
const TypedocWebpackPlugin = require('typedoc-webpack-plugin');
const CommonsChunkPlugin   = require('webpack/lib/optimize/CommonsChunkPlugin');


module.exports = {
    entry: {
        polyfills: helpers.root('src/ts/polyfills.ts'),
        main: helpers.root('src/ts/main.ts')
    },
    output: {
        filename: 'js/[chunkhash].[name].js',
        path: helpers.root('dist'),
        sourceMapFilename: '[name].map'
    },
    resolve: {
        extensions: ['.ts', '.js', '.html', '.sass'],
        alias: {
            clarityIconsShapes: helpers.root('node_modules/clarity-icons/shapes')
        }
    },
    module: {
        loaders: [
            {
                test: /\.ts$/,
                loaders: ['angular2-template-loader', 'awesome-typescript-loader']
            },
            {
                test: /\.html$/,
                loader: 'html-loader',
                options: {
                    minimize: true,
                    removeComments: true,
                    collapseWhitespace: true,

                    // angular 2 templates break if these are omitted
                    removeAttributeQuotes: false,
                    keepClosingSlash: true,
                    caseSensitive: true,
                    conservativeCollapse: true
                }
            },
            {
                test: /\.min.css$/,
                loader: ExtractTextPlugin.extract({
                    use: 'css-loader'
                })
            },
            {
                test: /\.sass$/,
                loaders: ['raw-loader', 'sass-loader']
            },
        ]
    },
    plugins: [
        // This enables to put imports from node_modules into a separated chunk
        new CommonsChunkPlugin({
            name: 'vendor',
            chunks: ['main'],
            minChunks: module => /node_modules/.test(module.resource)
        }),
        // extracting the CSS in it's own file
        new ExtractTextPlugin('css/[chunkhash].[name].css'),
        new HtmlWebpackPlugin({
            inject: 'body',
            template: helpers.root('src/index.html'),
            filename: helpers.root('dist/index.html'),
            chunksSortMode: 'dependency'
        }),
        // type checker plugin for TypeScript
        new CheckerPlugin(),
        // clean the dist folder before building
        new CleanWebpackPlugin(['dist'], {
            root: helpers.root('.'),
        }),
        // cf. https://github.com/angular/angular/issues/11580
        new webpack.ContextReplacementPlugin(
            // The (\\|\/) piece accounts for path separators in *nix and Windows
            /angular(\\|\/)core(\\|\/)(esm(\\|\/)src|src)(\\|\/)linker/,
            helpers.root('src'), {}
        ),
        new TypedocWebpackPlugin({
            name: 'HostMyDocs',
            target: 'es6',
            mode: 'file',
            exclude: helpers.root('src/**/*.spec.ts'),
            out: helpers.root('dist/docs')
        }, helpers.root('src/ts'))
    ]
};
