const fs = require('fs');
const path = require('path');
const glob = require('glob');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const EventHooksPlugin = require('event-hooks-webpack-plugin');

const version = '1';

function extractLicenceComments(file) {
  const content = fs.readFileSync(file, 'utf8');
  const comments = content.match(/\/\*![^*]*\*+([^\/*][^*]*\*+)*\//g);
  const licenseComments = [];

  if (comments) {
    comments.forEach(comment => {
      licenseComments.push(comment);
    });

    // write licence comments to a separate file
    fs.writeFileSync(`${file}.LICENSE.txt`, licenseComments.join('\r\n'));

    // remove comments from css file
    fs.writeFileSync(file, content.replace(/\/\*![^*]*\*+([^\/*][^*]*\*+)*\//g, ''));
  }
}

module.exports = {
  mode: 'production',
  devtool: 'source-map',
  watch: true,
  stats: 'errors-only',
  performance: {
    hints: false,
  },
  optimization: {
    usedExports: true,
  },
  cache: { type: 'filesystem' },
  entry: {
    ...Object.fromEntries(
      glob.sync('./views/ts/*.ts').map(file => [
        `js/${path.relative('./views/ts', file).replace(/\\/g, '/').replace(/\\.ts$/, '')}`,
        `./${file}`
      ])
    ),
    ...Object.fromEntries(
      glob.sync('./views/scss/[^_]*.scss').map(file => [
        `css/${path.relative('./views/scss', file).replace(/\\/g, '/').replace(/\\.scss$/, '')}`,
        `./${file}`
      ])
    )
  },
  output: {
    filename: (pathData) => {
      const ext = path.extname(pathData.chunk.name);
      const name = pathData.chunk.name.replace(/\.(scss|ts)$/, '');
      return ext === '.ts' ? `${name}-v${version}.min.js` : `${name}-v${version}.min.js`;
    },
    path: path.resolve(__dirname, 'dist'),
    clean: true,
  },
  module: {
    rules: [
      {
        test: /\.ts$/,
        use: 'ts-loader',
        exclude: /node_modules/,
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              url: false,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: [
                  require('autoprefixer')(),
                ],
              },
            },
          },
          'sass-loader',
        ],
      },
    ],
  },
  resolve: {
    extensions: ['.ts', '.js'],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: `[name].css`,
    }),
    new CopyWebpackPlugin({
      patterns: [
        {
          from: 'views/assets',
          to: 'assets',
          noErrorOnMissing: true,
        },
      ],
    }),
    new EventHooksPlugin({
      'invalid': () => {
        console.log('\r\n');
        console.log(`> recompile: ${(new Date()).toLocaleTimeString()}`);
      },
      'done': (stats) => {
        const time = stats.compilation.endTime - stats.compilation.startTime;

        console.log(`> \x1b[32m${time}ms\x1b[0m`);
        Object.entries(stats.compilation.assets).forEach(asset => {

          try {
            // if path contain css but file has .js extension, remove it
            if (path.extname(asset[0]) == '.js' && asset[0].includes('css')) fs.unlinkSync(path.resolve(__dirname, 'dist', asset[0]));

            // if file has .scss.css.map extension, remove it
            if (path.extname(asset[0]) == '.map' && asset[0].includes('.scss.css')) fs.unlinkSync(path.resolve(__dirname, 'dist', asset[0]));

            // if file has .scss.css extension, rename it to .css
            if (path.extname(asset[0]) == '.css' && asset[0].includes('.scss.css')) fs.renameSync(path.resolve(__dirname, 'dist', asset[0]), path.resolve(__dirname, 'dist', asset[0].replace('.scss.css', `-v${version}.min.css`)));

            // if file has .css extension, call extractLicenceComments on previously renamed file
            if (path.extname(asset[0]) == '.css') extractLicenceComments(path.resolve(__dirname, 'dist', asset[0].replace('.scss.css', `-v${version}.min.css`)));
          } catch (e) { }
        });
      }
    })
  ],
  optimization: {
    minimize: true,
    minimizer: [
      new TerserPlugin(),
      new CssMinimizerPlugin({
        minimizerOptions: {
          preset: ['default', { discardComments: { removeAll: false } }],
        },
      }),
    ],
  },
};
