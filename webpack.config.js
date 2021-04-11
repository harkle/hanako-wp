const fs = require('fs');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const EventHooksPlugin = require('event-hooks-webpack-plugin');

let basePath = './wp-content/themes/hanako-wp/';

try {
  fs.rmdirSync(basePath + '/dist', { recursive: true });
} catch (err) {
  console.error('Error while deleting /dist');
}

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
        exclude: /node_modules/,
        use: {
          loader: 'ts-loader',
          options: {
            transpileOnly: false
          }
        }
      },
      {
        test: /\.(scss|css)$/,
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
      }, {
        test: /\.(png|jpe?g|gif|svg)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name].[ext]',
              publicPath: '../assets/images',
              outputPath: 'assets/images',
              esModule: false,
            }
          }
        ]
      }, {
        test: /\.(woff|woff2|ttf|otf|eot)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name].[ext]',
              publicPath: '../assets/fonts',
              outputPath: 'assets/fonts',
              esModule: false
            }
          }
        ]
      }
    ]
  }
};

let configs = [];
console.log('> Entry points');
['src/ts/', 'src/scss/'].forEach((subFolder) => {
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
            path: path.resolve(__dirname, basePath + '/dist')
          },
          plugins: [
            new MiniCssExtractPlugin({
              filename: 'css/' + filename + '.min.css',
            }),
            new HtmlWebpackPlugin({
              template: './wp-content/themes/hanako-wp/src/index.html'
            }),
            new CopyPlugin({
              patterns: [{ from: 'wp-content/themes/hanako-wp/src/assets', to: 'assets' }]
            })
          ]
        }
      });
    }
  });
});

var data = new Date();
configs.forEach((config) => {
  if (config.plugins) {
    config.plugins.push(new EventHooksPlugin({
      'invalid': () => {
        console.log('\r\n');
        console.log('> webpack recompile: ' + (new Date()).toLocaleTimeString());
      },
      'done': () => {
        fs.readdirSync(basePath + '/dist').forEach(element => {
          if (fs.lstatSync(basePath + '/dist/' + element).isFile() && element != 'index.html') fs.unlinkSync(basePath + '/dist/' + element);
        });
      }
    }));
  }
});

module.exports = configs;
