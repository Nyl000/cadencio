'use strict'

const webpack = require('webpack')
const { VueLoaderPlugin } = require('vue-loader')
const HtmlWebpackPlugin = require('html-webpack-plugin')
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
resourcesDirs.push({from: 'config.js', to: 'config.js'});
resourcesDirs.push({from: '.htaccess'});


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
            aggregateTimeout: 200,
            poll: 1000,
            ignored: /node_modules/

        },
        historyApiFallback: true
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