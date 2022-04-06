const fs = require('fs');
const path = require('path');

const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const EventHooksPlugin = require('event-hooks-webpack-plugin');

let basePath = './';
let exclude = [];

let configBase = {
  mode: 'production',
  devtool: 'source-map',
  watch: true,
  stats: "minimal",
  performance: {
    hints: false,
  },
  optimization: {
    usedExports: true
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js']
  },
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        exclude: exclude,
        use: {
          loader: 'ts-loader',
          options: {
            transpileOnly: false
          }
        }
      }, {
        test: /\.(png|jpe?g|gif|svg)$/,
        exclude: exclude,
        type: 'asset/resource'
      }, {
        test: /\.(woff|woff2|ttf|otf|eot)$/,
        exclude: exclude,
        type: 'asset/resource'
      },
      {
        test: /\.(scss|css)$/,
        exclude: exclude,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: "postcss-loader",
            options: {
              postcssOptions: {
                plugins: function () {
                  return [
                    require('autoprefixer')
                  ];
                }
              }
            }
          }, {
            loader: 'sass-loader',
            options: {
              sourceMap: true
            }
          }
        ]
      }
    ]
  }
};

let configs = [];
console.log('> Entry points');
['views/ts/', 'views/scss/'].forEach((subFolder) => {
  fs.readdirSync(basePath + subFolder).forEach(element => {
    const extension = path.extname(element);
    const filename = element.replace(extension, '');

    if (extension == '.ts') {
      console.log(element);

      configs.push({
        ...configBase, ...{
          entry: basePath + subFolder + element,
          output: {
            filename: 'js/' + filename + '.min.js',
            path: path.resolve(__dirname, basePath + '/dist')
          },
          plugins: [
            new ForkTsCheckerWebpackPlugin(),
          ]
        }
      });
    }

    if (extension == '.scss' && filename[0] != '_') {
      console.log(element);

      configs.push({
        ...configBase, ...{
          entry: basePath + subFolder + element,
          output: {
            filename: filename + '.min.js',
            path: path.resolve(__dirname, basePath + '/dist'),
            assetModuleFilename: 'assets/[hash][ext][query]'
          },
          plugins: [
            new MiniCssExtractPlugin({
              filename: 'css/' + filename + '.min.css',
            }),
            new CopyPlugin({
              patterns: [{ from: 'views/assets/images', to: 'assets/images' }]
            })
          ]
        }
      });
    }
  });
});

var invalidationDate = new Date();
configs.forEach((config) => {
  if (config.plugins) {
    config.plugins.push(new EventHooksPlugin({
      'invalid': () => {
        console.log('\r\n');
        invalidationDate = new Date();
        console.log('> webpack recompile: ' + invalidationDate.toLocaleTimeString());
      },
      'done': () => {
        let date = new Date();
        let timeDifference = date.getTime() - invalidationDate.getTime();
        console.log('> compiled in: ' + timeDifference + 'ms');
        try {
          fs.readdirSync(basePath + 'dist/').forEach(element => {
            if (fs.lstatSync(basePath + 'dist/' + element).isFile() && element != 'index.html') fs.unlinkSync(basePath + 'dist/' + element);
          });
        } catch (err) {
        }
      }
    }));
  }
});

module.exports = configs;
