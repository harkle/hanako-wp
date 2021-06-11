const fs = require('fs');
const chokidar = require('chokidar');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config.js');
const basePath = './';

let isCompilerStarted = false;
let changeDebounce;

try {
  fs.rmdirSync(basePath + 'dist', { recursive: true });
} catch (err) {
  console.error('> Error while deleting dist/');
}

function buildImports() {
  let includes = {
    'scss': [],
    'ts': []
  }

  let paths = [
    'views/twig/components/',
    'views/twig/modules/',
    'views/twig/pages/archives/',
    'views/twig/pages/root/',
    'views/twig/pages/singles/',
    'views/twig/pages/templates/',
    'views/twig/partials/',
  ]

  const regex = /(export class )([A-Za-z]+)( extends)/;

  paths.forEach((path) => {
    fs.readdirSync(basePath + path).forEach(componentFolder => {
      if (fs.existsSync(basePath + path + componentFolder + '/index.scss')) {
        includes.scss.push({ path: path + componentFolder, name: componentFolder });
      }

      if (fs.existsSync(basePath + path + componentFolder + '/index.ts')) {
        const fileContent = fs.readFileSync(basePath + path + componentFolder + '/index.ts', { encoding: 'utf8', flag: 'r' });
        const found = fileContent.match(regex);
        const className = (found) ? found[2] : ''

        if (className) {
          includes.ts.push({ path: path + componentFolder, className: className });
        }
      }
    });
  });

  let scssCode = '';
  includes.scss.forEach(component => {
    scssCode += '@import \'../../' + component.path + '/index.scss\';\n';
  });

  fs.writeFile(basePath + '/views/scss/_components.scss', scssCode, function (err) { });

  let tsImportsCode = '';
  let tsInitCode = '';
  includes.ts.forEach(component => {
    tsImportsCode += 'import { ' + component.className + ' } from \'../../' + component.path + '\';\n';
    tsInitCode += '(new ' + component.className + '()).init();\n';
  });

  fs.writeFile(basePath + '/views/ts/site.ts', tsImportsCode + tsInitCode, function (err) { });

  startCompiler();
}

function startCompiler() {
  if (isCompilerStarted) return;

  const compiler = webpack(webpackConfig);

  compiler.watch({}, (err, stats) => {
    if (err) console.log(err);
    if (stats) console.log(stats.toString('minimal'));
  });

}

function requestCompile(delay, path) {
  clearTimeout(changeDebounce);
  changeDebounce = setTimeout(() => {
    console.log(path);
    console.log('> Refresh module list' + ((path) ? ' (' + path +')' : ''));

    buildImports();
  }, delay);
}

const watcher = chokidar.watch('views', {
  ignored: ['*.twig', '*.svg', '*.jpg', '*.png', '*.woff', '*.woff2', '*.otf', '*.ttf'],
  persistent: true
});

watcher.on('add', (event, path) => {
  requestCompile(30000, event);
});

watcher.on('unlink', (event, path) => {
  requestCompile(2500, event);
});

requestCompile(0, '');

