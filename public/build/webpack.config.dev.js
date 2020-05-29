'use strict'

const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const path = require('path');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const resolve = require('path').resolve;

const moduleConf = require('../../modules/__config');

var modulesEntries = ['babel-polyfill'];

modulesEntries = modulesEntries.concat(moduleConf.getModulesFiles());
modulesEntries.push('./src/app.js');


var moduleResolve = moduleConf.getModulesDirs();
moduleResolve = moduleResolve.concat([ path.resolve('./node_modules'), path.resolve('./src')]);

var resourcesDirs = moduleConf.getResourcesDirs();
resourcesDirs.push({from:'src/resources',to:'resources'});
resourcesDirs.push({from: 'config.sample.js', to: 'config.sample.js'});
resourcesDirs.push({from: '.htaccess', to: '.htaccess'});


module.exports = {
    mode: 'development',
    entry: modulesEntries,
    output: {
        filename: './dist/main.js',
        path: resolve('./dist'),
        publicPath: '/'

    },
    devServer: {
        hot: true,
        watchOptions: {
            poll: true
        },
        historyApiFallback: true
    },
    optimization: {
        minimize: true,
        minimizer: [new UglifyJsPlugin({
            include: /\.js$/
        })]
    },
    module: {
        rules: [
            {
                test: /\.js$/, //Regular expression
                exclude: /(node_modules|bower_components)/,//excluded node_modules
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@babel/preset-env"]  //Preset used for env setup
                    }
                }
            },
            {
                test: /\.vue$/,
                use: 'vue-loader'
            },
            {
                test: /\.css$/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ]
            },
            {
                test: /\.scss$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: 'fonts/'
                        }
                    }
                ]
            }
        ]
    },
    resolve : {
        alias: {
            'vue$': 'vue/dist/vue.esm.js'
        },
        extensions: ['*', '.js', '.vue', '.json'],
        modules : moduleResolve
    },
    node: {
        dns: 'mock',
        net: 'mock'
    },
    plugins: [
        new webpack.HotModuleReplacementPlugin(),
        new VueLoaderPlugin(),
        new HtmlWebpackPlugin({
            filename: 'index.html',
            template: 'index.html',
            inject: true
        }),
        new CopyWebpackPlugin(resourcesDirs)


    ]
}